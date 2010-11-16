<?php
class GaleriaController extends Zend_Controller_Action
{

    public function init()
    {
      $this->_helper->layout->getLayoutInstance()->active_item = 'Galeria';
    }

    public function indexAction()
    {
        $this->view->params = json_encode($_GET);
    }
    
    public function jsonAction() {
      $this->_helper->layout->disableLayout();
      $model = new Default_Model_Products();
      $products = $model->getProducts(Default_Model_Products::WITH_TAGS | Default_Model_Products::WITH_SIZES | Default_Model_Products::WITH_PHOTOS );
      $response = array('products' => $products, 'categories' => Default_Model_Products::$categoryList, 'tags' => Default_Model_Products::$tagsList, 'sizes' => Default_Model_Products::$sizesList);
      $this->view->jsonResponse = json_encode($response);
    }
    

}