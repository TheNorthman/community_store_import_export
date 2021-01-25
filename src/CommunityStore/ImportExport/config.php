<?php
namespace Concrete\Package\CommunityStoreImportExport\Src\CommunityStore\ImportExport;

use Concrete\Package\CommunityStore\Src\CommunityStore\Product\Product as StoreProduct;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductFile as StoreProductFile;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductGroup as StoreProductGroup;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductImage as StoreProductImage;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductList as StoreProductList;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductLocation as StoreProductLocation;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductUserGroup as StoreProductUserGroup;
use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductVariation\ProductVariation as StoreProductVariation;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductOption\ProductOption as StoreProductOption;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Group\Group as StoreGroup;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Group\GroupList as StoreGroupList;
// use Concrete\Package\CommunityStore\Src\Attribute\Key\StoreProductKey;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Tax\TaxClass as StoreTaxClass;

defined('C5_EXECUTE') or die('Access Denied.');

//
//  Config file for SOHO
//

class Config {

  public $name = 'SOHO';
  public $fileName = 'shopdata/InvenTable-2.csv';
  // Product

  public $productIndex = array(
    0 => 'pSKU',
    1 => 'pName',
    2 => '-not used-',
    3 => 'cLevel2',
    4 => 'cLevel1',
    5 => 'oSize',
    6 => 'pPrice',
    7 => 'gBrand',
    8 => 'oColor',
    9 => 'pQty'
  );

  public $defaultProductData = array(
    'pName' => '',
    'pSKU' => '',
    'pDesc' => '',
    'pDetail' => '',
    'pPrice' => '',
    'pSalePrice' => '',
    'pFeatured' => '',
    'pQty' => 0,
    'pQtyUnlim' => false,
    'pBackOrder' => false,
    'pNoQty' => false,
    'pTaxClass' => null,
    'pTaxable' => false,
    'pfID' => 0,
    'pActive' => true,
    'pCreateUserAccount' => false,
    'pShippable' => true,
    'pWidth' => '',
    'pHeight' => '',
    'pLength' => '',
    'pWeight' => '',
    'pNumberItems' => '',
    'pAutoCheckout' => false,
    'pExclusive' => false,
  );

  public $defaultProductWithOptionData = array(
    'pName' => '',
    'pSKU' => '',
    'pDesc' => '',
    'pDetail' => '',
    'pPrice' => '',
    'pSalePrice' => '',
    'pFeatured' => '',
    'pQty' => 0,
    'pQtyUnlim' => true,
    'pBackOrder' => false,
    'pNoQty' => false,
    'pTaxClass' => null,
    'pTaxable' => false,
    'pfID' => 0,
    'pActive' => true,
    'pCreateUserAccount' => false,
    'pShippable' => true,
    'pWidth' => '',
    'pHeight' => '',
    'pLength' => '',
    'pWeight' => '',
    'pNumberItems' => '',
    'pAutoCheckout' => false,
    'pExclusive' => false,
  );

  public $defaultVariationData = array(
    'pvSKU' => '',
    'pvPrice' => '',
    'pvSalePrice' => '',
    'pvQty' => 0,
    'pvQtyUnlim' => null,
    'pvfID' => null,
    'pvWeight' => '',
    'pvNumberItems' => '',
    'pvWidth' => '',
    'pvHeight' => '',
    'pvLength' => ''
  );

  public $optionNames = array(
    'oSize' => 'Størrelse',
    'oColor' => 'Farve'
  );

  public $optionSort = array(
    'oSize' => array(
      'S' => 0,
      'M' => 1,
      'L' => 2,
      'XL' => 3
    ),
    'oColor' => array(
      'sort' => 1,
      'grå' => 6,
      'taupe' => 18,
      'navy' => 28,
      'indigo' => 85
    )
  );
  public function getProductSKU($line) {
    $pSKU = $line[$this->getColumnByName('pSKU')];
    
    // Options: Size
    if (!empty($line[$this->getColumnByName('oSize')])) {
      $pSKU = substr($pSKU, 0, -(strlen($line[$this->getColumnByName('oSize')]) + 1));
    }

    // Option: Color
    if (!empty($line[$this->getColumnByName('oColor')])) {
      $pSKU = substr($pSKU, 0, -4);
    }
    return $pSKU;
  }

  public function getProduct($line) {
    return StoreProduct::getBySKU($this->getProductSKU($line));
  }

  public function getProductData($line, $product = null) {
    // TODO: if this is a product variation, then set data to default on the main product. ($this->defaultProductData)
    return array_merge($this->defaultProductData, array(
        'pSKU' => $line[$this->getColumnByName('pSKU')],
        'pName' => $line[$this->getColumnByName('pName')],
        'pPrice' => $line[$this->getColumnByName('pPrice')],
        'pQty' => $line[$this->getColumnByName('pQty')]
      ));
  }

  public function getProductWithOptionData($line, $product = null) {
    // TODO: if this is a product variation, then set data to default on the main product. ($this->defaultProductData)
    return array_merge($this->defaultProductWithOptionData, array(
        'pSKU' => $this->getProductSKU($line),
        'pName' => $line[$this->getColumnByName('pName')]
      ));
  }

  public function getVariation($line) {
    return StoreProductVariation::getBySKU($line[$this->getColumnByName('pSKU')]);
  }

  public function getVariationData($line) {
    return array_merge($this->defaultVariationData, array(
        'pvSKU' => $line[$this->getColumnByName('pSKU')],
        'pvName' => $line[$this->getColumnByName('pName')],
        'pvPrice' => $line[$this->getColumnByName('pPrice')],
        'pvQty' => $line[$this->getColumnByName('pQty')]
      ));
  }

  public function getOptionData($line) {
    $options = array();
    if ($line[$this->getColumnByName('oSize')]) {
      $options['oSize'] = array(
        'name' => $this->optionNames['oSize'],
        'items' => array( array(
          'name' => $line[$this->getColumnByName('oSize')],
          'sort' => $this->optionSort['oSize'][$line[$this->getColumnByName('oSize')]]
        ))
      );
    }
    if ($line[$this->getColumnByName('oColor')]) {
      $options['oColor'] = array(
        'name' => $this->optionNames['oColor'],
        'items' => array(array(
          'name' => $line[$this->getColumnByName('oColor')],
          'sort' => $this->optionSort['oColor'][$line[$this->getColumnByName('oColor')]]
        ))
      );
    }

    return $options;
  }

  public function hasOptions($line) {
    return ($line[$this->getColumnByName('oSize')] || $line[$this->getColumnByName('oColor')]? true : false);
  }

  public function getOptionName($type) {
    return $this->optionNames[$type];
  }

  public function getOptionReverseName($type) {
    return array_flip($this->optionNames)[$type];
  }

  public function getColumnByName($key) {
    return (in_array($key, $this->productIndex))? array_flip($this->productIndex)[$key] : '';
  }

}
