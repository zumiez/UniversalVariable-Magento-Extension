<?php 
class QuBit_UniversalVariable_Model_Page_Observer {

  protected $_version     = "1.0.0";
  protected $_user        = null;
  protected $_page        = null;
  protected $_basket      = null;
  protected $_product     = null;
  protected $_search      = null;
  protected $_transaction = null;
  protected $_listing     = null;

  protected function _getRequest() {
    return Mage::app()->getFrontController()->getRequest();
  }

  /*
  * Returns Controller Name
  */
  protected function _getControllerName() {
    return $this->_getRequest()->getControllerName();
  }

  protected function _getActionName() {
    return $this->_getRequest()->getActionName();
  }

  protected function _getModuleName() {
    return $this->_getRequest()->getModuleName();
  }

  protected function _getRouteName() {
    return $this->_getRequest()->getRouteName();
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
    $this->_page['category'] = $this->_getModuleName().'/'.$this->_getControllerName().'/'.$this->_getActionName();
  }

  public function _getAddress($address) {
    $billing = array();
    if ($address) {
      $billing['name']     = $address->getName();
      $billing['address']  = $address->getStreetFull();
      $billing['city']     = $address->getCity();
      $billing['postcode'] = $address->getPostcode();
      $billing['country']  = $address->getCountry();
    }
    // TODO: $billing['state']
    return $billing;
  }

  public function _setTranscation() {
    // default controllerName is "onepage"
    // relax the check, only check if contains checkout
    // somecheckout systems has different prefix/postfix,
    // but all contains checkout
    $isCheckout = strpos($this->_getModuleName(), 'checkout') !== false;

    if ($isCheckout && $this->_getActionName() == "success") {
      $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
      if ($orderId) {
        $transaction = array();
        $order = Mage::getModel('sales/order')->load($orderId);
        $items = $order->getAllItems();
        $line_items      = $this->_getInvoicedLineItems($items);
        $shippingAddress = $order->getShippingAddress();
        $billingAddress  = $order->getBillingAddress();

        $transaction['order_id']        = $order->getIncrementId();
        $transaction['currency']        = $this->_getCurrency();
        $transaction['subtotal']        = (float) $order->getSubtotal();
        // TODO: subtotal_include_tax
        $transaction['total']           = (float) $order->getGrandTotal();
        $transaction['voucher']         = $order->getCouponCode();
        // TODO: voucher_discount
        $transaction['tax']             = (float) $order->getTax();
        $transaction['shipping_cost']   = (float) $order->getShippingAmount();
        $transaction['shipping_method'] = $order->getShippingMethod();
        $transaction['billing']         = $this->_getAddress($billingAddress);
        $transaction['delivery']        = $this->_getAddress($shippingAddress);
        $transaction['line_items']      = $line_items;
        $this->_transaction             = $transaction;
      }
    }
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
    $product_model['unit_price']      = (float) $product->getPrice();
    $product_model['unit_sale_price'] = (float) $product->getFinalPrice();
    $product_model['currency']        = $this->_getCurrency();
    $product_model['description']     = $product->getDescription();
    $product_model['stock']           = $this->_getProuctStock($product);
    return $product_model;
  }

  public function _getLineItems($items) {
    $line_items = array();
    foreach($items as $item) {
      // backwards compaibility, getProduct() is not supported in older version
      $productId = $item->getProductId();
      $product   =  Mage::getModel('catalog/product')->load($productId);

      if ($product && $product->isVisibleInSiteVisibility()) {
        $litem_model = array();
        $litem_model['product'] = $this->_getProductModel($product);
        $litem_model['quantity'] = $item->getQty();
        $litem_model['subtotal'] = (float) $item->getRowTotalInclTax();
        array_push($line_items, $litem_model);
      }
    }
    return $line_items;
  }

  public function _getInvoicedLineItems($items) {
    $line_items = array();
    foreach($items as $item) {
      // backwards compaibility, getProduct() is not supported in older version
      $productId = $item->getProductId();
      $product   =  Mage::getModel('catalog/product')->load($productId);
      
      if ($product && $product->isVisibleInSiteVisibility()) {
        $litem_model = array();
        $litem_model['product'] = $this->_getProductModel($product);
        $litem_model['quantity'] = (float) $item->getQtyOrdered();
        $litem_model['subtotal'] = (float) $item->getRowTotalInclTax();
        array_push($line_items, $litem_model);
      }
    }
    return $line_items;
  }

  public function _setListing() {
  }

  /*
   * Sets basket information
   */
  public function _setBasket() {
    $cart = Mage::getSingleton('checkout/cart');
    $quote = $cart->getQuote();
    $basket = array();
    $items = $quote->getAllItems();
    $subTotal = $quote->getSubtotal();
    $grandTotal = $quote->getGrandTotal();
    $line_items = $this->_getLineItems($items);
    
    $basket['id'] = Mage::getSingleton('checkout/session')->getQuoteId();
    $basket['currency'] = $this->_getCurrency();
    $basket['subtotal'] = (float) $subTotal;
    // TODO: subtotal_incluce_tax
    // TODO: tax
    // TODO: shipping_cost
    // TODO: shipping_method
    $basket['total'] = (float) $grandTotal;
    $basket['line_items'] = $line_items;
    $this->_basket = $basket;
  }

  public function setUniversalVariable($observer) {
    $this->_setUser();
    $this->_setPage();
    $this->_setProduct();
    $this->_setListing();
    $this->_setBasket();
    $this->_setTranscation();
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
    return $this->_transaction;
  }

  public function getListing() {
    return $this->_listing;
  }
}
?>