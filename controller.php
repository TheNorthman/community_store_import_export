<?php
namespace Concrete\Package\CommunityStoreImportExport;

use Package;
use Page;
use SinglePage;
use View;
use AssetList;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends Package {

    protected $pkgHandle          = 'community_store_import_export';
    protected $appVersionRequired = '5.7.5';
    protected $pkgVersion         = '0.0.1';

    public function getPackageName() { return t("Community Store Import/Export"); }
    public function getPackageDescription() { return t("Import or export to and from Community Store"); }

    public function install() {
        $installed = Package::getInstalledHandles();
        if(!(is_array($installed) && in_array('community_store', $installed)) ) {
            throw new ErrorException(t('This package requires that Community Store be installed'));
        } else {
            $pkg = parent::install();

            $singlePage = Page::getByPath('/dashboard/store/import_export');
            if ($singlePage->error) {
                $singlePage = SinglePage::add('dashboard/store/import_export', $pkg);
                $singlePage->update(array('cName' => t('Import/Export'), 'cDescription' => t("Import/Export data")));
            }

            $singlePage = Page::getByPath('/dashboard/store/import_export/products/');
            if ($singlePage->error) {
                $singlePage = SinglePage::add('dashboard/store/import_export/products', $pkg);
                $singlePage->update(array('cName' => t('Products'), 'cDescription' => t("Import/Export product data")));
            }

        }
    }

}
