<?php
namespace LiveAnswer\Adapters;

use Guzzle\Http\Exception\ClientErrorResponseException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;
use OpenCloud\Rackspace;
use OpenCloud\ObjectStore\Resource\DataObject;


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
        $this->setPathPrefix($prefix);
        $this->container = $container;
    }


        /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        // var_dump($contents);
        $local = __DIR__.'/tmp_uploads/' . $path;

        // upload to local
        move_uploaded_file($contents, $local);
        $handle = fopen($local, 'r');
        // upload file to Rackspace
        $object = $this->container->uploadObject($path, $handle);
        fclose($handle);
        unlink($local);
        return $object;
    }

    /**
     * Set the path prefix.
     *
     * @param string $prefix
     *
     * @return self
     */
    public function setPathPrefix($prefix)
    {
        $is_empty = empty($prefix);

        if ( ! $is_empty) {
            $prefix = rtrim($prefix, $this->pathSeparator) . $this->pathSeparator;
        }

    }

    /**
     * Get the path prefix.
     *
     * @return string path prefix
     */
    public function getPathPrefix()
    {
        return $this->pathPrefix;
    }
     /**
     * Prefix a path.
     *
     * @param string $path
     *
     * @return string prefixed path
     */
    public function applyPathPrefix($path)
    {
        $path = ltrim($path, '\\/');

        if (strlen($path) === 0) {
            return $this->getPathPrefix() ?: '';
        }

        if ($prefix = $this->getPathPrefix()) {
            $path = $prefix . $path;
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        try {
            $location = $this->applyPathPrefix($path);
            $exists = $this->container->objectExists($location);
        } catch (ClientErrorResponseException $e) {
            return false;
        }

        return $exists;
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
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        $object = $this->getObject($path);
        $data = $this->normalizeObject($object);
        $data['contents'] = (string) $object->getContent();
        return $data;
    }

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

    /**
     * Get an object.
     *
     * @param string $path
     *
     * @return DataObject
     */
    protected function getObject($path)
    {
        $location = $this->applyPathPrefix($path);

        return $this->container->getObject($location);
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeObject(DataObject $object)
    {
        $name = $object->getName();
        // here is the bug
        $name = $this->removePathPrefix($name);
        $mimetype = explode('; ', $object->getContentType());

        return [
            'type'      => ((in_array('application/directory', $mimetype)) ? 'dir' : 'file'),
            'dirname'   => Util::dirname($name),
            'path'      => $name,
            'timestamp' => strtotime($object->getLastModified()),
            'mimetype'  => reset($mimetype),
            'size'      => $object->getContentLength(),
        ];
    }

    /**
     * Remove a path prefix.
     *
     * @param string $path
     *
     * @return string path without the prefix
     */
    public function removePathPrefix($path)
    {
        $pathPrefix = $this->getPathPrefix();

        if ($pathPrefix === null) {
            return $path;
        }

        return substr($path, strlen($pathPrefix));
    }


}
