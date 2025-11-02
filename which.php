<?php
header('Content-Type: text/plain; charset=UTF-8');
echo "WHICH FILE: ", __FILE__, "\n";
echo "URI: ", ($_SERVER['REQUEST_URI'] ?? ''), "\n";
echo "TIME: ", gmdate('c'), "\n";
