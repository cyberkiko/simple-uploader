<?php
namespace LiveAnswer;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use LiveAnswer\RackspaceAdapter;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
require_once(__DIR__.'/vendor/Rackspace/cloudfiles/cloudfiles.php');


/**
*
*/
class LiveUploader
{

    protected $storage;


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
        return $this->create_fly_system(new RackspaceAdapter($container), $config);
    }

    public function create_aws_s3_driver(array $config)
    {
        $client = S3Client::factory($config['client']);
        $bucket = $config['bucket_name'];

        return $this->create_fly_system(new AwsS3Adapter($client, $bucket), $config);
    }

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
        $auth = new \CF_Authentication($config['username'], $config['api_key']);
        $auth->authenticate();
        $connection = new \CF_Connection($auth);

        $container = $connection->get_container($config['container']);

        return $container;
    }

    private function get_default_driver()
    {
        return 'local';
    }

}
