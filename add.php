<?php
require __DIR__ . '/conn.php';          // defines $pdo
require __DIR__ . '/includes.php';
require_admin();      // common include (nav, etc.)

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name   = trim($_POST['itemname']  ?? '');
  $desc   = trim($_POST['itemdesc']  ?? '');
  $price  = trim($_POST['itemprice'] ?? '0');
  $thumb  = trim($_POST['itemthumb'] ?? '');
  $active = isset($_POST['active']) ? 1 : 0;

  if ($name === '')        { $errors[] = 'Name is required.'; }
  if ($price === '')       { $price = '0'; }
  if (!is_numeric($price)) { $errors[] = 'Price must be a number (e.g., 9.99).'; }

  if (!$errors) {
    try {
      $stmt = $pdo->prepare("
        INSERT INTO dd_catalog (itemname, itemdesc, itemprice, itemthumb, active)
        VALUES (:n, :d, :p, :t, :a)
        RETURNING itemid
      ");
      $stmt->execute([
        ':n' => $name,
        ':d' => $desc,
        ':p' => (float)$price,
        ':t' => $thumb ?: null,
        ':a' => $active,
      ]);
      header('Location: /public');
      exit;
    } catch (Throwable $e) {
      $errors[] = 'DB error: ' . $e->getMessage();
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Add Item · Attorney Directory</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/styles.css" />
</head>
<body class="bg-slate-950 text-slate-100">
<?php @include __DIR__ . '/templates/HeaderTemplate.php'; ?>

<main class="max-w-4xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-semibold mb-6">Add Item</h1>

  <?php if (!empty($errors)): ?>
    <div class="mb-6 rounded-xl bg-red-900/40 border border-red-700 text-red-200 p-4">
      <ul class="list-disc ml-5">
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="bg-white text-slate-900 rounded-2xl p-6 shadow-xl space-y-5">
    <div>
      <label for="itemname" class="block text-sm font-medium text-slate-700 mb-1">Name</label>
      <input id="itemname" name="itemname" type="text" required
             placeholder="e.g., Service Agreement"
             value="<?= htmlspecialchars($_POST['itemname'] ?? '') ?>"
             class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </div>

    <div>
      <label for="itemdesc" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
      <textarea id="itemdesc" name="itemdesc" rows="6"
                placeholder="Brief summary of the template..."
                class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"><?= htmlspecialchars($_POST['itemdesc'] ?? '') ?></textarea>
    </div>

    <div>
      <label for="itemprice" class="block text-sm font-medium text-slate-700 mb-1">Price (USD)</label>
      <input id="itemprice" name="itemprice" type="number" step="0.01" min="0"
             placeholder="9.99"
             value="<?= htmlspecialchars($_POST['itemprice'] ?? '') ?>"
             class="w-40 rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500" />
      <p class="text-xs text-slate-500 mt-1">Use 0.00 for free items.</p>
    </div>

    <div>
      <label for="itemthumb" class="block text-sm font-medium text-slate-700 mb-1">Image URL (optional)</label>
      <input id="itemthumb" name="itemthumb" type="url"
             placeholder="https://…"
             value="<?= htmlspecialchars($_POST['itemthumb'] ?? '') ?>"
             class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </div>

    <div class="flex items-center gap-2">
      <input id="active" name="active" type="checkbox"
             class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
             <?= isset($_POST['active']) ? 'checked' : 'checked' ?> />
      <label for="active" class="text-sm text-slate-700">Active</label>
    </div>

    <div class="pt-2 flex gap-3">
      <button type="submit"
              class="inline-flex items-center rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2">
        Save
      </button>
      <a href="/items"
         class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">
        Cancel
      </a>
    </div>
  </form>
</main>
</body>
</html>
