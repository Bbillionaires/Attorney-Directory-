<?php
// Keep your existing includes (they set up PDO and any helpers)
require_once __DIR__ . "/conn.php";
require_once __DIR__ . "/includes.php";

// If $pdo isn't set by includes.php, try DATABASE_URL as a fallback.
if (!isset($pdo) || !($pdo instanceof PDO)) {
    $url = getenv('DATABASE_URL');
    if ($url) {
        $parts = parse_url($url);
        $host  = $parts['host'] ?? 'localhost';
        $port  = $parts['port'] ?? '5432';
        $user  = $parts['user'] ?? '';
        $pass  = $parts['pass'] ?? '';
        $db    = ltrim($parts['path'] ?? '', '/');
        $dsn   = "pgsql:host={$host};port={$port};dbname={$db};sslmode=require";
        $pdo   = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}

// Fetch one random catalog item (PostgreSQL uses RANDOM(), not RAND())
$item = null;
try {
    $sql = "
        SELECT c.itemid, c.itemname, c.itemdesc, c.itemprice, c.itemthumb, c.itemcategory,
               cat.categoryname
        FROM dd_catalog AS c
        JOIN dd_categories AS cat ON c.itemcategory = cat.categoryid
        ORDER BY RANDOM()
        LIMIT 1
    ";
    $stmt = $pdo->query($sql);
    $item = $stmt ? $stmt->fetch() : null;
} catch (Throwable $e) {
    error_log('Index query failed: ' . $e->getMessage());
}

// Safely include templates if they exist; otherwise render a minimal page
$tplHeader = __DIR__ . "/templates/HeaderTemplate.php";
$tplIndex  = __DIR__ . "/templates/IndexTemplate.php";
$tplFooter = __DIR__ . "/templates/FooterTemplate.php";

if (is_file($tplHeader)) include_once $tplHeader;

// If your IndexTemplate expects $item, it’s now available.
if (is_file($tplIndex)) {
    include_once $tplIndex;
} else {
    // Fallback minimal HTML if template is missing
    ?>
    <!doctype html>
    <html>
    <head><meta charset="utf-8"><title>Attorney Directory</title></head>
    <body>
      <h1>Attorney Directory</h1>
      <?php if ($item): ?>
        <h2><?php echo htmlspecialchars($item['itemname'] ?? ''); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($item['itemdesc'] ?? '')); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($item['categoryname'] ?? ''); ?></p>
        <p><strong>Price:</strong> $<?php echo htmlspecialchars($item['itemprice'] ?? '0.00'); ?></p>
      <?php else: ?>
        <p>No items found yet.</p>
      <?php endif; ?>
    </body>
    </html>
    <?php
}

if (is_file($tplFooter)) include_once $tplFooter;
?>
