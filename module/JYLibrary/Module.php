<?php
namespace JYLibrary;
use JYLibrary\Model\SessionTable;
use \Zend\Db\Adapter\Adapter;

use \Zend\Session\Container;
use  \Zend\Mvc\ModuleRouteListener;
use \Zend\Db\TableGateway\TableGateway;
use \Zend\Session\SaveHandler\DbTableGateway;
use \Zend\Session\SaveHandler\DbTableGatewayOptions;
use \Zend\Session\SessionManager;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
	public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'JYLibrary\Model\SessionTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new SessionTable($dbAdapter);
                    return $table;
                },
				
				'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config['session'])) {
                        $session = $config['session'];

                        $sessionConfig = null;
                        if (isset($session['config'])) {
                            $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = null;
                        if (isset($session['storage'])) {
                            $class = $session['storage'];
                            $sessionStorage = new $class();
                        }

                        $sessionSaveHandler = null;
                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                        if (isset($session['validator'])) {
                            $chain = $sessionManager->getValidatorChain();
                            foreach ($session['validator'] as $validator) {
                                $validator = new $validator();
                                $chain->attach('session.validate', array($validator, 'isValid'));

                            }
                        }
                    } else {						
                        $sessionManager = new SessionManager();						
						
						/*
						$adapter = new \Zend\Db\Adapter\Adapter(array(
							'driver' => 'Mysqli',
							'database' => 'jymobile',
							'username' => 'root',
							'password' => '',
							'hostname'=>'localhost'
						));
						
						$tableGateway = new TableGateway('session', $adapter);
						$saveHandler = new DbTableGateway($tableGateway, new DbTableGatewayOptions());
						$manager = new SessionManager();
						$manager->setSaveHandler($saveHandler);
						
						//echo '×Ô¶¨ÒåsessionManager';
						*/
                    }
                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
            ),
        );
    }
	
	
	public function onBootstrap($e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $serviceManager      = $e->getApplication()->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->bootstrapSession($e);
    }

    public function bootstrapSession($e)
    {
        $session = $e->getApplication()
                     ->getServiceManager()
                     ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {
             $session->regenerateId(true);
             $container->init = 1;
        }
    }

}
?>