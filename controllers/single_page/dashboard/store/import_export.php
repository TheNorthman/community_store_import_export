<?php
namespace Concrete\Package\CommunityStoreImportExport\Controller\SinglePage\Dashboard\Store;

use Core;
use Concrete\Core\Page\Controller\PageController;

defined('C5_EXECUTE') or die("Access Denied.");

class ImportExport extends PageController {

  public function view() {
    $this->set('pageTitle', t('Import/Export'));
  }

}
