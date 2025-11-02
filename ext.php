<?php
header('Content-Type: text/plain');
$ext = get_loaded_extensions();
sort($ext);
echo implode("\n", $ext), "\n";
