<?php
class Zend_View_Helper_GetMenuItems extends Zend_View_Helper_Abstract {
  private $menu_items = array(
    'Strona Główna' => '/index',
    'Galeria' => '/galeria',
    'O nas' => '/onas',
    'Kontakt' => '/kontakt',
    'Biżuteria Koriki' => 'http://kolorowekamienie.pl/'
  );
  public function getMenuItems() {
    return $this->menu_items;
  }
}
?>