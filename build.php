<?php
/**
 * Vuemax — static build
 * Renders each .php page to a .html twin (for local file:// browsing).
 * Page links inside the output are rewritten .php -> .html.
 * API paths (api/*.php) and assets are left untouched.
 *
 * Run:  php build.php
 */
$root  = __DIR__;
$pages = ['index','about','products','product-detail','calculator','estimator',
          'installations','faq','contact','quote-results','404'];

$pageRe = '/(?<!\/)\b(' . implode('|', $pages) . ')\.php\b/';

foreach ($pages as $p) {
    $src = $root . DIRECTORY_SEPARATOR . $p . '.php';
    if (!file_exists($src)) { echo "skip $p (missing)\n"; continue; }

    $html = shell_exec(PHP_BINARY . ' ' . escapeshellarg($src) . ' 2>NUL');
    if ($html === null || $html === '') { echo "FAIL $p\n"; continue; }

    // Mask API references so they keep .php
    $html = preg_replace_callback(
        '/(?:api\/[a-z-]+\.php|API \+ \'[a-z-]+\.php)/',
        function ($m) { return str_replace('.php', '##PHP##', $m[0]); },
        $html
    );

    $html = preg_replace($pageRe, '$1.html', $html);
    $html = str_replace('##PHP##', '.php', $html);

    file_put_contents($root . DIRECTORY_SEPARATOR . $p . '.html', $html);
    echo "built $p.html\n";
}
echo "Done.\n";
