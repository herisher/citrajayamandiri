<?php
/**
 * ????????
 */
   
    
class Logic_Order extends Logic_Base {
    public function getFileForm() {
		$elem = new Zend_Form_Element_File("image_url");
		$form->addElement($elem);

        return $form;
    }
}