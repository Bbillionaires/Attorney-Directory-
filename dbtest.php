<?php
require __DIR__ . '/conn.php';
header('Content-Type: text/plain');
echo "ext: ".(extension_loaded('pdo_pgsql')?'pdo_pgsql=yes':'pdo_pgsql=no')."\n";
if(!$pdo){
  http_response_code(503);
  echo "DB_ERROR: ".($GLOBALS['DB_ERROR']??'unknown')."\n";
  echo "DB_INFO: ".json_encode($GLOBALS['DB_INFO'])."\n";
  exit;
}
try{
  $v=$pdo->query('select version() as v')->fetch()['v'] ?? 'unknown';
  echo "OK DB\nversion: $v\n";
}catch(Throwable $e){
  http_response_code(500);
  echo "QUERY_ERROR: ".$e->getMessage()."\n";
}
