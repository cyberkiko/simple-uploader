<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ .'/config.php';

use LiveAnswer\LiveFilesystem;
use League\Flysystem\AdapterInterface;

$live_filesystem = new LiveFilesystem();

$other = [
    'bucket_name' => 'liveanswer-rs',
    'optional-prefix' => 'test-upload'
];

$loaded_config = array_merge($config['services']['aws_s3'], $other);

// aws s3
$storage = $live_filesystem->storage('aws_s3', $loaded_config);

$url = $storage->getAdapter()->getFileUrl('default-user-570x57.png');
echo $url;
