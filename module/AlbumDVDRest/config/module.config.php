<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'AlbumDVDRest\Controller\AlbumDVDRest'
                => 'AlbumDVDRest\Controller\AlbumDVDRestController')
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'album-dvd-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/album-dvd-rest[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'AlbumDVDRest\Controller\AlbumDVDRest',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array( //Add this config
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);