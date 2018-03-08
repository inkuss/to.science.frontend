<?php
function console_log($obj) {
    file_put_contents('php://stderr', date('Y-m-d H:i:s') . ': ' . print_r($obj, true) . "\n");
}