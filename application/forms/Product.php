<?php
class Default_Form_Product extends Zend_Form
{
  public function init()
  {
    $model = new Default_Model_Products();
    $this->setMethod('post');
    $this->setAttrib('enctype', 'multipart/form-data');
    
    $this->addElement('text', 'name', array(
      'label'      => 'Nazwa sukienki',
      'required'   => true,
    ));
    $this->addElement('select', 'category_id', array(
      'label'      => 'Kategoria',
      'required'   => true,
      'multiOptions' => Default_Model_Products::$categoryList
    ));
    $this->addElement('multiCheckbox', 'tags', array(
      'label'      => 'Tagi',
      'multiOptions' => Default_Model_Products::$tagsList
    ));
    
    $this->addElement('multiCheckbox', 'sizes', array(
      'label'      => 'Rozmiary',
      'multiOptions' => Default_Model_Products::$sizesList
    ));

    $this->addElement('text', 'price', array(
      'label'      => 'Cena (w groszach)',
      'required'   => true,
    ));
    $this->addElement('textarea', 'description', array(
      'label'      => 'Opis :',
      'rows'       => 6
    ));
    $this->addElement('submit', 'submit', array(
      'ignore'   => true,
      'label'    => 'Wyślij',
    ));
    
    $this->addElement('hidden', 'primary_photo_id');
    $file = new Zend_Form_Element_File('photo');
    $file->setLabel('Dodaj zdjęcie:')
         ->setDestination('uploaded')
         ->setRequired(false);
    $file->addValidator('Extension', false, 'jpg');
    $file->addValidator('Size', false, 204800);
    $this->addElement($file, 'photo');
  }
}
