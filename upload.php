<?php
require __DIR__ . '/vendor/autoload.php';

use LiveAnswer\LiveUploader;

// Local Adapter
// $adapter = new Local(__DIR__.'/uploads');

$live_uploader = new LiveUploader();

// $config = [
//     'container' => '',
//     'username' => '',
//     'api_key' => ''
// ];


// $config = [
//     'client' => [
//         'key'    => '',
//         'secret' => '',
//         'region' => '',
//     ],
//     'bucket_name' => ''
// ];

$storage = $live_uploader->storage();


$contents = $_FILES['upload-custom']['tmp_name'];
$file_name = $_FILES['upload-custom']['name'];

$storage->write($file_name, $contents);
