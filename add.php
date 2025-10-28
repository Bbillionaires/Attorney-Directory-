<?php
require_once "includes.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Directory Entry</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <h1>Attorney Directory</h1>
  <nav>
    <a href="/list.php" class="btn">All Entries</a>
    <a href="/" class="btn secondary">Home</a>
  </nav>
</header>

<main class="container">
  <h2>Add New Record</h2>
  <form method="POST" action="">
    <label>Name:</label><br>
    <input type="text" name="itemname" required><br><br>

    <label>Description:</label><br>
    <textarea name="itemdesc"></textarea><br><br>

    <label>Price:</label><br>
    <input type="number" name="itemprice" step="0.01" value="0.00"><br><br>

    <button type="submit" class="btn">Save</button>
  </form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['itemname'];
    $desc = $_POST['itemdesc'];
    $price = floatval($_POST['itemprice']);

    $stmt = $pdo->prepare("INSERT INTO dd_catalog (itemname, itemdesc, itemprice) VALUES (?, ?, ?)");
    $stmt->execute([$name, $desc, $price]);

    echo "<p>✅ Entry added successfully! <a href='list.php'>View List</a></p>";
}
?>

</main>
<footer class="site-footer">
  &copy; <?= date('Y') ?> Attorney Directory
</footer>
</body>
</html>
