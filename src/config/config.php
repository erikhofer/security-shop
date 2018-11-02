<?php
if (file_exists(__DIR__ . '/config-local.php')) {
    $local = include __DIR__ . '/config-local.php';
} else {
    $local = [];
}

$default = [
    'db' => [
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => null,
        'database' => 'mi130'
    ]
];

return array_replace_recursive($default, $local);
