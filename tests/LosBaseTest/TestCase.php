<?php

namespace LosBaseTest;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    protected function setUp()
    {
        $this->setUpSm();
    }

    public function setUpSm($globPath = null)
    {
        $serviceManagerUtil = new ServiceManagerTestCase();
        $config = $serviceManagerUtil->getConfiguration();
        if ($globPath != null) {
            $config['module_listener_options']['config_glob_paths'] = [$globPath];
        }

        $this->serviceManager = $serviceManagerUtil->getServiceManager($config);
    }
}
