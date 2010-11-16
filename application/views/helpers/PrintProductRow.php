<?php
class Zend_View_Helper_PrintProductRow extends Zend_View_Helper_Abstract {
  public function printProductRow($product) {
    echo $product['name'];
    $this->view->printLink('/admin/edit/' . $product['id'], '[ edycja ]');
    $this->view->printLink('/admin/delete/' . $product['id'], '[ usuń ]');
  }
}
?>