<?php
// Shared layout + admin helpers

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin token comes from environment (Render > Environment > ADMIN_TOKEN)
$ADMIN_TOKEN = getenv('ADMIN_TOKEN') ?: 'my-super-secret-123';

function admin_logged_in(): bool {
    return !empty($_SESSION['is_admin']);
}

function require_admin(): void {
    if (!admin_logged_in()) {
        header('Location: /admin');
        exit;
    }
}

function render_header(string $title = 'Attorney Directory'): void {
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
  <nav class="bg-slate-900 border-b border-slate-800">
    <div class="max-w-5xl mx-auto flex items-center justify-between px-4 py-3">
      <a href="/" class="flex items-center gap-2 text-slate-100 font-semibold">
        <span class="text-xl">⚖️</span>
        <span>Attorney Directory</span>
      </a>
      <div class="flex items-center gap-4 text-sm">
        <a href="/" class="hover:text-emerald-400">Home</a>

        <?php if (admin_logged_in()): ?>
          <a href="/items" class="hover:text-emerald-400">All Items</a>
          <a href="/add" class="hover:text-emerald-400">Add new</a>
          <a href="/admin/logout" class="text-slate-400 hover:text-red-400">Log out</a>
        <?php else: ?>
          <a href="/public" class="hover:text-emerald-400">Public Directory</a>
          <a href="/admin" class="text-slate-400 hover:text-emerald-400">Admin</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main class="max-w-5xl mx-auto px-4 py-8">
<?php
}

function render_footer(): void {
    ?>
  </main>
  <footer class="border-t border-slate-800 py-6 mt-8 text-center text-xs text-slate-500">
    &copy; <?= date('Y') ?> Attorney Directory. All rights reserved.
  </footer>
</body>
</html>
<?php
}
