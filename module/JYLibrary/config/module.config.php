<?php
// module/JYLibrary/conﬁg/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'JYLibrary\Controller\Search' => 'JYLibrary\Controller\SearchController',
			'JYLibrary\Controller\MPortalSite' => 'JYLibrary\Controller\MPortalSiteController',
			'JYLibrary\Controller\OPACSearch' => 'JYLibrary\Controller\OPACSearchController',
			'JYLibrary\Controller\EbookServer1' => 'JYLibrary\Controller\EbookServer1Controller',
        ),
    ),
	
	// The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'Search' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/jysearch[/:action][/:id][/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'JYLibrary\Controller\Search',
                        'action'     => 'index',
                    ),
                ),
            ),
			'MPortalSite' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/mportalsite[/:action][/:id][/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'JYLibrary\Controller\MPortalSite',
                        'action'     => 'index',
                    ),
                ),
            ),
			'OPACSearch' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/opacsearch[/:action][/:id][/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'JYLibrary\Controller\OPACSearch',
                        'action'     => 'index',
                    ),
                ),
            ),
			'EbookServer1' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/ebookserver1[/:action][/:id][/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'JYLibrary\Controller\EbookServer1',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
		
    ),
	
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
?>