<?php

require_once 'Cualit/GeoApi/controllers/IndexController.php';

class Geocom_GeoApi_IndexController extends Cualit_GeoApi_IndexController {

	public function remainingQuantityDispatchOrdersAction() {
        $username = (string) $this->getRequest()->getPost('username');
        $password = (string) $this->getRequest()->getPost('password');
        $status = (string) $this->getRequest()->getPost('status');
        $local = (string) $this->getRequest()->getPost('local');
        $response = array('status' => "ok", 'data' => '');

        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);

        if (!$this->getRequest()->isPost()) {
            $this->notFoundException();
            return;
        }

        if (iconv_strlen($username) <= 0 || iconv_strlen($password) <= 0 || iconv_strlen($local) <= 0) {
            $response = array('status' => "error", 'msg' => 'Parametros incorrectos');
            $this->getResponse()->setBody(json_encode($response));
            return;
        }

        $adminUser = Mage::getModel('admin/user');
        if ($adminUser->authenticate($username, $password)) {
            try {
                $response['data'] = Mage::helper('geocom_geoapi')->buildRemainingDispatchOrderResponseData($local,$status);
            } catch (Exception $e) {
                $response = array('status' => "error", 'msg' => 'No existe una orden con ese identificador');
            }
            $this->getResponse()->setBody(json_encode($response));
            return;
        } else {
            $response = array('status' => "error", 'msg' => 'Usuario y contrase?a incorrectos');
            $this->getResponse()->setBody(json_encode($response));
            return;
        }
    }

public function remainingDispatchOrdersAction() {

        $username = (string) $this->getRequest()->getPost('username');
        $password = (string) $this->getRequest()->getPost('password');
        $status = (string) $this->getRequest()->getPost('status');
        
        $local = (string) $this->getRequest()->getPost('local');
        $response = array('status' => "ok", 'data' => '');

        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);

        if (!$this->getRequest()->isPost()) {
            $this->notFoundException();
            return;
        }

        if (iconv_strlen($username) <= 0 || iconv_strlen($password) <= 0 || iconv_strlen($local) <= 0) {
            $response = array('status' => "error", 'msg' => 'Parametros incorrectos');
            $this->getResponse()->setBody(json_encode($response));
            return;
        }

        $adminUser = Mage::getModel('admin/user');
        if ($adminUser->authenticate($username, $password)) {
            
            try {
                $response['data'] = Mage::helper('geocom_geoapi')->buildOrdersResponseData($local,$status);
            } catch (Exception $e) {
                $response = array('status' => "error", 'msg' => 'No existe una orden con ese identificador');
            }
            $this->getResponse()->setBody(json_encode($response));
            return;
        } else {
            $response = array('status' => "error", 'msg' => 'Usuario y contrase?a incorrectos');
            $this->getResponse()->setBody(json_encode($response));
            return;
        }
    }

    public function remainingDispatchOrdersFiltersQuantityAction() {
        
        $fechadesde = (string) $this->getRequest()->getPost('fechadesde');
        $fechahasta = (string) $this->getRequest()->getPost('fechahasta');
        $horadesde = (string) $this->getRequest()->getPost('horadesde');
        $horahasta = (string) $this->getRequest()->getPost('horahasta');
        
        $username = (string) $this->getRequest()->getPost('username');
        $password = (string) $this->getRequest()->getPost('password');
        $local = (string) $this->getRequest()->getPost('local');
        
        $response = array('status' => "ok", 'data' => '');

        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);

        if (!$this->getRequest()->isPost()) {
            $this->notFoundException();
            return;
        }

        if (iconv_strlen($username) <= 0 || iconv_strlen($password) <= 0 || iconv_strlen($local) <= 0 
                || iconv_strlen($fechadesde) <=0 || iconv_strlen($fechahasta) <=0 || iconv_strlen($horadesde) <=0 || iconv_strlen($horahasta)<=0 ){
            $response = array('status' => "error", 'msg' => 'Parametros incorrectos');
            $this->getResponse()->setBody(json_encode($response));
            return;
        }

        $adminUser = Mage::getModel('admin/user');
        if ($adminUser->authenticate($username, $password)) {
            try {
                $filters = new stdClass();
                $filters->fechadesde = $fechadesde;
                $filters->fechahasta = $fechahasta;
                $filters->horadesde = $horadesde;
                $filters->horahasta = $horahasta;
                $filters->local = $local;
                $response['data'] = Mage::helper('geocom_geoapi')->buildOrdersResponseDataFiltersQuantity($filters);
            } catch (Exception $e) {
                $response = array('status' => "error", 'msg' => 'No existe una orden con ese identificador');
            }
            $this->getResponse()->setBody(json_encode($response));
            return;
        } else {
            $response = array('status' => "error", 'msg' => 'Usuario y contrase?a incorrectos');
            $this->getResponse()->setBody(json_encode($response));
            return;
        }
    }
    
    public function remainingDispatchOrdersFiltersAction() {

        $fechadesde = (string) $this->getRequest()->getPost('fechadesde');
        $fechahasta = (string) $this->getRequest()->getPost('fechahasta');
        $horadesde = (string) $this->getRequest()->getPost('horadesde');
        $horahasta = (string) $this->getRequest()->getPost('horahasta');

        $username = (string) $this->getRequest()->getPost('username');
        $password = (string) $this->getRequest()->getPost('password');
        $local = (string) $this->getRequest()->getPost('local');

        $response = array('status' => "ok", 'data' => '');

        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);

        if (!$this->getRequest()->isPost()) {
            $this->notFoundException();
            return;
        }

        if (iconv_strlen($username) <= 0 || iconv_strlen($password) <= 0 || iconv_strlen($local) <= 0 || iconv_strlen($fechadesde) <= 0 || iconv_strlen($fechahasta) <= 0 || iconv_strlen($horadesde) <= 0 || iconv_strlen($horahasta) <= 0) {
            $response = array('status' => "error", 'msg' => 'Parametros incorrectos');
            $this->getResponse()->setBody(json_encode($response));
            return;
        }

        $adminUser = Mage::getModel('admin/user');
        if ($adminUser->authenticate($username, $password)) {
            try {
                $filters = new stdClass();
                $filters->fechadesde = $fechadesde;
                $filters->fechahasta = $fechahasta;
                $filters->horadesde = $horadesde;
                $filters->horahasta = $horahasta;
                $filters->local = $local;
                $response['data'] = Mage::helper('geocom_geoapi')->buildOrdersResponseDataFilters($filters);
            } catch (Exception $e) {
                $response = array('status' => "error", 'msg' => 'No existe una orden con ese identificador');
            }
            $this->getResponse()->setBody(json_encode($response));
            return;
        } else {
            $response = array('status' => "error", 'msg' => 'Usuario y contrase?a incorrectos');
            $this->getResponse()->setBody(json_encode($response));
            return;
        }
    }

    public function notFoundException() {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoRoute');
        }
    }

}
