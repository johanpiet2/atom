<?php use_helper('Date'); ?>

<h1><?php echo __('GRAP Report - Heritage Asset Register'); ?></h1>

<div class="grap-statistics">
  <h2><?php echo __('Summary Statistics'); ?></h2>
  
  <div class="row">
    <div class="col-md-3">
      <div class="stat-box">
        <h3><?php echo $statistics['total']; ?></h3>
        <p><?php echo __('Total Items'); ?></p>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stat-box">
        <h3><?php echo number_format($statistics['total_initial_value'], 2); ?></h3>
        <p><?php echo __('Total Initial Value'); ?></p>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stat-box">
        <h3><?php echo number_format($statistics['total_carrying_amount'], 2); ?></h3>
        <p><?php echo __('Total Carrying Amount'); ?></p>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stat-box">
        <h3><?php echo count($statistics['by_asset_class']); ?></h3>
        <p><?php echo __('Asset Classes'); ?></p>
      </div>
    </div>
  </div>
</div>

<div class="grap-filters">
  <h2><?php echo __('Filter Results'); ?></h2>
  
  <form method="post" action="<?php echo url_for(['module' => 'grapReport', 'action' => 'index']); ?>">
    <div class="row">
      <div class="col-md-2">
        <?php echo $filterForm['asset_class']->renderLabel(); ?>
        <?php echo $filterForm['asset_class']->render(); ?>
      </div>
      
      <div class="col-md-2">
        <?php echo $filterForm['recognition_status']->renderLabel(); ?>
        <?php echo $filterForm['recognition_status']->render(); ?>
      </div>
      
      <div class="col-md-2">
        <?php echo $filterForm['measurement_basis']->renderLabel(); ?>
        <?php echo $filterForm['measurement_basis']->render(); ?>
      </div>
      
      <div class="col-md-2">
        <?php echo $filterForm['date_from']->renderLabel(); ?>
        <?php echo $filterForm['date_from']->render(); ?>
      </div>
      
      <div class="col-md-2">
        <?php echo $filterForm['date_to']->renderLabel(); ?>
        <?php echo $filterForm['date_to']->render(); ?>
      </div>
      
      <div class="col-md-2">
        <?php echo $filterForm['compliance_status']->renderLabel(); ?>
        <?php echo $filterForm['compliance_status']->render(); ?>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary"><?php echo __('Apply Filters'); ?></button>
        <a href="<?php echo url_for(['module' => 'grapReport', 'action' => 'index']); ?>" class="btn btn-secondary">
          <?php echo __('Clear Filters'); ?>
        </a>
        <a href="<?php echo url_for(['module' => 'grapReport', 'action' => 'export']) . '?' . http_build_query(['filters' => $filters]); ?>" class="btn btn-success">
          <?php echo __('Export CSV'); ?>
        </a>
      </div>
    </div>
  </form>
</div>

<div class="grap-results">
  <h2><?php echo __('Results'); ?> (<?php echo $results['total']; ?> <?php echo __('items'); ?>)</h2>
  
  <?php if (count($results['items']) > 0): ?>
    <table class="table table-striped sticky-enabled">
      <thead>
        <tr>
          <th><?php echo __('Identifier'); ?></th>
          <th><?php echo __('Title'); ?></th>
          <th><?php echo __('Asset Class'); ?></th>
          <th><?php echo __('Recognition'); ?></th>
          <th><?php echo __('Measurement'); ?></th>
          <th><?php echo __('Initial Value'); ?></th>
          <th><?php echo __('Carrying Amount'); ?></th>
          <th><?php echo __('GL Account'); ?></th>
          <th><?php echo __('Actions'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results['items'] as $item): ?>
          <tr>
            <td><?php echo esc_entities($item->identifier); ?></td>
            <td><?php echo esc_entities($item->title); ?></td>
            <td><?php echo esc_entities($item->asset_class); ?></td>
            <td><?php echo esc_entities($item->recognition_status); ?></td>
            <td><?php echo esc_entities($item->measurement_basis); ?></td>
            <td class="text-right"><?php echo number_format((float) $item->initial_recognition_value, 2); ?></td>
            <td class="text-right">
              <?php
                $carryingAmount = ($item->measurement_basis === 'revaluation_model' && $item->revaluation_amount)
                  ? (float) $item->revaluation_amount - (float) ($item->accumulated_depreciation ?? 0)
                  : (float) ($item->initial_recognition_value ?? 0) - (float) ($item->accumulated_depreciation ?? 0);
            echo number_format($carryingAmount, 2);
            ?>
            </td>
            <td><?php echo esc_entities($item->gl_account_code); ?></td>
            <td>
              <a href="<?php echo url_for(['module' => 'informationobject', 'slug' => $item->identifier]); ?>" class="btn btn-sm btn-info">
                <?php echo __('View'); ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    
    <?php if ($results['last_page'] > 1): ?>
      <div class="pagination">
        <?php for ($i = 1; $i <= $results['last_page']; $i++): ?>
          <?php if ($i == $results['current_page']): ?>
            <span class="current"><?php echo $i; ?></span>
          <?php else: ?>
            <a href="<?php echo url_for(['module' => 'grapReport', 'action' => 'index', 'page' => $i]) . '&' . http_build_query(['filters' => $filters]); ?>">
              <?php echo $i; ?>
            </a>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
    
  <?php else: ?>
    <p class="no-results"><?php echo __('No GRAP data found matching your criteria.'); ?></p>
  <?php endif; ?>
</div>

<div class="grap-links">
  <h2><?php echo __('Related Reports'); ?></h2>
  <ul>
    <li>
      <a href="<?php echo url_for(['module' => 'grapReport', 'action' => 'complianceCheck']); ?>">
        <?php echo __('GRAP Compliance Check'); ?>
      </a>
    </li>
    <li>
      <a href="<?php echo url_for(['module' => 'grapReport', 'action' => 'grap103Disclosure']); ?>">
        <?php echo __('GRAP 103 Heritage Assets Disclosure'); ?>
      </a>
    </li>
  </ul>
</div>
