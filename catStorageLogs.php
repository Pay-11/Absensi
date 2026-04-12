<?php
$lines = file('storage/logs/laravel.log');
foreach ($lines as $line) {
    if (strpos($line, 'local.ERROR') !== false || strpos($line, 'Next Illuminate') !== false) {
        echo trim($line) . PHP_EOL;
    }
}
