<?php
namespace Concrete\Package\CommunityStoreImportExport\Controller\SinglePage\Dashboard\Store\ImportExport;

use Core;
use Concrete\Core\Page\Controller\PageController;

use Concrete\Package\CommunityStore\Src\CommunityStore\Product\Product as StoreProduct;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductFile as StoreProductFile;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductGroup as StoreProductGroup;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductImage as StoreProductImage;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductList as StoreProductList;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductLocation as StoreProductLocation;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductUserGroup as StoreProductUserGroup;
use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductVariation\ProductVariation as StoreProductVariation;
use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductOption\ProductOption as StoreProductOption;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Group\Group as StoreGroup;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Group\GroupList as StoreGroupList;
// use Concrete\Package\CommunityStore\Src\Attribute\Key\StoreProductKey;
// use Concrete\Package\CommunityStore\Src\CommunityStore\Tax\TaxClass as StoreTaxClass;

use Concrete\Package\CommunityStoreImportExport\Src\CommunityStore\ImportExport\Csv;
use Concrete\Package\CommunityStoreImportExport\Src\CommunityStore\ImportExport\Config;

defined('C5_EXECUTE') or die("Access Denied.");

class Products extends PageController {

  public $config;
  public $lines;
  public $report;

  public function view() {
    $this->set('pageTitle', t('Import Products'));
    $this->set('form', Core::make("helper/form"));
  }

  public function analyse() {

    $this->set('lines', $this->processFile('analyse'));
    $this->set('report', $this->report);

    $this->set('pageTitle', t('Analyse'));
    $this->set('form', Core::make("helper/form"));
  }

  public function import() {

    $this->set('lines', $this->processFile('import'));
    $this->set('report', $this->report);

    $this->set('pageTitle', t('Import'));
    $this->set('form', Core::make("helper/form"));
  }

  public function processFile($type) {

    // Get config object
    $this->config = new Config;

    $this->lines = Csv::getFileLines('shopdata/invenTable.csv');

    if (!$this->hasError()) {
      $this->doOptions($type);
      // $this->findCategories();
      // $this->findGroups();
    } 

    return $this->lines;
  }
  
  public function hasError() {
    foreach ($this->lines as $line) {
      // Get Main Product and/or Variation if exist
      $product = $this->config->getProduct($line);
      $variation = $this->config->getVariation($line);

      // Error checking
      if (!$product && $variation) {
        $this->report['error']['variation']['product do not exist'][] = array(
          'pvSKU' => $line[$this->config->getColumnByName('pSKU')],
          'pvID' => $variation->getID()
        );
        $this->report['line'][$this->config->getProductSKU($line)]['hasError'] = true;
      }
      if ($this->config->hasOptions($line) && $pTmp = StoreProduct::getBySKU($line[$this->config->getColumnByName('pSKU')])) {
        $this->report['error']['variation']['exist as product'][] = array(
          'pSKU' => $line[$this->config->getColumnByName('pSKU')],
          'pID' => $pTmp->getID()
        );
        $this->report['line'][$this->config->getProductSKU($line)]['hasError'] = true;
      }
    }
    return (bool) $this->report['error'];
  }

