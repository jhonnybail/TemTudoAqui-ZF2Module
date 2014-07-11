<?php

return array(
    'router' => array(
        'routes' => array(
            'tta' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/tta',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'tta-common' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/common/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'TemTudoAqui\Common\Controller',
                                'action'		=> 'list',
                            ),
                        ),
                    ),
                    'tta-locality' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/locality/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'TemTudoAqui\Common\Locality\Controller',
                                'action'		=> 'list',
                            ),
                        ),
                    ),
                    'tta-user' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/user[/:controller]/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'TemTudoAqui\User\Controller',
                                'controller'    => 'user',
                                'action' 		=> 'list',
                            ),
                        ),
                    ),
                    'store-user' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/store/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' 		=> 'list',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'doctrine.authenticationadapter.orm_correios' => new DoctrineModule\Service\Authentication\AdapterFactory('orm_correios'),
            'doctrine.authenticationstorage.orm_correios' => new DoctrineModule\Service\Authentication\StorageFactory('orm_correios'),
            'doctrine.authenticationservice.orm_correios' => new DoctrineModule\Service\Authentication\AuthenticationServiceFactory('orm_correios'),
            'doctrine.connection.orm_correios' => new DoctrineORMModule\Service\DBALConnectionFactory('orm_correios'),
            'doctrine.configuration.orm_correios' => new DoctrineORMModule\Service\ConfigurationFactory('orm_correios'),
            'doctrine.entitymanager.orm_correios' => new DoctrineORMModule\Service\EntityManagerFactory('orm_correios'),
            'doctrine.driver.orm_correios' => new DoctrineModule\Service\DriverFactory('orm_correios'),
            'doctrine.eventmanager.orm_correios' => new DoctrineModule\Service\EventManagerFactory('orm_correios'),
            'doctrine.entity_resolver.orm_correios' => new DoctrineORMModule\Service\EntityResolverFactory('orm_correios'),
            'doctrine.sql_logger_collector.orm_correios' => new DoctrineORMModule\Service\SQLLoggerCollectorFactory('orm_correios'),
            'doctrine.mapping_collector.orm_correios' => function (Zend\ServiceManager\ServiceLocatorInterface $sl) {
                    $em = $sl->get('doctrine.entitymanager.orm_correios');

                    return new DoctrineORMModule\Collector\MappingCollector($em->getMetadataFactory(), 'orm_correios_mappings');
                },
            'DoctrineORMModule\Form\Annotation\AnnotationBuilder' => function(Zend\ServiceManager\ServiceLocatorInterface $sl) {
                    return new DoctrineORMModule\Form\Annotation\AnnotationBuilder($sl->get('doctrine.entitymanager.orm_correios'));
                },
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'TemTudoAqui\Common\Locality\Controller\Country' 		=> 'TemTudoAqui\Common\Locality\Controller\CountryController',
            'locality-country' 										=> 'TemTudoAqui\Common\Locality\Controller\CountryController',
            'TemTudoAqui\Common\Locality\Controller\State' 			=> 'TemTudoAqui\Common\Locality\Controller\StateController',
            'locality-state' 										=> 'TemTudoAqui\Common\Locality\Controller\StateController',
            'TemTudoAqui\Common\Locality\Controller\City' 			=> 'TemTudoAqui\Common\Locality\Controller\CityController',
            'locality-city' 										=> 'TemTudoAqui\Common\Locality\Controller\CityController',
            'TemTudoAqui\Common\Locality\Controller\Address' 		=> 'TemTudoAqui\Common\Locality\Controller\AddressController',
            'locality-address' 										=> 'TemTudoAqui\Common\Locality\Controller\AddressController',
            'TemTudoAqui\Common\Controller\Metier' 					=> 'TemTudoAqui\Common\Controller\MetierController',
            'metier' 												=> 'TemTudoAqui\Common\Controller\MetierController',
            'TemTudoAqui\Common\Controller\Business' 				=> 'TemTudoAqui\Common\Controller\BusinessController',
            'business' 												=> 'TemTudoAqui\Common\Controller\BusinessController',
            'TemTudoAqui\Common\Controller\Profession'				=> 'TemTudoAqui\Common\Controller\ProfessionController',
            'profession'											=> 'TemTudoAqui\Common\Controller\ProfessionController',
            'TemTudoAqui\Common\Controller\Schooling'				=> 'TemTudoAqui\Common\Controller\SchoolingController',
            'schooling'												=> 'TemTudoAqui\Common\Controller\SchoolingController',
            'TemTudoAqui\Common\Controller\Hobbie'					=> 'TemTudoAqui\Common\Controller\HobbieController',
            'hobbie'												=> 'TemTudoAqui\Common\Controller\HobbieController',
            'TemTudoAqui\User\Controller\User' 						=> 'TemTudoAqui\User\Controller\UserController',
            'user' 										            => 'TemTudoAqui\User\Controller\UserController',
            'TemTudoAqui\User\Controller\Telephone' 	            => 'TemTudoAqui\User\Controller\TelephoneController',
            'telephone' 								            => 'TemTudoAqui\User\Controller\TelephoneController',
            'TemTudoAqui\User\Controller\Email' 		            => 'TemTudoAqui\User\Controller\EmailController',
            'email' 									            => 'TemTudoAqui\User\Controller\EmailController',
            'TemTudoAqui\User\Controller\UserAuth'		            => 'TemTudoAqui\User\Controller\UserAuthController',
            'userauth'									            => 'TemTudoAqui\User\Controller\UserAuthController',
            'TemTudoAqui\User\Controller\Role'		                => 'TemTudoAqui\User\Controller\RoleController',
            'role'									                => 'TemTudoAqui\User\Controller\RoleController',
            'TemTudoAqui\User\Controller\Privilege'		            => 'TemTudoAqui\User\Controller\PrivilegeController',
            'privilege'									            => 'TemTudoAqui\User\Controller\PrivilegeController',
            'TemTudoAqui\User\Controller\Resource'		            => 'TemTudoAqui\User\Controller\ResourceController',
            'resource'									            => 'TemTudoAqui\User\Controller\ResourceController',
            'TemTudoAqui\User\Controller\UserPrivilege'	            => 'TemTudoAqui\User\Controller\UserPrivilegeController',
            'userprivilege'								            => 'TemTudoAqui\User\Controller\UserPrivilegeController',
            'TemTudoAqui\User\Controller\RolePrivilege'	            => 'TemTudoAqui\User\Controller\RolePrivilegeController',
            'roleprivilege'								            => 'TemTudoAqui\User\Controller\RolePrivilegeController',
            'TemTudoAqui\User\Controller\Logger'	                => 'TemTudoAqui\User\Controller\LoggerController',
            'logger'								                => 'TemTudoAqui\User\Controller\LoggerController',
            'TemTudoAqui\Store\Controller\Category' 	            => 'TemTudoAqui\Store\Controller\CategoryController',
            'category' 	                                            => 'TemTudoAqui\Store\Controller\CategoryController',
            'TemTudoAqui\Store\Controller\Product'   	            => 'TemTudoAqui\Store\Controller\ProductController',
            'product' 	                                            => 'TemTudoAqui\Store\Controller\ProductController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'locality-country' 		=> __DIR__ . '/../views',
            'locality-state' 		=> __DIR__ . '/../views',
            'locality-city' 		=> __DIR__ . '/../views',
            'metier' 				=> __DIR__ . '/../views',
            'business'	 			=> __DIR__ . '/../views',
            'profession' 			=> __DIR__ . '/../views',
            'schooling' 			=> __DIR__ . '/../views',
            'hobbie'	 			=> __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'TemTudoAqui_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/TemTudoAqui/Common')
            ),
            'TemTudoAqui\User_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/TemTudoAqui/User')
            ),
            'TemTudoAqui\Store_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/TemTudoAqui/Store')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'TemTudoAqui\Common'    => 'TemTudoAqui_driver',
                    'TemTudoAqui\User'      => 'TemTudoAqui\User_driver',
                    'TemTudoAqui\Store'     => 'TemTudoAqui\Store_driver'
                ),
            ),
        ),
        'configuration' => array(
            'orm_default' => array(
                'proxy_dir' => $_SERVER['DOCUMENT_ROOT'].'/../data/DoctrineORMModule/Proxy',
                'proxy_namespace' => 'TemTudoAquiCommon',
                'types' => array(
                    'tta_datetime'  => 'TemTudoAqui\Utils\Doctrine\DateTime',
                    'tta_file'      => 'TemTudoAqui\Utils\Doctrine\File',
                ),
            ),
            'orm_correios' => array(
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',
                'driver'            => 'orm_correios',
                'generate_proxies'  => true,
                'filters'           => array()
            ),
        ),
        'entitymanager' => array(
            'orm_correios' => array(
                'connection'    => 'orm_correios',
                'configuration' => 'orm_correios'
            ),
        ),
        'eventmanager' => array(
            'orm_correios' => array()
        ),
        'sql_logger_collector' => array(
            'orm_correios' => array(),
        ),
        'entity_resolver' => array(
            'orm_correios' => array()
        ),
        'authentication' => array(
            'orm_correios' => array(
                'objectManager' => 'doctrine.entitymanager.orm_correios'
            ),
        ),
    ),
    'session' => array(
        'path' => $_SERVER["DOCUMENT_ROOT"]."/../data/cookie/",
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'wecan',
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionStorage',
        'validators' => array(
            array(
                'Zend\Session\Validator\RemoteAddr',
                'Zend\Session\Validator\HttpUserAgent',
            ),
        ),
    ),
);
