<?php
class Default_Form_Login extends Zend_Form
{ 
  public function init()
  {
    $this->setMethod('post');
    
    $this->addElement('password', 'login', array(
      'label'      => 'Podaj hasło',
      'required'   => true,
    ));
    $this->addElement('submit', 'submit', array(
      'ignore'   => true,
      'label'    => 'Zaloguj',
    ));
  }
}