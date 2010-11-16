<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
      $this->_helper->layout->getLayoutInstance()->active_item = 'Strona Główna';
    }
    public function onasAction() {
      $this->_helper->layout->getLayoutInstance()->active_item = 'O nas';
    }
    public function kontaktAction() {
      $this->_helper->layout->getLayoutInstance()->active_item = 'Kontakt';
    }
}
?>
