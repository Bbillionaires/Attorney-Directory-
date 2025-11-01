<?php if (isset($GLOBALS["DB_ERROR"])) { echo "<!-- DB_ERROR: " . htmlspecialchars($GLOBALS["DB_ERROR"]) . " -->"; } ?>
<?php if (isset($GLOBALS["DB_ERROR"])) { echo "<!-- DB_ERROR: " . htmlspecialchars($GLOBALS["DB_ERROR"]) . " -->"; } ?>
<?php
$bid = @file_get_contents(__DIR__.'/build-id.txt') ?: 'unknown';
echo "OK from index.php — BUILD_ID: {$bid} — " . date('c');
