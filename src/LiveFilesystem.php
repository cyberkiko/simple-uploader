<?php
namespace LiveAnswer;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use OpenCloud\Rackspace;
use League\Flysystem\Rackspace\RackspaceAdapter;
use Aws\S3\S3Client;
use LiveAnswer\Adapters\AwsS3Adapter;

/**
*
*/
class LiveFilesystem
{

    protected $livesystem;

    public function __construct()
    {
        // load config
    }

    public function storage($name = null, $config = [])
    {
        $name = $name ?: $this->get_default_driver();
        return $this->{'create_' . $name . '_driver'}($config);
    }

    /**
     * Create an instance of the local driver.
     *
     * @param  array  $config
     */
    public function create_local_driver(array $config)
    {
        $config['root'] = __DIR__.'/uploads';
        $permissions = isset($config['permissions']) ? $config['permissions'] : [];
        return $this->create_fly_system(new Local($config['root'], FILE_APPEND), $config);
    }

    public function create_rackspace_driver(array $config)
    {
        // Get the container we want to use
        $container = $this->get_rackspace_container($config);
        $this->livesystem =  $this->create_fly_system(new RackspaceAdapter($container), $config);
        return $this->livesystem;
    }

    public function create_aws_s3_driver(array $config)
    {
        $client = S3Client::factory($config['client']);
        $bucket = $config['bucket_name'];
        $prefix = $config['optional-prefix'];

        $this->livesystem = $this->create_fly_system(new AwsS3Adapter($client, $bucket, $prefix), $config);
        return $this->livesystem;
    }
    // public function get_s3_image_url($path)
    // {
    //     $bucket = $this->storage->getAdapter()->getBucket();
    //     $url = $this->storage
    //         ->getAdapter()
    //         ->getClient()
    //         ->getObjectUrl($bucket, $path);

    //     return $url;
    // }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface  $adapter
     * @param  array  $config
     * @return \League\Flysystem\FlysystemInterface
     */

    protected function create_fly_system(AdapterInterface $adapter, array $config)
    {
        return new Filesystem($adapter);
    }


    private function get_rackspace_container($config)
    {
         $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
            'username' => $config['username'],
            'apiKey' => $config['api_key']
        ));

        $objectStoreService = $client->objectStoreService(null, 'DFW', 'publicURL');
        $container = $objectStoreService->getContainer($config['container']);
        return $container;
    }

    private function get_default_driver()
    {
        return 'local';
    }

}
