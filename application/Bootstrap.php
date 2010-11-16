<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
  protected function _initAutoload()
  {
    $autoloader = new Zend_Application_Module_Autoloader(array(
      'namespace' => 'Default_',
      'basePath'  => dirname(__FILE__),
    ));
    return $autoloader;
  }
  
  protected function _initRoutes() {
    $router = Zend_Controller_Front::getInstance()->getRouter();
    $router->addRoute('onas', new Zend_Controller_Router_Route('onas',
      array(
        'controller' => 'index',
        'action'     => 'onas'
      )
    ));
    $router->addRoute('kontakt', new Zend_Controller_Router_Route('kontakt',
      array(
        'controller' => 'index',
        'action'     => 'kontakt'
      )
    ));
    $router->addRoute('adminDelete', new Zend_Controller_Router_Route('admin/delete/:id',
      array(
        'controller' => 'admin',
        'action'     => 'delete'
      )
    ));
    $router->addRoute('adminEdit', new Zend_Controller_Router_Route('admin/edit/:id',
      array(
        'controller' => 'admin',
        'action'     => 'edit'
      )
    ));
    $router->addRoute('adminPhotoDelete', new Zend_Controller_Router_Route('admin/delete_photo/:id',
      array(
        'controller' => 'admin',
        'action'     => 'deletePhoto'
      )
    ));
  }
}

