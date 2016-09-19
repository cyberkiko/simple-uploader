<?php
namespace LiveAnswer;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
require_once(__DIR__.'/vendor/Rackspace/cloudfiles/cloudfiles.php');


class RackspaceAdapter implements AdapterInterface
{
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var string
     */
    protected $prefix;
    /**
     * Constructor.
     *
     * @param Container $container
     * @param string    $prefix
     */
    public function __construct($container, $prefix = null)
    {
        // $this->setPathPrefix($prefix);
        $this->container = $container;
    }


        /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        // var_dump($contents);
        $local = __DIR__.'/uploads/' . $path;

        // upload to local
        move_uploaded_file($contents, $local);

        // upload file to Rackspace
        $object = $this->container->create_object($path);
        $object->load_from_filename($local);
        unlink($local);
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config){}

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config){}

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config){}

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath){}

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath){}

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path){}

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname){}

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config){}

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility){}

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path){}

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path){}

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path){}

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false){}

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path){}

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path){}

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path){}

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path){}

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path){}
}
