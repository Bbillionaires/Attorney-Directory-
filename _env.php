<?php
header('Content-Type: text/plain');
$mods = array_map('strtolower', get_loaded_extensions());
echo "pdo_pgsql: ".(in_array('pdo_pgsql',$mods)?'OK':'MISSING')."\n";
echo "pdo_mysql: ".(in_array('pdo_mysql',$mods)?'OK':'MISSING')."\n";
foreach (['PORT','DB_HOST','DB_PORT','DB_NAME','DB_USER'] as $k) {
  $v = getenv($k); if ($k==='DB_USER' || $k==='DB_HOST') { $v = $v ?: '(empty)'; }
  echo "$k=$v\n";
}
