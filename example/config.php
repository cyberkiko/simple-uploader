<?php

$enviroment_file = file('.env');


foreach ($enviroment_file as $line){
    putenv($line);
}

$config = [
    'services' => [
        'aws_s3' => [
            'key' => getenv(''),
            'secret' => getenv(''),
            'region' => getenv(''),
        ],
        'rackspace' => [
            'username' => getenv(''),
            'api_key' => getenv(''),
        ],
    ]
];
