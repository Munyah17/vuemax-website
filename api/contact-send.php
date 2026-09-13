<?php
/**
 * ============================================================
 * VUEMAX API — POST contact form submission
 * ============================================================
 * File: /api/contact-send.php
 *
 * Accepts JSON body (or form-encoded) from contact.html,
 * validates the fields, inserts into `contacts`, and emails a
 * notification to the sales inbox.
 *
 * Request body:
 * {
 *   "name": "John Moyo",
 *   "phone": "784 000 000",
 *   "email": "john@example.com",      // optional
 *   "subject": "quote",               // quote|product|installation|bulk|support|other
 *   "message": "I need a quote for...",
 *   "consent": true
 * }
 *
 * Response:
 * {
 *   "ok": true,
 *   "id": 42,
 *   "message": "Thanks, we'll be in touch within 24 hours."
 * }
 * ============================================================
 */

require_once __DIR__ . '/config.php';
require_db($pdo, $db_error);
require_method('POST');

/* ---------- Read body ---------- */
$body = read_json_body();

if (!is_array($body) || empty($body)) {
    json_error('Empty or invalid request body.', 400);
}

/* ---------- Extract ---------- */
$name    = clean_str(g($body, 'name', ''),    160);
$phone   = clean_str(g($body, 'phone', ''),   60);
$email   = clean_str(g($body, 'email', ''),   160);
$subject = clean_str(g($body, 'subject', ''), 80);
$message = clean_str(g($body, 'message', ''), 5000);
$consent = !empty(g($body, 'consent', false));

/* ---------- Validate ---------- */
$errors = [];

if ($name === '')    $errors['name']    = 'Name is required.';
if ($phone === '')   $errors['phone']   = 'Phone number is required.';
if ($message === '') $errors['message'] = 'Message is required.';
if (mb_strlen($message) < 10) $errors['message'] = 'Message is too short.';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Email address is not valid.';
}

/* Whitelist subjects */
$allowed_subjects = ['quote', 'product', 'installation', 'bulk', 'support', 'other'];
if ($subject === '' || !in_array($subject, $allowed_subjects, true)) {
    $subject = 'other';
}

if (!$consent) {
    $errors['consent'] = 'Please agree to be contacted so we can respond.';
}

if (!empty($errors)) {
    json_error('Please correct the highlighted fields.', 422, ['fields' => $errors]);
}

/* ---------- Rate limit (simple, per IP, per hour) ----------
   Prevents spam floods — max 5 submissions per hour from one IP. */

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM contacts
        WHERE ip_address = :ip
          AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute([':ip' => $ip]);
    if ((int) $stmt->fetchColumn() >= 5) {
        json_error('Too many submissions. Please try again later.', 429);
    }
} catch (PDOException $e) {
    /* If the rate-limit check fails, don't block the submission — log and continue. */
}

/* ---------- Save to DB ---------- */
$user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);

try {
    $stmt = $pdo->prepare("
        INSERT INTO contacts
            (name, phone, email, subject, message, status, ip_address, user_agent)
        VALUES
            (:name, :phone, :email, :subject, :message, 'new', :ip, :ua)
    ");
    $stmt->execute([
        ':name'    => $name,
        ':phone'   => $phone,
        ':email'   => $email ?: null,
        ':subject' => $subject,
        ':message' => $message,
        ':ip'      => $ip,
        ':ua'      => $user_agent,
    ]);
    $contact_id = (int) $pdo->lastInsertId();
} catch (PDOException $e) {
    json_error('Failed to save your message.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Send notification email ----------
   Uses PHP mail() by default. On cPanel, mail() works out of the box.
   If you need SMTP later (Gmail, SendGrid), swap this block for PHPMailer. */

$subject_labels = [
    'quote'        => 'Request a Quote',
    'product'      => 'Product Information',
    'installation' => 'Installation Enquiry',
    'bulk'         => 'Bulk Order / Wholesale',
    'support'      => 'Existing Order Support',
    'other'        => 'General Enquiry',
];

$email_subject = '[Vuemax Contact] ' . ($subject_labels[$subject] ?? 'New message') . ' — ' . $name;

$email_body  = "New contact form submission on vuemax.co.zw\n";
$email_body .= str_repeat('=', 60) . "\n\n";
$email_body .= "Reference: #{$contact_id}\n";
$email_body .= "Received:  " . date('Y-m-d H:i:s') . "\n\n";
$email_body .= "Name:     {$name}\n";
$email_body .= "Phone:    {$phone}\n";
if ($email) $email_body .= "Email:    {$email}\n";
$email_body .= "Subject:  " . ($subject_labels[$subject] ?? $subject) . "\n";
$email_body .= "IP:       {$ip}\n\n";
$email_body .= "Message:\n";
$email_body .= str_repeat('-', 60) . "\n";
$email_body .= $message . "\n";
$email_body .= str_repeat('-', 60) . "\n\n";
$email_body .= "View in admin panel (once available): /admin/contacts.php?id={$contact_id}\n";

$headers  = "From: Vuemax Website <no-reply@vuemax.co.zw>\r\n";
$headers .= "Reply-To: " . ($email ?: SITE_EMAIL) . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

/* mail() returns false on failure but we don't want to fail the whole
   request just because email delivery failed — the DB record is the
   source of truth. Log the failure in the response for debugging. */
$mail_sent = false;
$mail_error = null;

if (function_exists('mail')) {
    try {
        // Suppress PHP warnings that mail() sometimes emits on shared hosts
        $mail_sent = @mail(SITE_EMAIL, $email_subject, $email_body, $headers);
        if (!$mail_sent) {
            $mail_error = 'mail() returned false. Check cPanel email configuration.';
        }
    } catch (Throwable $e) {
        $mail_error = $e->getMessage();
    }
} else {
    $mail_error = 'PHP mail() not available on this host.';
}

/* ---------- Respond ---------- */
$response = [
    'ok'      => true,
    'id'      => $contact_id,
    'message' => "Thanks {$name}, we've received your message and will respond within 24 hours.",
];

// Include mail status only when debugging or when mail failed
if (DEBUG || !$mail_sent) {
    $response['mail_sent']  = $mail_sent;
    if ($mail_error) $response['mail_error'] = $mail_error;
}

json_response($response);