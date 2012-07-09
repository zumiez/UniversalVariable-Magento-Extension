<?php 
class QuBit_UniversalVariable_Model_Page_Observer {

  protected $_version     = "1.0.0";
  protected $_user        = null;
  protected $_product     = null;
  protected $_basket      = null;
  protected $_search      = null;
  protected $_transction  = null;

  /*
  * Returns Controller Name
  */
  protected function _getControllerName() {
    $request = Mage::app()->getFrontController()->getRequest();
    return $request->getControllerName();
  }

  /*
  * Creates  Block View
  */
  protected function _createBlock() {
    $layout = Mage::app()->getLayout();
    $block = $layout->createBlock('QuBit_UniversalVariable_Block_Uv','universal_variable_block');
  }


  public function getUser() {
    $logged_in_user = Mage::helper('customer')->getCustomer();
    $user = array();
    $user['email'] = $logged_in_user->getEmail();
    $user['id'] = $logged_in_user->getEntityId();
    $user['returning'] = $user['id'] ? true : false;
    $user['language'] = Mage::app()->getLocale()->getDefaultLocale();
    return $user;
  }

  public function _getPage() {
    $page = array();
    $page['category'] = $this->_getControllerName();
    return $page;
  }

  public function isConfirmationPage() {
    $transction = array();
  }

  public function isProductPage() {
    return $this->_getControllerName() == 'product';
  }

  public function isCartPage() {
    return $this->_getControllerName() == 'cart';
  }

  public function _getTransction() {
    // return $this->_getControllerName() == 'confirmation';
  }

  public function getCartModel() {
    $cart = array();
  }

  public function setProductModel($product) {
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

  public function _getSearch() {

  }

  public function setUniversalVariable($observer) {
    $name = $this->_getControllerName();
    // product
    // cart
    // index
    $user_model = json_encode($this->_getUser());
    // <script type="text/javascript">
    //   window.universal_variable = window.universal_variable || {};
    //   window.universal_variable.version = "1.0.0";
    //   window.universal_variable.user = {$user_model};
    //   console.log("{$name}")</script>
    $this->createBlock();

    return $this;
  }
}
?>