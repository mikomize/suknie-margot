<?php
class Zend_View_Helper_PrintPhoto extends Zend_View_Helper_Abstract {
  public function printPhoto($photoName) {
    echo '<img src="' . $this->view->baseUrl() . '/uploaded/thumbs/' . $photoName . '"></img>';
  }
}
?>