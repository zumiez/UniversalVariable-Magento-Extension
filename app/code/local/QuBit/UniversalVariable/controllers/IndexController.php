<?php

  class QuBit_UniversalVariable_IndexController
    extends Mage_Core_Controller_Front_Action {
    
    public function testAction () {
      Mage::log('logging!!!!!!!!!!!!');
      echo "test index";
    }

    public function mamethodeAction () {
      echo "test mymethod";
    }
  }

?>