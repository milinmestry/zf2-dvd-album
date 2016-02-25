<?php

namespace AlbumDVD\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use AlbumDVD\Module;

class AlbumDVDTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAllDefault()
    {
        $resultSet = $this->tableGateway->select(
            array('is_active' => 1)
        );
        return $resultSet;
    }

    public function fetchAll()
    {
//         $select = new Select(Module::TABLE_ZF2DEMO_CD_ALBUM);
        $select = $this->tableGateway->getSql()->select();
        $expression_added_on = new \Zend\Db\Sql\Expression('FROM_UNIXTIME(`added_on`)');
        $select->columns(array('id', 'title', 'artist', 'added_by',
            'added_on' => $expression_added_on))
                ->where(array('is_active' => 1))
                ->order(array('added_on DESC'));
        $resultSet = $this->tableGateway->selectWith($select);
//         print_r($expression_added_on);
        return $resultSet;
    }

    public function fetchAllWithPaging($paginated = false)
    {
        if ($paginated) {
            // create a new Select object for the table album
            $select = $this->tableGateway->getSql()->select();
            $expression_added_on = new \Zend\Db\Sql\Expression('FROM_UNIXTIME(`added_on`)');
            $select->columns(array('id', 'title', 'artist', 'added_by',
                'added_on' => $expression_added_on))
                ->where(array('is_active' => 1))
                ->order(array('id DESC'));
            // create a new result set based on the Album entity
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new AlbumDVD());
            // create a new pagination adapter object
            $paginatorAdapter = new DbSelect(
                // our configured select object
                $select,
                // the adapter to run it against
                $this->tableGateway->getAdapter(),
                // the result set to hydrate
                $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        return $this->fetchAll();
    }

    public function getAlbum($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveAlbum(AlbumDVD $album)
    {
        $username = 'milin.mestry';
        $data = array(
            'artist' => $album->artist,
            'title'  => $album->title,
            'added_by' => $username,
            'edited_by' => $username,
        );

        $id = (int) $album->id;
        if ($id == 0) {
            $data['added_on'] = time();
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue(); //Add this line
        } else {
            if ($this->getAlbum($id)) {
                $data['updated_on'] = time();
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        return $id;
    }

    public function deleteAlbum($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}
