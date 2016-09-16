<?php
namespace LiveUploader;

use League\Flysystem\Adapter\AbstractAdapter;

$root_path = $_SERVER['DOCUMENT_ROOT'];
require_once($root_path.'/vendor/Rackspace/cloudfiles/cloudfiles.php');

class RackspaceAdapter extends AbstractAdapter
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
    public function __construct(Container $container, $prefix = null)
    {
        $this->setPathPrefix($prefix);
        $this->container = $container;
    }

        /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $headers = [];
        if ($config && $config->has('headers')) {
            $headers =  $config->get('headers');
        }
        $response = $this->container->uploadObject($location, $contents, $headers);
        return $this->normalizeObject($response);
    }
}
