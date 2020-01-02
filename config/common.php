<?php

return [
    'const' => [
        'DEBUG' => true,
        'API' => dirname(__DIR__).'/api',
        'IMAGES' => dirname(__DIR__).'/images'
    ],
    'settings' => [],
    'mail' => [
        'login' => '',
        'pass' => '',
        'mailServer' => '',
        'port' => '',
        'encryption' => '',
    ],
    'database' => [
        'driver' => '',
        'host' => '',
        'dbname' => '',
        'username' => '',
        'password' => '',
        'charset'=>'utf8',
        'collation'=>'utf8_unicode_ci',
        'prefix'    => ''
    ]
];
