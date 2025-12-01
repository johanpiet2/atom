<?php use_helper('Date'); ?>

<h1><?php echo __('GRAP 103 - Heritage Assets Disclosure'); ?></h1>

<div class="disclosure-intro">
  <p>
    <?php echo __('This report provides the disclosure requirements as per GRAP 103 - Heritage Assets standard for public sector entities in South Africa.'); ?>
  </p>
</div>

<section class="disclosure-section">
  <h2><?php echo __('Heritage Assets Summary'); ?></h2>
  
  <div class="row">
    <div class="col-md-4">
      <div class="stat-box">
        <h3><?php echo $disclosureData['total_heritage_assets']; ?></h3>
        <p><?php echo __('Total Heritage Assets'); ?></p>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="stat-box">
        <h3><?php echo number_format((float) $disclosureData['total_value'], 2); ?></h3>
        <p><?php echo __('Total Value'); ?></p>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="stat-box">
        <h3><?php echo $disclosureData['not_recognised_count']; ?></h3>
        <p><?php echo __('Not Recognised in Statement'); ?></p>
      </div>
    </div>
  </div>
</section>

<section class="disclosure-section">
  <h2><?php echo __('Measurement Basis'); ?></h2>
  
  <table class="table table-bordered">
    <thead>
      <tr>
        <th><?php echo __('Measurement Basis'); ?></th>
        <th><?php echo __('Number of Items'); ?></th>
        <th><?php echo __('Total Value'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($disclosureData['by_measurement_basis'] as $row): ?>
        <tr>
          <td><?php echo esc_entities($row->measurement_basis ?? __('Not Set')); ?></td>
          <td><?php echo $row->count; ?></td>
          <td class="text-right"><?php echo number_format((float) $row->total_value, 2); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section class="disclosure-section">
  <h2><?php echo __('Reconciliation of Heritage Assets'); ?></h2>
  
  <table class="table table-bordered">
    <tbody>
      <tr>
        <th><?php echo __('Opening Balance'); ?></th>
        <td class="text-right"><?php echo number_format($reconciliationData['opening_balance'], 2); ?></td>
      </tr>
      <tr>
        <th><?php echo __('Additions'); ?></th>
        <td class="text-right"><?php echo number_format($reconciliationData['additions'], 2); ?></td>
      </tr>
      <tr>
        <th><?php echo __('Revaluations'); ?></th>
        <td class="text-right"><?php echo number_format($reconciliationData['revaluations'], 2); ?></td>
      </tr>
      <tr>
        <th><?php echo __('Depreciation'); ?></th>
        <td class="text-right">(<?php echo number_format($reconciliationData['depreciation'], 2); ?>)</td>
      </tr>
      <tr>
        <th><?php echo __('Impairments'); ?></th>
        <td class="text-right">(<?php echo number_format($reconciliationData['impairments'], 2); ?>)</td>
      </tr>
      <tr>
        <th><?php echo __('Disposals'); ?></th>
        <td class="text-right">(<?php echo number_format($reconciliationData['disposals'], 2); ?>)</td>
      </tr>
      <tr class="total-row">
        <th><?php echo __('Closing Balance'); ?></th>
        <td class="text-right"><strong><?php echo number_format($reconciliationData['closing_balance'], 2); ?></strong></td>
      </tr>
    </tbody>
  </table>
</section>

<section class="disclosure-section">
  <h2><?php echo __('Balance Sheet Summary by Asset Class'); ?></h2>
  
  <table class="table table-bordered">
    <thead>
      <tr>
        <th><?php echo __('Asset Class'); ?></th>
        <th><?php echo __('GL Account'); ?></th>
        <th><?php echo __('Items'); ?></th>
        <th><?php echo __('Initial Value'); ?></th>
        <th><?php echo __('Depreciation'); ?></th>
        <th><?php echo __('Carrying Amount'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($balanceSheetData['by_class'] as $row): ?>
        <tr>
          <td><?php echo esc_entities($row->asset_class ?? __('Unclassified')); ?></td>
          <td><?php echo esc_entities($row->gl_account_code); ?></td>
          <td><?php echo $row->item_count; ?></td>
          <td class="text-right"><?php echo number_format((float) $row->total_initial_value, 2); ?></td>
          <td class="text-right"><?php echo number_format((float) $row->total_depreciation, 2); ?></td>
          <td class="text-right"><?php echo number_format((float) $row->total_carrying_amount, 2); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr class="total-row">
        <th colspan="2"><?php echo __('Totals'); ?></th>
        <th><?php echo $balanceSheetData['totals']['total_items']; ?></th>
        <th class="text-right"><?php echo number_format($balanceSheetData['totals']['total_initial_value'], 2); ?></th>
        <th class="text-right"><?php echo number_format($balanceSheetData['totals']['total_depreciation'], 2); ?></th>
        <th class="text-right"><?php echo number_format($balanceSheetData['totals']['total_carrying_amount'], 2); ?></th>
      </tr>
    </tfoot>
  </table>
</section>

<?php if ($disclosureData['items_with_restrictions'] > 0): ?>
<section class="disclosure-section">
  <h2><?php echo __('Restrictions on Use or Disposal'); ?></h2>
  
  <p>
    <?php echo __('%1% heritage assets have restrictions on their use or disposal.', ['%1%' => $disclosureData['items_with_restrictions']]); ?>
  </p>
  
  <table class="table table-bordered">
    <thead>
      <tr>
        <th><?php echo __('Item'); ?></th>
        <th><?php echo __('Restrictions'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($disclosureData['restrictions'] as $item): ?>
        <tr>
          <td><?php echo esc_entities($item->information_object_id); ?></td>
          <td><?php echo esc_entities($item->restrictions_use_disposal); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php endif; ?>

<?php if ($disclosureData['items_with_conservation_commitments'] > 0): ?>
<section class="disclosure-section">
  <h2><?php echo __('Conservation Commitments'); ?></h2>
  
  <p>
    <?php echo __('%1% heritage assets have conservation commitments.', ['%1%' => $disclosureData['items_with_conservation_commitments']]); ?>
  </p>
  
  <table class="table table-bordered">
    <thead>
      <tr>
        <th><?php echo __('Item'); ?></th>
        <th><?php echo __('Conservation Commitments'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($disclosureData['conservation_commitments'] as $item): ?>
        <tr>
          <td><?php echo esc_entities($item->information_object_id); ?></td>
          <td><?php echo esc_entities($item->conservation_commitments); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php endif; ?>

<?php if ($disclosureData['not_recognised_count'] > 0): ?>
<section class="disclosure-section">
  <h2><?php echo __('Heritage Assets Not Recognised'); ?></h2>
  
  <p>
    <?php echo __('The following %1% heritage assets are not recognised in the statement of financial position:', ['%1%' => $disclosureData['not_recognised_count']]); ?>
  </p>
  
  <table class="table table-bordered">
    <thead>
      <tr>
        <th><?php echo __('Item'); ?></th>
        <th><?php echo __('Reason for Non-Recognition'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($disclosureData['not_recognised_items'] as $item): ?>
        <tr>
          <td><?php echo esc_entities($item->information_object_id); ?></td>
          <td><?php echo esc_entities($item->recognition_status_reason ?? __('Not specified')); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php endif; ?>

<div class="actions">
  <a href="<?php echo url_for(['module' => 'grapReport', 'action' => 'index']); ?>" class="btn btn-secondary">
    <?php echo __('Back to Report'); ?>
  </a>
  <a href="<?php echo url_for(['module' => 'grapReport', 'action' => 'complianceCheck']); ?>" class="btn btn-primary">
    <?php echo __('Run Compliance Check'); ?>
  </a>
</div>
