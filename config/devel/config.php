<?php

use Phalcon\Config;

return new Config( 
    [
        'database'    => [
            'adapter'  => 'Mysql',
            'host'     => 'localhost',
            'username' => 'root',
            'password' => 'root',
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
        ],
        'housekeeping' => [
            'filelog' => [
                'click' => [
                    'archieve'  => '-1', // time for archieving/compressing filelog
                    'retention' => '-30', // time for sent to data warehouse then cleanup
                    'warehouse' => [
                        'host'      => '',
                        'protocol'  => 'ftp'
                    ]
                ]
            ]
            
        ]
    ]
);
