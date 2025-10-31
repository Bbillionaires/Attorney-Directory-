<?php
header('Content-Type: text/plain');
echo "OK\n";
echo "PORT=".getenv('PORT')."\n";
echo "TIME=".date('c')."\n";
