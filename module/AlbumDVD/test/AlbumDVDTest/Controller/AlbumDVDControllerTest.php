<?php

namespace AlbumDVDTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AlbumDVDControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        $this->setApplicationConfig(
            include '/home/milin.mestry/public_html/zf2_demo/config/application.config.php'
        );
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/albumdvd');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('AlbumDVD');
        $this->assertControllerName('AlbumDVD\Controller\AlbumDVD');
        $this->assertControllerClass('AlbumDVDController');
        $this->assertMatchedRouteName('album-dvd');
    }

    public function testAddActionRedirectsAfterValidPost()
    {
        $albumTableMock = $this->getMockBuilder('AlbumDVD\Model\AlbumDVDTable')
            ->disableOriginalConstructor()
            ->getMock();

        $albumTableMock->expects($this->once())
            ->method('saveAlbum')
            ->will($this->returnValue(null));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('AlbumDVD\Model\AlbumDVDTable', $albumTableMock);

        $postData = array(
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => '',
        );
        $this->dispatch('/albumdvd/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/albumdvd/');
    }

    public function testEditActionCanBeAccessed()
    {
        $this->dispatch('/albumdvd/edit/1');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('AlbumDVD');
        $this->assertControllerName('AlbumDVD\Controller\AlbumDVD');
        $this->assertControllerClass('AlbumDVDController');
        $this->assertMatchedRouteName('album-dvd');
    }

    public function testEditActionRedirectsAfterValidPost()
    {
        $album_id = 1;
        $albumTableMock = $this->getMockBuilder('AlbumDVD\Model\AlbumDVDTable')
            ->disableOriginalConstructor()
            ->getMock();

        $albumTableMock->expects($this->once())
            ->method('getAlbum')
            ->will($this->returnValue(new \AlbumDVD\Model\AlbumDVD($album_id)));

        $albumTableMock->expects($this->once())
            ->method('saveAlbum')
            ->will($this->returnValue(null));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('AlbumDVD\Model\AlbumDVDTable', $albumTableMock);

        $postData = array(
            'title'  => 'Jake Bugg',
            'artist' => 'Jake Bugg',
            'id'     => $album_id,
        );
        $this->dispatch('/albumdvd/edit/' . $album_id, 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/albumdvd/');
    }
}