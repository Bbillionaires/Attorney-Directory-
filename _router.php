<?php header('Content-Type:text/plain');
echo "DIRECT FILE OK: _router.php\n";
echo "cwd=" . getcwd() . "\n";
foreach (['add.php','public_list.php','list.php','styles.css','index.php','router.php'] as $f) {
  echo $f . '=' . (file_exists(__DIR__.'/'.$f) ? 'yes' : 'no') . "\n";
}
