<?php
session_start();
require __DIR__ . '/includes.php';

$admin_token_env = getenv('ADMIN_TOKEN') ?: 'changeme';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = trim($_POST['token'] ?? '');
  if (hash_equals($admin_token_env, $token)) {
    $_SESSION['is_admin'] = true;
    header('Location: /items');
    exit;
  } else {
    $error = "Invalid token.";
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 flex items-center justify-center min-h-screen">
  <form method="post" class="bg-slate-800 p-8 rounded-lg shadow-lg w-96">
    <h1 class="text-xl font-semibold mb-4 text-center">Admin Login</h1>
    <?php if (!empty($error)): ?>
      <p class="text-red-400 mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <input type="password" name="token" placeholder="Enter admin token" class="w-full mb-4 p-2 rounded bg-slate-700 text-white">
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 p-2 rounded text-white">Login</button>
  </form>
</body>
</html>
