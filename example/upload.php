<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ .'/config.php';

use LiveAnswer\LiveFilesystem;

$live_uploader = new LiveFilesystem();

$loaded_config = array_merge($config['services']['aws_s3'], ['bucket_name' => 'liveanswer-rs', 'optional-prefix' => 'test-upload']);

// $storage = $live_uploader->storage('rackspace', $loaded_config);
// aws s3
$storage = $live_uploader->storage('aws_s3', $loaded_config);


// $ = $_FILES['upload-custom']['tmp_name'];
$file_name = $_FILES['upload-custom']['name'];

// upload
$contents = fopen($_FILES['upload-custom']['tmp_name'], 'r+');
try {
   $data = $storage->writeStream($file_name, $contents);
   fclose($contents);
} catch (Exception $e) {
    echo $e->getMessage();
}
// check if exists
// if ($storage->has($file_name)) {
//     $data = $storage->update($file_name, $contents);
// } else {
//     $data = $storage->write($file_name, $contents);
// }

