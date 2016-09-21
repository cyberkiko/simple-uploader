<?php

$enviroment_file = file('.env');


foreach ($enviroment_file as $line){
    putenv(trim($line));
}

$config = [
    'services' => [
        'aws_s3' => [
            'version' => 'latest',
            'credentials' => [
                'key' => getenv('AWS_S3_KEY'),
                'secret' => getenv('AWS_S3_SECRET'),
            ],
            'region' => getenv('AWS_S3_REGION'),
        ],
        'rackspace' => [
            'username' => getenv('RACKSPACE_USERNAME'),
            'api_key' => getenv('RACKSPACE_API_KEY'),
        ],
    ]
];
