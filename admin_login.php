<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/includes.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $expected = getenv('ADMIN_PASSWORD') ?: 'changeme';

    if ($password !== '' && hash_equals($expected, $password)) {
        $_SESSION['is_admin'] = true;
        header('Location: /items');
        exit;
    } else {
        $error = 'Invalid password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Login – Attorney Directory</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/styles.css" />
</head>
<body class="bg-slate-950 text-slate-100">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-slate-900/70 border border-slate-800 rounded-2xl shadow-xl p-8">
      <h1 class="text-2xl font-semibold text-slate-50 mb-6 text-center">Admin Login</h1>

      <?php if ($error): ?>
        <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-2 text-sm text-red-100">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-200 mb-1" for="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            required
            class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/40"
          />
        </div>

        <button
          type="submit"
          class="inline-flex w-full items-center justify-center rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white shadow hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/50"
        >
          Log in
        </button>
      </form>

      <p class="mt-6 text-center text-xs text-slate-400">
        Tip: set an <code>ADMIN_PASSWORD</code> environment variable in Render.
      </p>
    </div>
  </div>
</body>
</html>
