<?php

use Phalcon\Config;

return new Config( 
    [
        'database'    => [
            'adapter'  => 'Mysql',
            'host'     => '10.10.1.51',
            'username' => 'morpheus',
            'password' => 'luc1ddr34m',
            'name'     => 'layananzakat'
        ],
        'application' => [
            'controllersDir'    => __DIR__ . '/../../controllers/',
            'modelsDir'         => __DIR__ . '/../../models/',
            'baseUri'           => '/core/',
        ],
        'log' => [
            'core'  => [
                'path'      => '/var/log/lzapi/',
                'prefix'    => 'core_'
            ]
        ],
        'queue' => [
            'host'  => '127.0.0.1',
            'port'  => '11300',
            'tube'  => [
                'callbackPool' => 'callbackpool'
            ]
        ],
        'cache' => [
            'host' => "127.0.0.1",
            'port' => 6379
        ]
    ]
);
