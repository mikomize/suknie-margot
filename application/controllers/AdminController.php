<?php
class AdminController extends Zend_Controller_Action 
{
  
  protected $pass = '12SuknieM2';
  
  protected function requireAdmin() 
  {
    if(!isset($_COOKIE['auth']) || $_COOKIE['auth'] != md5($this->pass) ) {
      return $this->_helper->redirector('login');
    }
  }
  
  public function init() 
  {
    $this->productsModel = new Default_Model_Products();
    $this->_helper->layout->setLayout('admin_layout');
  }
  
  public function indexAction() 
  {
    $this->requireAdmin();
    $products = $this->productsModel->getProducts();
    $groupedProducts = array();
    foreach($products as $product) {
      $groupedProducts[$product['category_id']][] = $product; 
    }
    $this->view->products = $groupedProducts; 
  }
  
  public function addAction() 
  {
    $this->requireAdmin();
    $request = $this->getRequest();
    $form    = new Default_Form_Product();
    if ($request->isPost()) {
      if ($form->isValid($request->getPost())) {
        $this->productsModel->addProduct($form->getValues());
        return $this->_helper->redirector('index');
      }
    }
    $this->view->form = $form;
  }
  
  public function deleteAction() 
  {
    $this->requireAdmin();
    $id = $this->_getParam('id');
    if(!is_null($id)) {
      $this->productsModel->deleteProduct($id);
    }
    return $this->_helper->redirector('index');
  }
  
  public function deletephotoAction() 
  {
    $this->requireAdmin();
    $id = $this->_getParam('id');
    if(!is_null($id)) {
      $this->productsModel->deletePhoto($id);
    }
    return $this->_helper->redirector('edit','admin',null,array('id'=>$id));
  }
  
  public function editAction() 
  {
    $this->requireAdmin();
    $request = $this->getRequest();
    $form    = new Default_Form_Product();
    $form->addElement('hidden', 'id');
    if ($request->isPost()) {
      if ($form->isValid($request->getPost())) {
        $this->productsModel->updateProduct($form->getValues());
        return $this->_helper->redirector('index');
      }
    } else {
      $id = $this->_getParam('id');
      if(!is_null($id)) {
        $product = $this->productsModel->getProduct($id, Default_Model_Products::WITH_TAGS | Default_Model_Products::WITH_SIZES | Default_Model_Products::WITH_PHOTOS);
        if($product) {
          $form->setDefaults($product);
          $this->view->product = $product;
        } else {
          return $this->_helper->redirector('index');
        }
      } else {
        return $this->_helper->redirector('index');
      }
    }
    $this->view->form = $form;
  }
  
  public function loginAction() 
  {
    $request = $this->getRequest();
    $form    = new Default_Form_Login();
    $this->view->bad_password = false;
    if ($request->isPost()) {
      if ($form->isValid($request->getPost())) {
        $pass = $form->getValue('login');
        if($pass == $this->pass) {
          setcookie('auth', md5($pass));
          return $this->_helper->redirector('index');
        } else {
          $this->view->bad_password = true;
        }
      }
    }
    $this->view->form = $form;
  }
}
?>