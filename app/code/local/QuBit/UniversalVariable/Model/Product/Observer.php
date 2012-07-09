<?php 
class QuBit_UniversalVariable_Model_Product_Observer {

  protected $_product_model;
  protected function _product_universal_variable($product) {
    $product_model = array();
    $product_model['id'] = $product->getId();
    $product_model['sku_code'] = $product->getSku();
    $product_model['url'] = $product->getProductUrl();
    $product_model['name'] = $product->getName();
    $product_model['unit_price'] = intval($product->getPrice());
    $product_model['unit_sale_price'] = $product->getFinalPrice();
    $product_model['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
    $product_model['description'] = $product->getDescription();
    return $product_model;
  }

  public function getProductModel() {
    return $this->_product_model;
  }

  public function generate_universal_variable($observer) {
    $product = $observer->getEvent()->getProduct();
    $this->_product_model = $this->_product_universal_variable($product);
    
    $layout = Mage::app()->getLayout();
    $block = $layout->createBlock('QuBit_UniversalVariable_Block_Uv','universal_variable_block');
    
    return $this;
  }



}
?>