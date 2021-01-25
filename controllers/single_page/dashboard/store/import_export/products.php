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
use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductOption\ProductOptionItem as StoreProductOptionItem;
use Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductVariation\ProductVariationOptionItem as StoreProductVariationOptionItem;
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
    $this->lines = Csv::getFileLines($this->config->fileName);

    foreach ($this->lines as $line) {
      $this->product($line);
      print "---------------------------------------------------------------------------------<br>";
    }
    // if (!$this->hasErrors()) {
      // $this->doIndex($type);
      // $this->doProducts($type);
      // $this->doOptions($type);
      // $this->doOptionItems($type);
    exit;
      
      // $this->doCategories();
      // $this->doGroups($type);
      // $this->findGroups();
    // }

    // return $this->products;
  }
  
  // public function hasErrors() {
  //   foreach ($this->lines as $line) {
  //     // Get Main Product and/or Variation if exist
  //     $product = $this->config->getProduct($line);
  //     $variation = $this->config->getVariation($line);

  //     // Error checking
  //     if (!$product && $variation) {
  //       $this->report['error']['variation']['product do not exist'][] = array(
  //         'pvSKU' => $line[$this->config->getColumnByName('pSKU')],
  //         'pvID' => $variation->getID()
  //       );
  //       $this->report['line'][$this->config->getProductSKU($line)]['hasError'] = true;
  //     }
  //     if ($this->config->hasOptions($line) && $pTmp = StoreProduct::getBySKU($line[$this->config->getColumnByName('pSKU')])) {
  //       $this->report['error']['variation']['exist as product'][] = array(
  //         'pSKU' => $line[$this->config->getColumnByName('pSKU')],
  //         'pID' => $pTmp->getID()
  //       );
  //       $this->report['line'][$this->config->getProductSKU($line)]['hasError'] = true;
  //     }
  //   }
  //   return (bool) $this->report['error'];
  // }

  // public function doIndex($type) {

  //   foreach ($this->lines as $line) {
      
  //     $pSKU = $this->config->getProductSKU($line);
  //     $product = $this->config->getProduct($line);

  //     // Product
  //     $action = (is_object($product))? 'update' : 'add';
  //     $this->index[$pSKU]['product'] = array(
  //       'action' => $action,
  //       'data' => $this->config->getProductData($action, $line, $product)
  //     );

  //     // Options
  //     if ($product && ($options = $product->getOptions())) {
  //       foreach ($options as $option) {
  //         foreach ($option->getOptionItems() as $item) {
  //           $this->index[$pSKU]['options'][$option->getName()][] = $item->getName();
  //         }
  //       }
  //       $this->index[$pSKU]['debug']['oCount'] = count($options);
  //       $this->index[$pSKU]['debug']['iCount'] = count($option->getOptionItems());
  //     }


  //     $this->set('index', $this->index);
  //     // $this->set('productLines', $this->index);
  //   }


  //   // '415153': [
  //   //   product: [
  //   //     action: {1:'add', 'update', 'delete'}
  //   //     {,pName: 'title'}
  //   //     {,pPrice: 0}
  //   //     {,pQty: 0}
  //   //   ],
  //   //   options: [
  //   //     oColor: [
  //   //       action: {1:'add', 'update', 'delete'},
  //   //       name: "Farve"
  //   //     ],
  //   //     oSize [
  //   //       action: {1:'add', 'update', 'delete'},
  //   //       name: "Strørrelse"
  //   //     ]
  //   //   ],
  //   //   OptionItems: [
  //   //     oColor: [
  //   //       add: [ {'sort'}, {'taupe'} ],
  //   //       delete: [ {'brun'} ]
  //   //     ],
  //   //     oSize: [
  //   //       add: [ {'S'}, {'M'} ],
  //   //       delete: [ {'L'} ]
  //   //     ]
  //   //   ],
  //   //   Variations: [
  //   //     '415153-001-S': [
  //   //       action: {1:'add', 'update', 'delete'}
  //   //       {,pPrice: 399.00}
  //   //       {,pQty: 5}
  //   //     ],
  //   //     '415153-001-M': [
  //   //       action: {1:'add', 'update', 'delete'}
  //   //       {,pPrice: 399.00}
  //   //       {,pQty: 5}
  //   //     ]
  //   //   ]
  //   // ]

  // }

  // public function doOptions($type) {
  //   foreach ($this->lines as $line) {
  //     // Get Main Product and/or Variation if exist
  //     $product = $this->config->getProduct($line);
  //     $variation = $this->config->getVariation($line);

  //     // Error checking
  //     if (!$product && $variation) {
  //       $this->report['error']['variation']['product do not exist'][] = array(
  //         'pvSKU' => $line[$this->config->getColumnByName('pSKU')],
  //         'pvID' => $variation->getID()
  //       );
  //       $this->report['line'][$this->config->getProductSKU($line)]['hasError'] = true;
  //     }
  //     if ($this->config->hasOptions($line) && $pTmp = StoreProduct::getBySKU($line[$this->config->getColumnByName('pSKU')])) {
  //       $this->report['error']['variation']['exist as product'][] = array(
  //         'pSKU' => $line[$this->config->getColumnByName('pSKU')],
  //         'pID' => $pTmp->getID()
  //       );
  //       $this->report['line'][$this->config->getProductSKU($line)]['hasError'] = true;
  //     }

  //     if ($this->config->hasOptions($line)) {
  //       if ($product) {
  //         foreach ($product->getOptions() as $option) {
  //           $optionName =$this->config->getOptionReverseName($option->getName());
  //           $options[$this->config->getProductSKU($line)][$optionName] = true;
  //           foreach ($option->getOptionItems() as $optionItem) {
  //             if ($optionItem->getName() == $line[$this->config->getColumnByName($optionName)]) {
  //               $optionItems[$this->config->getProductSKU($line)][$optionName][$optionItem->getName()] = true;
  //             }
  //           }
  //         }
  //       } else {
  //         // Create Product
  //         if ($type == 'import') {
  //           $product = StoreProduct::saveProduct($this->getData('Product', $line));
  //           $product->setHasVariations(1);
  //           $product->save();
  //         } // Import
  //       }

  //       // oColor
  //       if (!$options[$this->config->getProductSKU($line)]['oColor']) {
  //         $this->report['stats']['options']['oColor']['create']['count']++;
  //         $this->report['stats']['options']['oColor']['create']['items'][] = array(
  //           'pSKU' => $line[$this->config->getColumnByName('pSKU')],
  //           'oColor' => $line[$this->config->getColumnByName('oColor')]
  //         );
  //         // Create new oColor option
  //         if ($type == 'import' && $product) { $option = StoreProductOption::add($product, $this->config->getOptionName('oColor'), $this->config->optionIndex['oColor']); // Import
  //         }
  //       }
  //       if ($optionItems[$this->config->getProductSKU($line)]['oColor'][$line[$this->config->getColumnByName('oColor')]]) {
  //         $this->report['stats']['options']['oColor']['items']['create']['count']++;
  //         $this->report['stats']['options']['oColor']['items']['create']['items'][] = array(
  //           'pSKU' => $line[$this->config->getColumnByName('pSKU')],
  //           'oColor' => $line[$this->config->getColumnByName('oColor')]
  //         );
  //         // Create new oColor option item
  //         if ($type == 'import' && $product) { StoreProductOptionItem::add($option, 'oColor', $this->config->getOptionName('oColor')); } // Import
  //       }

  //       // oSize
  //       if (!$options[$this->config->getProductSKU($line)]['oSize']) {
  //         $this->report['stats']['options']['oSize']['create']['count']++;
  //         $this->report['stats']['options']['oSize']['create']['items'][] = array(
  //           'pSKU' => $line[$this->config->getColumnByName('pSKU')],
  //           'oSize' => $line[$this->config->getColumnByName('oSize')]
  //         );
  //         // Create new oSize option
  //         // if ($type == 'import' && $product) { $this->addOption($product, $this->config->getOptionName['oSize'], $this->config->optionIndex['oSize']); } // Import
  //       }
  //       if (!$optionItems[$this->config->getProductSKU($line)]['oSize'][$line[$this->config->getColumnByName('oSize')]]) {
  //         $this->report['stats']['options']['oSize']['items']['create']['count']++;
  //         $this->report['stats']['options']['oSize']['items']['create']['items'][] = array(
  //           'pSKU' => $line[$this->config->getColumnByName('pSKU')],
  //           'oSize' => $line[$this->config->getColumnByName('oSize')]
  //         );
  //         // if ($type == 'import' && $product) { $this->addOptionItem($product, 'oSize', $this->config->getOptionName('oSize')); } // Import
  //       }

  //     }
  //     $product = $variation = null;
  //   }
  // }

  // public function getData($type, $line) {
  //   switch ($type) {

  //     case "Product":

  //       return array_merge($this->config->defaultProductData, array(
  //         'pName' => $line[$this->config->getColumnByName('pName')],
  //         'pSKU' => $this->config->getProductSKU($line),
  //         'pPrice' => $line[$this->config->getColumnByName('pPrice')],
  //         'pQty' => $line[$this->config->getColumnByName('pQty')],

  //         //'pID' => ($product = StoreProduct::getBySKU($this->config->getProductID($line)))? $product->getID() : ''
  //       ));
  //       break;

  //     case "Variation":
  //       break;
  //   }
  // }

  // public function addOption($product, $name) {
  //   $option = StoreProductOption::add($product, $name, $index);
  // }

  // public function addOptionItem($product, $option, $value) {

  // }

  // public function getColumnSelectBox($name, $selected) {

  //   $productFields = array('pID' => t('Product ID'), 'pName' => t('Name'), 'pSKU' => t('SKU'), 'pDesc' => t('Description'), 'pDetail' => t('Details'), 'pPrice' => t('Price'), 'pSalePrice' => t('Sales Price'), 'pFeatured' => t('Featured'), 'pQty' => t('Qantity'), 'pQtyUnlim' => t('Qantity Unlimited'), 'pBackOrder' => t('Allow Back Orders'), 'pNoQty' => t('No Qantity Selection'), 'pTaxClass' => t('Tax Class'), 'pTaxable' => t('Taxable'), 'pActive' => t('Active'), 'pCreateUserAccount' => t('pCreateUserAccount'), 'pShippable' => t('pShippable'), 'pWidth' => t('pWidth'), 'pHeight' => t('pHeight'), 'pLength' => t('pLength'), 'pWeight' => t('pWeight'), 'pNumberItems' => t('pNumberItems'), 'pAutoCheckout' => t('pAutoCheckout'), 'pExclusive' => t('pExclusive'));
  //   $optionFields = array('oSize' => 'Size', 'oColor' => 'Color');
  //   $categoryFields = array('cLevel1' => 'Level 1', 'cLevel2' => 'Level 2');

  //   foreach ($productFields as $key => $value) {
  //     $pFields .= '<option value="'. $key .'"'. ($key == $selected? ' selected="selected"': '').'>'. $value .'</option>';
  //   }
  //   foreach ($optionFields as $key => $value) {
  //     $oFields .= '<option value="'. $key .'"'. ($key == $selected? ' selected="selected"': '').'>'. $value .'</option>';
  //   }
  //   foreach ($categoryFields as $key => $value) {
  //     $cFields .= '<option value="'. $key .'"'. ($key == $selected? ' selected="selected"': '').'>'. $value .'</option>';
  //   }
  //   return '
  //     <select class="form-control">
  //       <option value="-1">'. t("Not Used") .'</option>
  //       <optgroup label="'. t("Product fields") .'">'. $pFields .'</optgroup>
  //       <optgroup label="'. t("Product Options") .'">'. $oFields .'</optgroup>
  //       <optgroup label="'. t("Product Category Level") .'">'. $cFields .'</optgroup>
  //     </select>';
  // }

  public function product($line) {
    $pSKU = $this->config->getProductSKU($line);
    $this->products[] = $pSKU;
    $product = StoreProduct::getBySKU($pSKU);

    $isProduct = is_object($product)? true : false;
    $hasOptions = $this->config->hasOptions($line)? true : false;

    print "isProduct: " . $isProduct . '<br>';
    print "hasOptions: " . $hasOptions . '<br>';



    if (!$isProduct && !$hasOptions) { // Product don't exist and don't have options

      // Add Product
      $data = $this->config->getProductData($line);
      $product = StoreProduct::saveProduct($data);
      print "Add Product ID: " . $product->getID() . '<br>';



    } else if ($isProduct && !$hasOptions) { // Product exist and has no options

      // Update Product
      $data = array_merge($this->config->getProductData($line), array('pID' => $product->getID()));
      $product = StoreProduct::saveProduct($data);
      print "Update Product ID: " . $product->getID() . '<br>';



      
    } else if (!$isProduct && $hasOptions) { // Product don't exist but have options

      // Add Product
      $data = $this->config->getProductWithOptionData($line);
      $product = new StoreProduct;
      $product->setDateAdded(new \DateTime());
      $product->setName($data['pName']);
      $product->setSKU($data['pSKU']);
      $product->setDescription($data['pDesc']);
      $product->setDetail($data['pDetail']);
      $product->setPrice($data['pPrice']);
      $product->setSalePrice($data['pSalePrice']);
      $product->setIsFeatured($data['pFeatured']);
      $product->setQty($data['pQty']);
      $product->setIsUnlimited($data['pQtyUnlim']);
      $product->setAllowBackOrder($data['pBackOrder']);
      $product->setNoQty($data['pNoQty']);
      $product->setTaxClass($data['pTaxClass']);
      $product->setIsTaxable($data['pTaxable']);
      $product->setImageID($data['pfID']);
      $product->setIsActive($data['pActive']);
      $product->setCreatesUserAccount($data['pCreateUserAccount']);
      $product->setIsShippable($data['pShippable']);
      $product->setWidth($data['pWidth']);
      $product->setHeight($data['pHeight']);
      $product->setLength($data['pLength']);
      $product->setWeight($data['pWeight']);
      $product->setNumberItems($data['pNumberItems']);
      $product->setAutoCheckout($data['pAutoCheckout']);
      $product->setIsExclusive($data['pExclusive']);
      $product->setHasVariations(1);
      $product->save();
      $product = StoreProduct::getByID($product->getID());
      print "HasVariation?: " . $product->hasVariations() . '<br>';
      print "Add Product ID: " . $product->getID() . '<br>';


      // Add Variation
      $data = $this->config->getVariationData($line);
      $variation = StoreProductVariation::add($product->getID(), $data);    
      print "Add Variation ID: " . $variation->getID() . '<br>';

      // Add Options
      foreach ($this->config->getOptionData($line) as $type => $optionData) {
        $sort = $type == 'oSize'? 0 : 1;
        $option = StoreProductOption::add($product, $optionData['name'], $sort);
        print "Add Option ID: " . $option->getID() . " (" . $optionData['name'] . ')<br>';

        // print_r($optionData['items'] ); exit;
        foreach ($optionData['items'] as $item) {
          $optionItem = StoreProductOptionItem::add($option, $item['name'], ($item['sort']? $item['sort'] : $item['name']));
          print "Add OptionItem ID: " . $optionItem->getID() . " (" .  $item['name'] . ')<br>';
        }

        $optionItems[] = $optionItem;
      }

      // Link Variation and OptionItems
      foreach ($optionItems as $optionItem) {
        $variationOptionItem = new StoreProductVariationOptionItem;
        $variationOptionItem->setVariation($variation);
        $variationOptionItem->setOption($optionItem);
        $variationOptionItem->save();
        print "Add Variation/OptionItem ID: " . $variationOptionItem->getID() . '<br>';
      }

      print "Variation Count: " . count(StoreProductVariation::getVariationsForProduct($product)) . '<br>';



    } else if ($isProduct && $hasOptions){ // Product exist and has options

      // Update Product
      $data = array_merge($this->config->getProductWithOptionData($line), array('pID' => $product->getID()));
      $product->setName($data['pName']);
      $product->save();
      print "Update Product ID: " . $product->getID() . '<br>';

      $productVariations = $product->getVariations();

      // Update Variation if exist
      $variation = $this->config->getVariation($line);
      if ($variation) {

        // Update Variation
        $data = array_merge($this->config->getVariationData($line), array('pvID' => $variation->getID()));
        $variation->setVariationSKU($data['pvSKU']);
        $variation->setVariationPrice($data['pvPrice']);
        $variation->setVariationQty($data['pvQty']);
        $variation->save();
        print "Update Variation ID: " . $variation->getID() . '<br>';

      } else {


        $options = StoreProductOption::getOptionsForProduct($product);
        
        foreach ($options as $option) {

          if ($option->getName() == $this->config->optionNames['oSize']) {

            foreach (StoreProductOptionItem::getOptionItemsForProductOption($option) as $optionItem) {
              if ($optionItem->getName() == $line[$this->config->getColumnByName('oSize')]) { $optionSize = $optionItem; }
            }

            if (!$optionSize) {
              // Add OptionItem
              $name = $line[$this->config->getColumnByName('oSize')];
              $sort = $this->config->optionSort['oSize'][$name]? $this->config->optionSort['oSize'][$name] : $name;
              $optionSize = StoreProductOptionItem::add($option, $name, $sort);
              print "Add OptionItem ID: " . $optionSize->getID() . " (" . $name . ')<br>';
            }
          }

          if ($option->getName() == $this->config->optionNames['oColor']) {

            foreach (StoreProductOptionItem::getOptionItemsForProductOption($option) as $optionItem) {
              if ($optionItem->getName() == $line[$this->config->getColumnByName('oColor')]) { $optionColor = $optionItem; }
            }

            if (!$optionColor) {
              // Add OptionItem
              $optionColor = StoreProductOptionItem::add($option, $line[$this->config->getColumnByName('oColor')], $this->config->optionSort['oColor'][$line[$this->config->getColumnByName('oColor')]]);
              print "Add OptionItem ID: " . $optionColor->getID() . " (" . $line[$this->config->getColumnByName('oColor')] . ')<br>';
            }
          }

        }

        // Add Variation
        $data = $this->config->getVariationData($line);
        $variation = StoreProductVariation::add($product->getID(), $data);    
        print "Add Variation ID: " . $variation->getID() . '<br>';

        // Link Variation and OptionItems
        foreach (array($optionSize, $optionColor) as $optionItem) {
          $variationOptionItem = new StoreProductVariationOptionItem;
          $variationOptionItem->setVariation($variation);
          $variationOptionItem->setOption($optionItem);
          $variationOptionItem->save();
          print "Add Variation/OptionItem ID: " . $variationOptionItem->getID() . '<br>';
        }

      }
      
    }

  }

}
