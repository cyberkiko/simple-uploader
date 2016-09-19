<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ .'/config.php';

use LiveAnswer\LiveUploader;

$live_uploader = new LiveUploader();

$loaded_config = array_merge($config['services']['rackspace'], ['container' => 'users_imgs']);

$storage = $live_uploader->storage('rackspace', $loaded_config);

// aws s3
// $storage = $live_uploader->storage('aws_s3', $config);


$contents = $_FILES['upload-custom']['tmp_name'];
$file_name = $_FILES['upload-custom']['name'];

// upload



// check if exists
if ($storage->has($file_name)) {
    $data = $storage->update($file_name, $contents);
} else {
    $data = $storage->write($file_name, $contents);
}

