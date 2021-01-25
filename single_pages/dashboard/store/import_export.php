<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<div class="ccm-pane-body">

  <div class="col-xs-6">
    <h2><?= t('Import'); ?></h2>
    <ul style="list-style-type: none;">
      <li><a href="<?= $view->url('/dashboard/store/import_export/products/')?>" class=""><?= t('Products')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/orders/import/')?>" class=""><?= t('Orders')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/settings/import/')?>" class=""><?= t('Settings')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/discounts/import/')?>" class=""><?= t('Discounts')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/reports/import/')?>" class=""><?= t('Reports')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/layouts/import/')?>" class=""><?= t('Import/Export Layouts')?></a></li>
    </ul>
  </div>

  <div class="col-xs-6">
    <h2><?= t('Export'); ?></h2>
    <ul style="list-style-type: none;">
      <li><a href="<?= $view->url('/dashboard/store/import_export/products/export/')?>" class=""><?= t('Products')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/orders/export/')?>" class=""><?= t('Orders')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/settings/export/')?>" class=""><?= t('Settings')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/discounts/export/')?>" class=""><?= t('Discounts')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/reports/export/')?>" class=""><?= t('Reports')?></a></li>
      <li><a href="<?= $view->url('/dashboard/store/import_export/layouts/export/')?>" class=""><?= t('Import/Export Layouts')?></a></li>
    </ul>
  </div>

</div>

