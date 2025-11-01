<?php
header('Content-Type: text/plain');
echo "ROUTER_INDEX=" . (defined('ROUTER_INDEX') ? ROUTER_INDEX : 'n/a') . "\n";
echo "FROM_ROUTER=" . (defined('FROM_ROUTER') ? 'yes' : 'no') . "\n";
