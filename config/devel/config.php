<?php

use Phalcon\Config;

return new Config( 
    [
        'database'    => [
            'adapter'  => 'Mysql',
            'host'     => 'localhost',
            'username' => 'mustahik',
            'password' => 'mustahikpwd',
            'name'     => 'mustahik'
        ],
        'application' => [
            'controllersDir'    => __DIR__ . '/../../controllers/',
            'modelsDir'         => __DIR__ . '/../../models/',
            'baseUri'           => 'http://api.mustahik.me/',
        ],
        'log' => [
            'core'  => [
                'path'      => '/app/werks/mustahik/logs/',
                'prefix'    => 'api_'
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
