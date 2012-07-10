<?php 
class QuBit_UniversalVariable_Model_Page_Observer {

  protected $_version     = "1.0.0";
  protected $_user        = null;
  protected $_page        = null;
  protected $_basket      = null;
  protected $_product     = null;
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


  /*
   * Sets user information
   */
  public function _setUser() {
    $user = Mage::helper('customer')->getCustomer();
    $this->_user = array();
    $this->_user['email'] = $user->getEmail();
    $this->_user['id'] = $user->getEntityId();
    $this->_user['returning'] = $this->_user['id'] ? true : false;
    $this->_user['language'] = Mage::app()->getLocale()->getDefaultLocale();
  }

  /*
   * Sets current page information
   */
  public function _setPage() {
    $this->_page = array();
    $this->_page['category'] = $this->_getControllerName();
  }


  public function _setTranscation() {
    // return $this->_getControllerName() == 'confirmation';
  }

  public function _setProduct() {
    $product  = Mage::registry('current_product');
    if ($product) {
      $this->_product = $this->_getProductModel($product);;
    }
  }

  public function _getProuctStock($product) {
    return (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
  }

  public function _getCurrency() {
    return Mage::app()->getStore()->getCurrentCurrencyCode();
  }

  public function _getProductModel($product) {
    $product_model = array();
    $product_model['id']       = $product->getId();
    $product_model['sku_code'] = $product->getSku();
    $product_model['url']      = $product->getProductUrl();
    $product_model['name']     = $product->getName();
    $product_model['unit_price']      = $product->getPrice();
    $product_model['unit_sale_price'] = $product->getFinalPrice();
    $product_model['currency']        = $this->_getCurrency();
    $product_model['description']     = $product->getDescription();
    $product_model['stock']           = $this->_getProuctStock($product);
    return $product_model;
  }

  /*
   * Sets basket information
   */
  public function _setBasket() {
    $cart = Mage::getSingleton('checkout/cart');
    $quote = $cart->getQuote();
    $line_items = array();
    $basket = array();
    $items = $quote->getAllItems();
    $subTotal = $quote->getSubtotal();
    $grandTotal = $quote->getGrandTotal();

    foreach($items as $item) {
      $litem_model = array();
      $litem_model['product'] = $this->_getProductModel($item->getProduct());
      $litem_model['quantity'] = $item->getQty();
      // is this correct?
      $litem_model['subtotal'] = $item->getCalculationPrice() * $item->getQty();
      array_push($line_items, $litem_model);
    }
    $basket['id'] = Mage::getSingleton('checkout/session')->getQuoteId();
    $basket['currency'] = $this->_getCurrency();
    $basket['subtotal'] = $subTotal;
    // TODO: subtotal_incluce_tax
    // TODO: tax
    // TODO: shipping_cost
    // TODO: shipping_method
    $basket['total'] = $grandTotal;
    $basket['line_items'] = $line_items;
    $this->_basket = $basket;
  }

  public function setUniversalVariable($observer) {
    $this->_setUser();
    $this->_setPage();
    $this->_setProduct();
    $this->_setBasket();

    // product
    // cart
    // index
    // result
    // $user_model = json_encode($this->_getUser());
    // <script type="text/javascript">
    //   window.universal_variable = window.universal_variable || {};
    //   window.universal_variable.version = "1.0.0";
    //   window.universal_variable.user = {$user_model};
    //   console.log("{$name}")</script>
    $this->_createBlock();

    return $this;
  }


  public function getVersion() {
    return $this->_version;
  }

  public function getUser() {
    return $this->_user;
  }

  public function getPage() {
    return $this->_page;
  }

  public function getProduct() {
    return $this->_product;
  }

  public function getBasket() {
    return $this->_basket;
  }

  public function getTransaction() {
    return null;
  }

  public function getListing() {
    return null;
  }
}
?>