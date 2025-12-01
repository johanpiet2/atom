<?php use_helper('Date'); ?>

<h1><?php echo __('GRAP Compliance Check'); ?></h1>

<div class="compliance-summary">
  <h2><?php echo __('Compliance Summary'); ?></h2>
  
  <div class="row">
    <div class="col-md-3">
      <div class="stat-box">
        <h3><?php echo $complianceResults['total']; ?></h3>
        <p><?php echo __('Total Items'); ?></p>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stat-box stat-success">
        <h3><?php echo $complianceResults['compliant']; ?></h3>
        <p><?php echo __('Compliant'); ?></p>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stat-box stat-danger">
        <h3><?php echo $complianceResults['non_compliant']; ?></h3>
        <p><?php echo __('Non-Compliant'); ?></p>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stat-box">
        <h3><?php echo $complianceResults['compliance_rate']; ?>%</h3>
        <p><?php echo __('Compliance Rate'); ?></p>
      </div>
    </div>
  </div>
</div>

<?php if ($complianceResults['non_compliant'] > 0): ?>
  <div class="non-compliant-items">
    <h2><?php echo __('Non-Compliant Items'); ?></h2>
    
    <table class="table table-striped">
      <thead>
        <tr>
          <th><?php echo __('Identifier'); ?></th>
          <th><?php echo __('Title'); ?></th>
          <th><?php echo __('Compliance Issues'); ?></th>
          <th><?php echo __('Actions'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($nonCompliantItems as $item): ?>
          <?php
            $issues = isset($complianceResults['issues'][$item->id])
              ? $complianceResults['issues'][$item->id]
              : [];
            ?>
          <tr>
            <td><?php echo esc_entities($item->identifier); ?></td>
            <td><?php echo esc_entities($item->title); ?></td>
            <td>
              <?php if (!empty($issues)): ?>
                <ul class="compliance-issues">
                  <?php foreach ($issues as $issue): ?>
                    <li class="text-danger"><?php echo esc_entities($issue); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </td>
            <td>
              <a href="<?php echo url_for(['module' => 'informationobject', 'slug' => $item->identifier]); ?>" class="btn btn-sm btn-primary">
                <?php echo __('Edit'); ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="alert alert-success">
    <strong><?php echo __('Congratulations!'); ?></strong>
    <?php echo __('All items are GRAP compliant.'); ?>
  </div>
<?php endif; ?>

<div class="compliance-requirements">
  <h2><?php echo __('GRAP Compliance Requirements'); ?></h2>
  
  <p><?php echo __('For an item to be GRAP compliant, it must have:'); ?></p>
  
  <ul>
    <li><?php echo __('Recognition status set'); ?></li>
    <li><?php echo __('Measurement basis defined'); ?></li>
    <li><?php echo __('Initial recognition date recorded'); ?></li>
    <li><?php echo __('Initial recognition value recorded'); ?></li>
    <li><?php echo __('Acquisition method specified'); ?></li>
    <li><?php echo __('Asset class assigned'); ?></li>
    <li><?php echo __('GL account code linked'); ?></li>
    <li><?php echo __('If donated: Fair value at acquisition'); ?></li>
    <li><?php echo __('If using revaluation model: Revaluation date'); ?></li>
    <li><?php echo __('If not recognised: Reason provided'); ?></li>
  </ul>
</div>

<div class="actions">
  <a href="<?php echo url_for(['module' => 'grapReport', 'action' => 'index']); ?>" class="btn btn-secondary">
    <?php echo __('Back to Report'); ?>
  </a>
</div>
