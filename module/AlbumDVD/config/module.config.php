<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'AlbumDVD\Controller\AlbumDVD' => 'AlbumDVD\Controller\AlbumDVDController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'album-dvd' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/album-dvd[/][:action][/:id][/:page][/:page_id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'page_id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'AlbumDVD\Controller\AlbumDVD',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'template_path_stack' => array(
            'album-dvd' => __DIR__ . '/../view',
        ),
    ),
);
