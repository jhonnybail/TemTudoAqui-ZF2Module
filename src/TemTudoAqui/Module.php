<?php

namespace TemTudoAqui;

use Zend\Mvc\MvcEvent,
    Zend\Session\SessionManager,
    Zend\Session\Container,
    TemTudoAqui\User\Logger,
    TemTudoAqui\User\Resource,
    TemTudoAqui\User\UserAuth,
    TemTudoAqui\Utils\Data\DateTime,
    TemTudoAqui\Utils\Data\File,
    TemTudoAqui\Utils\Net\FileReference,
    TemTudoAqui\Utils\Data\ArrayObject;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $eventManager->attach('dispatch', array($this, 'loadConfiguration'));

        $session = $e->getApplication()->getServiceManager()->get("Zend\Session\SessionManager");
        $session->start();

        $sys                    = new ArrayObject;
        $sys['pathSession']     = $e->getApplication()->getServiceManager()->get("config")['session']['path'];

        $sys['directory']		= $_SERVER['DOCUMENT_ROOT'];
        if($sys['directory'][strlen($sys['directory'])-1] != "/")
            $sys['directory'] .= '/';

        $sys['protocol'] 		= $_SERVER["REQUEST_SCHEME"];
        $sys['url']				= $sys['protocol']."://".$_SERVER['HTTP_HOST']."/";

        $sys['data']			= 'data/';
        $sys['dataUsers'] 		= $sys['data']."users/";

        $sys['dataProducts']	= 'products/';

        $config                 = $e->getApplication()->getServiceManager()->get("config")['system'];
        if(!empty($config))
            $sys->merge($config);

        System::SetSystem((array)$sys);

        if(empty($e->getRequest()->getCookie()->session)){
            $dT         = new DateTime;
            $idCookie   = md5(System::GetVariable('REMOTE_ADDR').$dT->format("YmdHis"));
            $file = File::CreateFileByString("[]");
            FileReference::Save($file, $sys['pathSession'], $idCookie, 'cok');
            $cookie     = new SetCookie('session', $idCookie, time() + 365 * 60 * 60 * 24, null, System::GetVariable("HTTP_HOST"));
            $e->getResponse()->getHeaders()->addHeader($cookie);
        }

    }

    public function loadConfiguration(MvcEvent $e)
    {

        $controller         = $e->getTarget();
        $controllerClass    = get_class($controller);
        $actionName         = $e->getRouteMatch()->getParam('action');
        $manager            = $e->getApplication()->getServiceManager()->get('Zend\Session\SessionManager');
        $idUserAuth         = $manager->getStorage()->userAuth;

        if(!empty($idUserAuth) && $actionName != 'list' && $controller->getResourceName() != 'logger'){

            try{

                $loggerDao              = $e->getApplication()->getServiceManager()->get("TemTudoAqui\User\Dao\LoggerDao");

                $logger                 = new Logger;
                $logger->controller     = $controllerClass;
                $logger->action         = $actionName;
                $logger->path           = $e->getRequest()->getUri()->getPath();

                $daoUserAuth            = $e->getApplication()->getServiceManager()->get("TemTudoAqui\User\Dao\UserAuthDao");
                $userAuth               = new UserAuth;
                $userAuth->id           = $idUserAuth;
                $userAuth               = $daoUserAuth->findById($userAuth);
                $logger->userAuth       = $userAuth;
                $logger->dateRequest    = new DateTime;

                if(!isset($controller->getProtectedActions()[$actionName])){
                    $logger->userAuthActive = $userAuth;
                    $logger->dateActive     = $logger->dateRequest;
                }

                $logger->method         = $e->getRequest()->getMethod();
                $logger->dataPost       = (array)$e->getRequest()->getPost();
                $logger->dataGet        = (array)$e->getRequest()->getQuery();
                $logger->dataFile       = (array)$e->getRequest()->getFiles();

                $daoResource            = $e->getApplication()->getServiceManager()->get("TemTudoAqui\User\Dao\ResourceDao");
                $resource               = new Resource;
                $resource->type         = "(equals)".$controller->getResourceName();
                $resource               = $daoResource->find($resource)[0];
                $logger->resource       = $resource;

                $loggerDao->save($logger);

            }catch(Exception $e){

            }

        }

    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getServiceConfig(){
        return array(
            'factories' => array(
                'TemTudoAqui\Common\Session' => function($service){
                        return new \TemTudoAqui\Common\Session($service->get("request"));
                    },
                'TemTudoAqui\Common\Dao\MetierDao' => function($service){
                        return new \TemTudoAqui\Common\Dao\MetierDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Common\Dao\BusinessDao' => function($service){
                        return new \TemTudoAqui\Common\Dao\BusinessDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Common\Dao\ProfessionDao' => function($service){
                        return new \TemTudoAqui\Common\Dao\ProfessionDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Common\Dao\HobbieDao' => function($service){
                        return new \TemTudoAqui\Common\Dao\HobbieDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Common\Dao\SchoolingDao' => function($service){
                        return new \TemTudoAqui\Common\Dao\SchoolingDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Common\Locality\Dao\CountryDao' => function($service){
                        return new \TemTudoAqui\Common\Locality\Dao\CountryDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Common\Locality\Dao\StateDao' => function($service){
                        return new \TemTudoAqui\Common\Locality\Dao\StateDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Common\Locality\Dao\CityDao' => function($service){
                        return new \TemTudoAqui\Common\Locality\Dao\CityDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Common\Locality\Dao\AddressDao' => function($service){
                        return new \TemTudoAqui\Common\Locality\Dao\AddressDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\UserDao' => function($service){
                        return new \TemTudoAqui\User\Dao\UserDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\TelephoneDao' => function($service){
                        return new \TemTudoAqui\User\Dao\TelephoneDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\EmailDao' => function($service){
                        return new \TemTudoAqui\User\Dao\EmailDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\RoleDao' => function($service){
                        return new \TemTudoAqui\User\Dao\RoleDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\UserAuthDao' => function($service){
                        return new \TemTudoAqui\User\Dao\UserAuthDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\PrivilegeDao' => function($service){
                        return new \TemTudoAqui\User\Dao\PrivilegeDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\ResourceDao' => function($service){
                        return new \TemTudoAqui\User\Dao\ResourceDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\UserPrivilegeDao' => function($service){
                        return new \TemTudoAqui\User\Dao\UserPrivilegeDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\RolePrivilegeDao' => function($service){
                        return new \TemTudoAqui\User\Dao\RolePrivilegeDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\User\Dao\LoggerDao' => function($service){
                        return new \TemTudoAqui\User\Dao\LoggerDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Store\Dao\CategoryDao' => function($service){
                        return new \TemTudoAqui\Store\Dao\CategoryDao($service->get('Doctrine\ORM\EntityManager'));
                    },
                'TemTudoAqui\Store\Dao\ProductDao' => function($service){
                        return new \TemTudoAqui\Store\Dao\ProductDao($service->get('Doctrine\ORM\EntityManager'));
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
                        }
                        Container::setDefaultManager($sessionManager);
                        return $sessionManager;
                    },
            ),
        );
    }

}
