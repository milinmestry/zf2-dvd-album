<?php

namespace AlbumDVDRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use AlbumDVD\Model\AlbumDVD;
use AlbumDVD\Form\AlbumDVDForm;
use AlbumDVD\Model\AlbumDVDTable;
use Zend\View\Model\JsonModel;

class AlbumDVDRestController extends AbstractRestfulController {

    protected $albumTable;

    public function getList()
    {
        $results = $this->getAlbumTable()->fetchAll();
        $data = array();
        foreach($results as $result) {
            $data[] = $result;
        }
        $records_count = count($data);

        $status = array('request' => 'getList', 'request_unixtime' => time()
            , 'error_code' => 0, 'error_message' => null
            , 'total_records' => $records_count);

        return new JsonModel(array(
            'status' => $status,
            'data' => $data,
        ));
    }

    public function get($id)
    {
        $album = null;
        try {
            $album = $this->getAlbumTable()->getAlbum($id);
            $status = array('request' => 'get', 'request_unixtime' => time()
                , 'error_code' => 0, 'error_message' => null
                , 'total_records' => 1);
        }
        catch (\Exception $e) {
            $status['error_code'] = '10001';
            $status['error_message'] = $e->getMessage();
            $status['total_records'] = 0;
        }

        return new JsonModel(array(
            'status' => $status,
            'data' => $album,
        ));
    }

    public function create($data)
    {
        $id = 0;
        $form = new AlbumDVDForm();
        $album = new AlbumDVD();
        $form->setInputFilter($album->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $album->exchangeArray($form->getData());
            $id = $this->getAlbumTable()->saveAlbum($album);
            $response_data = $this->get($id);

            $status = array('request' => 'create', 'request_unixtime' => time()
                , 'error_code' => 0, 'error_message' => null
                , 'total_records' => 1);
        }
        else {
            $response_data = null;
            $status['error_code'] = '10002';
            $status['error_message'] = 'Fail to save data.';
            $status['total_records'] = 0;
        }

        return new JsonModel(array(
            'status' => $status,
            'data' => $response_data,
        ));
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        $album = $this->getAlbumTable()->getAlbum($id);
        $form  = new AlbumDVDForm();
        $form->bind($album);
        $form->setInputFilter($album->getInputFilter());
        $form->setData($data);

        $status = array('request' => 'update', 'request_unixtime' => time()
            , 'error_code' => 0, 'error_message' => null
            , 'total_records' => 1);

        if ($form->isValid()) {
            $id = $this->getAlbumTable()->saveAlbum($form->getData());
            $response_data = $this->get($id);
        } else {
            $response_data = null;
            $status['error_code'] = '10003';
            $status['error_message'] = 'Fail to update data.';
            $status['total_records'] = 0;
        }

        return new JsonModel(array(
            'status' => $status,
            'data' => 'Record updated.',
        ));
    }

    public function delete($id)
    {
        $this->getAlbumTable()->deleteAlbum($id);

        $status = array('request' => 'delete', 'request_unixtime' => time()
            , 'error_code' => 0, 'error_message' => null
            , 'total_records' => 1);

        return new JsonModel(array(
            'status' => $status,
            'data' => 'deleted',
        ));
    }

    public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('AlbumDVD\Model\AlbumDVDTable');
        }
        return $this->albumTable;
    }
}