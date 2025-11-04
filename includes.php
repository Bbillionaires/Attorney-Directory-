<?php

// Start session for admin login, etc.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Make sure we have the DB connection (only once)
require_once __DIR__ . '/conn.php';

/**
 * Return the global PDO or null if not connected.
 */
if (!function_exists('pdo_or_null')) {
    function pdo_or_null(): ?PDO
    {
        return $GLOBALS['pdo'] ?? null;
    }
}

/**
 * Simple admin check using a session flag.
 */
function is_admin(): bool
{
    return !empty($_SESSION['is_admin']);
}

/**
 * Require admin for protected pages.
 * Redirects to /admin if not logged in.
 */
function require_admin(): void
{
    if (!is_admin()) {
        header('Location: /admin');
        exit;
    }
}

/**
 * Common page header + nav.
 */
function render_header(string $title = 'Attorney Directory', string $active = ''): void
{
    ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/styles.css" />
</head>
<body class="bg-slate-950 text-slate-100">
<header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur">
  <div class="mx-auto max-w-5xl px-4 py-4 flex items-center justify-between">
    <a href="/" class="text-lg font-semibold tracking-tight">
      <span class="text-sky-400">Attorney</span> Directory
    </a>
    <nav class="flex items-center gap-4 text-sm">
      <a href="/items"
         class="<?= $active === 'items' ? 'text-sky-400 font-medium' : 'text-slate-300 hover:text-white' ?>">
        All Items
      </a>
      <a href="/add"
         class="<?= $active === 'add' ? 'text-sky-400 font-medium' : 'text-slate-300 hover:text-white' ?>">
        Add new
      </a>
      <a href="/public"
         class="<?= $active === 'public' ? 'text-sky-400 font-medium' : 'text-slate-300 hover:text-white' ?>">
        Public Directory
      </a>
    </nav>
  </div>
</header>
<main class="mx-auto max-w-5xl px-4 py-8 space-y-8">
<?php
}

/**
 * Common footer.
 */
function render_footer(): void
{
    ?>
</main>
<footer class="border-t border-slate-800 bg-slate-900/70 mt-12">
  <div class="mx-auto max-w-5xl px-4 py-6 text-xs text-slate-400 flex items-center justify-between">
    <p>&copy; <?= date('Y') ?> Attorney Directory. All rights reserved.</p>
    <p class="space-x-3">
      <a href="/" class="hover:text-sky-400">Home</a>
      <a href="/public" class="hover:text-sky-400">Public Directory</a>
    </p>
  </div>
</footer>
</body>
</html>
<?php
}
