<?php
require __DIR__ . '/vendor/autoload.php';
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

// Local Adapter
// $adapter = new Local(__DIR__.'/temp');


$filesystem = new Filesystem($adapter);
$filesystem->write($_FILES['upload-custom']['name']);
