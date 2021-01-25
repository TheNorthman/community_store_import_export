<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<?php if ($controller->getTask() == "view") { ?>
  <div class="ccm-dashboard-header-buttons btn-group">
      <a href="<?= $view->url('/dashboard/store/import_export/products/')?>" class="btn btn-primary"><?= t('Manage Product Layouts')?></a>
  </div>

    <div class="ccm-dashboard-form-actions-wrapper">
      <div class="ccm-dashboard-form-actions">
        <a href="<?= $view->url('/dashboard/store/import_export/products/analyse')?>" class="pull-right btn btn-success"><?= t('Analyse')?></a>
      </div>
    </div>
<?php } ?>

<?php if ($controller->getTask() == "analyse" || $controller->getTask() == "import") { ?>

<pre>
<?php print_r($lines); ?>
</pre>

<pre>
<?php print_r($index); ?>
</pre>

  <form action="<?= URL::to('/dashboard/store/import_export/products/', ($controller->getTask() == "view")? "analyse" : "import"); ?>" method="post">

    <div class="ccm-pane-body">

      <?php if ($productLines) { ?>

        <h2><?= t('Product Data'); ?></h2>

        <table class="ccm-search-results-tablex productTable">
          <tr>
            <td><?= t('Type'); ?></td>
            <td><?= t('SKU'); ?></td>
            <td><?= t('Product Name'); ?></td>
            <td><?= t('pID'); ?></td>
            <td><?= t('pvID'); ?></td>
            <td><?= t('hasErrors'); ?></td>
            <td><?= t('Variation Count'); ?></td>
          </tr>
          <?php foreach ($productLines as $product) { ?>
            <!-- Product Line Overview -->
            <tr class="product_overview" style="color: <?php echo ($product['data']['hasError'])? 'red' : 'green'; ?>">
              <td><?= ($product['pvID'])? t('Variation') : ($product['data']['pID'])? t('Main') : ''; ?></td>
              <td><?= $product[0]; ?></td>
              <td><?= $product[1]; ?></td>
              <td><?= $product['data']['pID']; ?></td>
              <td><?= $product['data']['pvID']; ?></td>
              <td><?= $product['data']['hasError']; ?></td>
              <td><?= $product['data']['variationCount']; ?></td>
            </tr>
            <tr class="product_details" style="display: none;">
              <td colspan="7">

                <table class="ccm-search-results-tablex productTable">
                  <tr>
                    <?php foreach ($column as $title) { ?>
                    <th><?= $title; ?></th>
                    <?php } ?>
                  </tr>
                  <!-- Product Line Details -->
                  <tr style="color: <?php //echo ($product['hasError'])? 'red' : 'green'; ?>;">
                    <td><?= $product[0]; ?></td>
                    <td><?= $product[1]; ?></td>
                    <td><?= $product[2]; ?></td>
                    <td><?= $product[3]; ?></td>
                    <td><?= $product[4]; ?></td>
                    <td><?= $product[5]; ?></td>
                    <td><?= $product[6]; ?></td>
                    <td><?= $product[7]; ?></td>
                    <td><?= $product[8]; ?></td>
                    <td><?= $product[9]; ?></td>
                  </tr>
                </table>
                
              </td>
            </tr>

          <?php } ?>
        </table>
      <?php } ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
      <div class="ccm-dashboard-form-actions">
        <?php print $form->submit('submit', t('Import'), '', "pull-right btn btn-danger"); ?>
      </div>
    </div>

  </form>

  <style type="text/css">
    .productTable { width: 100%; }
    .productTable tr td, .productTable tr th { padding: 5px 10px; }
    .product_details { border: 1px solid #666; }
  </style>

  <script type="text/javascript">
    $(document).ready(function() {
      $('tr.product_overview').on('click', function(event) {
        if ($(this).next('tr').is(':visible')) {
          $(this).next('tr').hide();
        } else {
          $('tr.product_details').hide();
          $(this).next('tr').show(400);
        }
      });
    });
  </script>
<?php } ?>

<?php if ($controller->getTask() == "import") { ?>
<?php } ?>