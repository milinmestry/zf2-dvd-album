<?php

namespace AlbumDVD\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AlbumDVD\Model\AlbumDVD;
use AlbumDVD\Form\AlbumDVDForm;

class AlbumDVDController extends AbstractActionController
{
    protected $albumTable;
    const PAGING_ITEM_COUNT_PER_PAGE = 25;

    public function indexAction()
    {
        // grab the paginator from the AlbumTable
        $paginator = $this->getAlbumTable()->fetchAllWithPaging(true);
        // set the current page to what has been passed in query string, or to 1 if none set
        $paginator->setCurrentPageNumber(
            (int) $this->params()->fromRoute('page_id', 1));
        // set the number of items per page to 10
        $paginator->setItemCountPerPage(self::PAGING_ITEM_COUNT_PER_PAGE);

        return new ViewModel(array(
            'albums' => $paginator,
            'item_count_per_page' => self::PAGING_ITEM_COUNT_PER_PAGE,
            'current_page_number' => $paginator->getCurrentPageNumber(),
        ));

//         return new ViewModel(array(
//             'albums' => $this->getAlbumTable()->fetchAll(),
//         ));
    }

    public function addAction()
    {
        $form = new AlbumDVDForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new AlbumDVD();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album-dvd');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album-dvd', array(
                'action' => 'add'
            ));
        }
        $album = $this->getAlbumTable()->getAlbum($id);

        $form  = new AlbumDVDForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($form->getData());

                // Redirect to list of albums
                return $this->redirect()->toRoute('album-dvd');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album-dvd');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album-dvd');
        }

        return array(
            'id'    => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );
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
