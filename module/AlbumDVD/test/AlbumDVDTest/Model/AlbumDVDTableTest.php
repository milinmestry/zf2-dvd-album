<?php
namespace AlbumDVDTest\Model;

use AlbumDVD\Model\AlbumDVDTable;
use AlbumDVD\Model\AlbumDVD;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;

class AlbumDVDTableTest extends PHPUnit_Framework_TestCase
{
    public function testFetchAllReturnsAllAlbums()
    {
        $resultSet = new ResultSet();
        $mockTableGateway = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array('select'),
            array(),
            '',
            false
        );
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with()
                         ->will($this->returnValue($resultSet));

        $albumTable = new AlbumDVDTable($mockTableGateway);

        $this->assertSame($resultSet, $albumTable->fetchAllDefault());
    }

    public function testCanRetrieveAnAlbumByItsId()
    {
        $album_id = 1;
        $album = new AlbumDVD();
        $album->exchangeArray(array('id'     => $album_id,
            'artist' => 'Jake Bugg',
            'title'  => 'Jake Bugg'));

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new AlbumDVD());
        $resultSet->initialize(array($album));

        $mockTableGateway = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array('select'),
            array(),
            '',
            false
        );
        $mockTableGateway->expects($this->once())
        ->method('select')
        ->with(array('id' => $album_id))
        ->will($this->returnValue($resultSet));

        $albumTable = new AlbumDVDTable($mockTableGateway);

        $this->assertSame($album, $albumTable->getAlbum($album_id));
    }

    public function testCanDeleteAnAlbumByItsId()
    {
        $mockTableGateway = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array('delete'),
            array(),
            '',
            false
        );
        $mockTableGateway->expects($this->once())
        ->method('delete')
        ->with(array('id' => 123));

        $albumTable = new AlbumDVDTable($mockTableGateway);
        $albumTable->deleteAlbum(123);
    }

    public function testSaveAlbumWillInsertNewAlbumsIfTheyDontAlreadyHaveAnId()
    {
        $username = 'milin.mestry';
        $albumData = array(
            'artist' => 'The Military Wives',
            'title'  => 'In My Dreams',
            'added_by' => $username,
            'updated_by' => $username,
            'added_on' => time(),
        );
        $album     = new AlbumDVD();
        $album->exchangeArray($albumData);

        $mockTableGateway = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array('insert'),
            array(),
            '',
            false
        );
        $mockTableGateway->expects($this->once())
        ->method('insert')
        ->with($albumData);

        $albumTable = new AlbumDVDTable($mockTableGateway);
        $albumTable->saveAlbum($album);
    }


    public function testSaveAlbumWillUpdateExistingAlbumsIfTheyAlreadyHaveAnId()
    {
        $album_id = 1;
        $username = 'milin.mestry';
        $albumData = array(
            'id'     => $album_id,
            'artist' => 'The Military Wives',
            'title'  => 'In My Dreams',
        );
        $album     = new AlbumDVD();
        $album->exchangeArray($albumData);

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new AlbumDVD());
        $resultSet->initialize(array($album));

        $mockTableGateway = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array('select', 'update'),
            array(),
            '',
            false
        );
        $mockTableGateway->expects($this->once())
        ->method('select')
        ->with(array('id' => $album_id))
        ->will($this->returnValue($resultSet));

        $mockTableGateway->expects($this->once())
        ->method('update')
        ->with(
            array(
                'artist' => 'The Military Wives',
                'title'  => 'In My Dreams',
                'added_by' => $username,
                'updated_by' => $username,
                'updated_on' => time(),
            ),
            array('id' => $album_id)
        );

        $albumTable = new AlbumDVDTable($mockTableGateway);
        $albumTable->saveAlbum($album);
    }

    public function testExceptionIsThrownWhenGettingNonExistentAlbum()
    {
        $album_id = 1;
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new AlbumDVD());
        $resultSet->initialize(array());

        $mockTableGateway = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array('select'),
            array(),
            '',
            false
        );
        $mockTableGateway->expects($this->once())
        ->method('select')
        ->with(array('id' => $album_id))
        ->will($this->returnValue($resultSet));

        $albumTable = new AlbumDVDTable($mockTableGateway);

        try {
            $albumTable->getAlbum($album_id);
        }
        catch (\Exception $e) {
            $this->assertSame('Could not find row ' . $album_id, $e->getMessage());
            return;
        }

        $this->fail('Expected exception was not thrown');
    }

}