<?php
require __DIR__ . '/includes.php';

global $ADMIN_TOKEN;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    if (hash_equals($ADMIN_TOKEN, $token)) {
        $_SESSION['is_admin'] = true;
        header('Location: /items');
        exit;
    } else {
        $errors[] = 'Invalid admin token.';
    }
}

render_header('Admin Login');
?>
<h1 class="text-2xl font-semibold mb-6">Admin Login</h1>

<?php if ($errors): ?>
  <div class="mb-4 rounded-lg bg-red-950/60 border border-red-700 text-red-200 px-4 py-3 text-sm">
    <?= htmlspecialchars(implode(' ', $errors)) ?>
  </div>
<?php endif; ?>

<form method="post" class="max-w-md space-y-4">
  <label class="block text-sm font-medium text-slate-200">
    Admin token
    <input
      type="password"
      name="token"
      class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
      autocomplete="current-password"
      required
    />
  </label>

  <button
    type="submit"
    class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400"
  >
    Sign in
  </button>
</form>
<?php
render_footer();
