<?php
$pdo=null; $GLOBALS['DB_ERROR']=null; $GLOBALS['DB_INFO']=[];
try{
  $u=getenv('DATABASE_URL'); if(!$u) throw new Exception('DATABASE_URL not set');
  // Normalize and parse
  $u=preg_replace('#^postgresql://#','postgres://',$u);
  $p=parse_url($u); if(!$p) throw new Exception('Bad DATABASE_URL format');
  $host=$p['host']??'';
  $port=$p['port']??5432;
  $db=ltrim($p['path']??'', '/');
  $user=urldecode($p['user']??'');
  $pass=urldecode($p['pass']??'');
  // Build DSN; always SSL on Render Postgres
  $dsn=sprintf('pgsql:host=%s;port=%d;dbname=%s;sslmode=require',$host,$port,$db);
  $GLOBALS['DB_INFO']=[
    'host'=>$host,'port'=>$port,'db'=>$db,'user'=>$user,
    'url_prefix'=>substr($u,0,20).'…', // mask
  ];
  $pdo=new PDO($dsn,$user,$pass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
  ]);
}catch(Throwable $e){ $GLOBALS['DB_ERROR']=$e->getMessage(); }
