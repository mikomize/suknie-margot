<?php
class Zend_View_Helper_PrintLink extends Zend_View_Helper_Abstract {
  public function printLink($uri, $name, $attrs = array()) {
  	$attrs_string = '';
    if(preg_match('/^http:/', $uri, $matches)) {
      $full_uri = $uri;
      $attrs['target'] = '_blank'; 
    } else {
      $full_uri = $this->view->baseUrl() . $uri;
    }
  	foreach($attrs as $attr => $value) {
      $attrs_string .= ' ' . $attr . '="' . $value . '"';  	  
  	}
    echo '<a href="' . $full_uri . '"' . $attrs_string . '>' . $name . '</a>';
  }
}

?>