  public function doOptions($type) {
    foreach ($this->lines as $line) {
      // Get Main Product and/or Variation if exist
      $product = $this->config->getProduct($line);
      $variation = $this->config->getVariation($line);

      // Error checking
      if (!$product && $variation) {
        $this->report['error']['variation']['product do not exist'][] = array(
          'pvSKU' => $line[$this->config->getColumnByName('pSKU')],
          'pvID' => $variation->getID()
        );
        $this->report['line'][$this->config->getProductSKU($line)]['hasError'] = true;
      }
      if ($this->config->hasOptions($line) && $pTmp = StoreProduct::getBySKU($line[$this->config->getColumnByName('pSKU')])) {
        $this->report['error']['variation']['exist as product'][] = array(
          'pSKU' => $line[$this->config->getColumnByName('pSKU')],
          'pID' => $pTmp->getID()
        );
        $this->report['line'][$this->config->getProductSKU($line)]['hasError'] = true;
      }

      if ($this->config->hasOptions($line)) {
        if ($product) {
          foreach ($product->getOptions() as $option) {
            $optionName =$this->config->getOptionReverseName($option->getName());
            $options[$this->config->getProductSKU($line)][$optionName] = true;
            foreach ($option->getOptionItems() as $optionItem) {
              if ($optionItem->getName() == $line[$this->config->getColumnByName($optionName)]) {
                $optionItems[$this->config->getProductSKU($line)][$optionName][$optionItem->getName()] = true;
              }
            }
          }
        } else {
          // Create Product
          if ($type == 'import') {
            $product = StoreProduct::saveProduct($this->getData('Product', $line));
            $product->setHasVariations(1);
            $product->save();
          } // Import
        }

        // oColor
        if (!$options[$this->config->getProductSKU($line)]['oColor']) {
          $this->report['stats']['options']['oColor']['create']['count']++;
          $this->report['stats']['options']['oColor']['create']['items'][] = array(
            'pSKU' => $line[$this->config->getColumnByName('pSKU')],
            'oColor' => $line[$this->config->getColumnByName('oColor')]
          );
          // Create new oColor option
          if ($type == 'import' && $product) { $option = StoreProductOption::add($product, $this->config->getOptionName('oColor'), $this->config->optionIndex['oColor']); // Import
          }
        }
        if ($optionItems[$this->config->getProductSKU($line)]['oColor'][$line[$this->config->getColumnByName('oColor')]]) {
          $this->report['stats']['options']['oColor']['items']['create']['count']++;
          $this->report['stats']['options']['oColor']['items']['create']['items'][] = array(
            'pSKU' => $line[$this->config->getColumnByName('pSKU')],
            'oColor' => $line[$this->config->getColumnByName('oColor')]
          );
          // Create new oColor option item
          if ($type == 'import' && $product) { StoreProductOptionItem::add($option, 'oColor', $this->config->getOptionName('oColor')); } // Import
        }

        // oSize
        if (!$options[$this->config->getProductSKU($line)]['oSize']) {
          $this->report['stats']['options']['oSize']['create']['count']++;
          $this->report['stats']['options']['oSize']['create']['items'][] = array(
            'pSKU' => $line[$this->config->getColumnByName('pSKU')],
            'oSize' => $line[$this->config->getColumnByName('oSize')]
          );
          // Create new oSize option
          // if ($type == 'import' && $product) { $this->addOption($product, $this->config->getOptionName['oSize'], $this->config->optionIndex['oSize']); } // Import
        }
        if (!$optionItems[$this->config->getProductSKU($line)]['oSize'][$line[$this->config->getColumnByName('oSize')]]) {
          $this->report['stats']['options']['oSize']['items']['create']['count']++;
          $this->report['stats']['options']['oSize']['items']['create']['items'][] = array(
            'pSKU' => $line[$this->config->getColumnByName('pSKU')],
            'oSize' => $line[$this->config->getColumnByName('oSize')]
          );
          // if ($type == 'import' && $product) { $this->addOptionItem($product, 'oSize', $this->config->getOptionName('oSize')); } // Import
        }

      }
      $product = $variation = null;
    }
  }

  public function getData($type, $line) {
    switch ($type) {

      case "Product":

        return array_merge($this->config->defaultProductData, array(
          'pName' => $line[$this->config->getColumnByName('pName')],
          'pSKU' => $this->config->getProductSKU($line),
          'pPrice' => $line[$this->config->getColumnByName('pPrice')],
          'pQty' => $line[$this->config->getColumnByName('pQty')],

          //'pID' => ($product = StoreProduct::getBySKU($this->config->getProductID($line)))? $product->getID() : ''
        ));
        break;

      case "Variation":
        break;
    }
  }

  public function addOption($product, $name) {
    $option = StoreProductOption::add($product, $name, $index);
  }

  public function addOptionItem($product, $option, $value) {

  }

  public function getColumnSelectBox($name, $selected) {

    $productFields = array('pID' => t('Product ID'), 'pName' => t('Name'), 'pSKU' => t('SKU'), 'pDesc' => t('Description'), 'pDetail' => t('Details'), 'pPrice' => t('Price'), 'pSalePrice' => t('Sales Price'), 'pFeatured' => t('Featured'), 'pQty' => t('Qantity'), 'pQtyUnlim' => t('Qantity Unlimited'), 'pBackOrder' => t('Allow Back Orders'), 'pNoQty' => t('No Qantity Selection'), 'pTaxClass' => t('Tax Class'), 'pTaxable' => t('Taxable'), 'pActive' => t('Active'), 'pCreateUserAccount' => t('pCreateUserAccount'), 'pShippable' => t('pShippable'), 'pWidth' => t('pWidth'), 'pHeight' => t('pHeight'), 'pLength' => t('pLength'), 'pWeight' => t('pWeight'), 'pNumberItems' => t('pNumberItems'), 'pAutoCheckout' => t('pAutoCheckout'), 'pExclusive' => t('pExclusive'));
    $optionFields = array('oSize' => 'Size', 'oColor' => 'Color');
    $categoryFields = array('cLevel1' => 'Level 1', 'cLevel2' => 'Level 2');

    foreach ($productFields as $key => $value) {
      $pFields .= '<option value="'. $key .'"'. ($key == $selected? ' selected="selected"': '').'>'. $value .'</option>';
    }
    foreach ($optionFields as $key => $value) {
      $oFields .= '<option value="'. $key .'"'. ($key == $selected? ' selected="selected"': '').'>'. $value .'</option>';
    }
    foreach ($categoryFields as $key => $value) {
      $cFields .= '<option value="'. $key .'"'. ($key == $selected? ' selected="selected"': '').'>'. $value .'</option>';
    }
    return '
      <select class="form-control">
        <option value="-1">'. t("Not Used") .'</option>
        <optgroup label="'. t("Product fields") .'">'. $pFields .'</optgroup>
        <optgroup label="'. t("Product Options") .'">'. $oFields .'</optgroup>
        <optgroup label="'. t("Product Category Level") .'">'. $cFields .'</optgroup>
      </select>';
  }
}
