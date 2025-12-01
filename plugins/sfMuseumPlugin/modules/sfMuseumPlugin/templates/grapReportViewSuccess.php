<?php decorate_with('layout_1col'); ?>

<?php slot('title'); ?>
  <h1><?php echo __('GRAP 103 Heritage Asset Financial Report'); ?></h1>
  <?php if (QubitInformationObject::ROOT_ID != $resource->parentId) { ?>
    <?php echo include_partial('default/breadcrumb', ['resource' => $resource, 'objects' => $resource->getAncestors()->andSelf()->orderBy('lft')]); ?>
  <?php } ?>
<?php end_slot(); ?>

<?php slot('content'); ?>

<div class="grap-report">
  
  <!-- Report Header -->
  <div class="report-header" style="margin-bottom: 30px; padding: 20px; background: #f5f5f5; border: 1px solid #ddd;">
    <h2>GRAP 103 Heritage Asset Financial Report</h2>
    <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    <?php if (isset($repository)) { ?>
      <p><strong>Institution:</strong> <?php echo render_title($repository); ?></p>
    <?php } ?>
  </div>

  <!-- Object Identification -->
  <section class="report-section" style="margin-bottom: 30px;">
    <h3 style="background: #0066cc; color: white; padding: 10px;">1. Asset Identification</h3>
    <div style="padding: 15px; border: 1px solid #ddd;">
      <table class="table table-bordered">
        <tr>
          <th width="30%">Reference Code</th>
          <td><?php echo $resource->referenceCode; ?></td>
        </tr>
        <tr>
          <th>Title</th>
          <td><?php echo render_title($resource); ?></td>
        </tr>
        <?php if (!empty($creators)) { ?>
        <tr>
          <th>Creator(s)</th>
          <td><?php echo implode('; ', $creators); ?></td>
        </tr>
        <?php } ?>
        <?php if (isset($museumData['object_type'])) { ?>
        <tr>
          <th>Object Type</th>
          <td><?php echo $museumData['object_type']; ?></td>
        </tr>
        <?php } ?>
        <?php if (isset($museumData['classification'])) { ?>
        <tr>
          <th>Classification</th>
          <td><?php echo $museumData['classification']; ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </section>

  <?php if (isset($grapData) && !empty($grapData)) { ?>

  <!-- Recognition and Measurement -->
  <section class="report-section" style="margin-bottom: 30px;">
    <h3 style="background: #0066cc; color: white; padding: 10px;">2. Recognition and Measurement (GRAP 103 para 7-21)</h3>
    <div style="padding: 15px; border: 1px solid #ddd;">
      <table class="table table-bordered">
        <?php if (!empty($grapData['recognition_status'])) { ?>
        <tr>
          <th width="30%">Recognition Status</th>
          <td>
            <?php 
              $statusLabels = ['recognised' => 'Recognised', 'not_recognised' => 'Not Recognised'];
              echo isset($statusLabels[$grapData['recognition_status']]) ? $statusLabels[$grapData['recognition_status']] : $grapData['recognition_status'];
            ?>
          </td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['recognition_status_reason'])) { ?>
        <tr>
          <th>Reason (if not recognised)</th>
          <td><?php echo nl2br(htmlspecialchars($grapData['recognition_status_reason'])); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['measurement_basis'])) { ?>
        <tr>
          <th>Measurement Basis</th>
          <td>
            <?php 
              $basisLabels = ['cost_model' => 'Cost Model', 'revaluation_model' => 'Revaluation Model'];
              echo isset($basisLabels[$grapData['measurement_basis']]) ? $basisLabels[$grapData['measurement_basis']] : $grapData['measurement_basis'];
            ?>
          </td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['asset_class'])) { ?>
        <tr>
          <th>Asset Class</th>
          <td>
            <?php 
              $classLabels = ['heritage_asset' => 'Heritage Asset', 'operational_asset' => 'Operational Asset', 'investment' => 'Investment'];
              echo isset($classLabels[$grapData['asset_class']]) ? $classLabels[$grapData['asset_class']] : $grapData['asset_class'];
            ?>
          </td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['initial_recognition_date'])) { ?>
        <tr>
          <th>Initial Recognition Date</th>
          <td><?php echo $grapData['initial_recognition_date']; ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['initial_recognition_value'])) { ?>
        <tr>
          <th>Initial Recognition Value</th>
          <td>R <?php echo number_format($grapData['initial_recognition_value'], 2); ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </section>

  <!-- Acquisition Information -->
  <section class="report-section" style="margin-bottom: 30px;">
    <h3 style="background: #0066cc; color: white; padding: 10px;">3. Acquisition Information</h3>
    <div style="padding: 15px; border: 1px solid #ddd;">
      <table class="table table-bordered">
        <?php if (!empty($grapData['acquisition_method_grap'])) { ?>
        <tr>
          <th width="30%">Acquisition Method</th>
          <td>
            <?php 
              $methodLabels = ['purchase' => 'Purchase', 'donation' => 'Donation', 'transfer' => 'Transfer', 'exchange' => 'Exchange', 'other' => 'Other'];
              echo isset($methodLabels[$grapData['acquisition_method_grap']]) ? $methodLabels[$grapData['acquisition_method_grap']] : $grapData['acquisition_method_grap'];
            ?>
          </td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['cost_of_acquisition'])) { ?>
        <tr>
          <th>Cost of Acquisition</th>
          <td>R <?php echo number_format($grapData['cost_of_acquisition'], 2); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['fair_value_at_acquisition'])) { ?>
        <tr>
          <th>Fair Value at Acquisition</th>
          <td>R <?php echo number_format($grapData['fair_value_at_acquisition'], 2); ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </section>

  <!-- Financial Classification -->
  <section class="report-section" style="margin-bottom: 30px;">
    <h3 style="background: #0066cc; color: white; padding: 10px;">4. Financial Classification</h3>
    <div style="padding: 15px; border: 1px solid #ddd;">
      <table class="table table-bordered">
        <?php if (!empty($grapData['gl_account_code'])) { ?>
        <tr>
          <th width="30%">GL Account Code</th>
          <td><?php echo $grapData['gl_account_code']; ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['cost_center'])) { ?>
        <tr>
          <th>Cost Center</th>
          <td><?php echo $grapData['cost_center']; ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['fund_source'])) { ?>
        <tr>
          <th>Fund Source</th>
          <td><?php echo $grapData['fund_source']; ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </section>

  <!-- Depreciation -->
  <section class="report-section" style="margin-bottom: 30px;">
    <h3 style="background: #0066cc; color: white; padding: 10px;">5. Depreciation (GRAP 103 para 22-28)</h3>
    <div style="padding: 15px; border: 1px solid #ddd;">
      <table class="table table-bordered">
        <?php if (!empty($grapData['depreciation_policy'])) { ?>
        <tr>
          <th width="30%">Depreciation Policy</th>
          <td>
            <?php 
              $policyLabels = ['not_depreciated' => 'Not Depreciated (Heritage Asset)', 'depreciated' => 'Depreciated (Operational Asset)'];
              echo isset($policyLabels[$grapData['depreciation_policy']]) ? $policyLabels[$grapData['depreciation_policy']] : $grapData['depreciation_policy'];
            ?>
          </td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['depreciation_method'])) { ?>
        <tr>
          <th>Depreciation Method</th>
          <td>
            <?php 
              $depMethodLabels = ['straight_line' => 'Straight Line', 'reducing_balance' => 'Reducing Balance'];
              echo isset($depMethodLabels[$grapData['depreciation_method']]) ? $depMethodLabels[$grapData['depreciation_method']] : $grapData['depreciation_method'];
            ?>
          </td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['useful_life_years'])) { ?>
        <tr>
          <th>Useful Life</th>
          <td><?php echo $grapData['useful_life_years']; ?> years</td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['residual_value'])) { ?>
        <tr>
          <th>Residual Value</th>
          <td>R <?php echo number_format($grapData['residual_value'], 2); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['accumulated_depreciation'])) { ?>
        <tr>
          <th>Accumulated Depreciation</th>
          <td>R <?php echo number_format($grapData['accumulated_depreciation'], 2); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['initial_recognition_value']) && !empty($grapData['accumulated_depreciation'])) { ?>
        <tr>
          <th><strong>Carrying Amount (Net Book Value)</strong></th>
          <td><strong>R <?php echo number_format($grapData['initial_recognition_value'] - $grapData['accumulated_depreciation'], 2); ?></strong></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </section>

  <!-- Revaluation -->
  <section class="report-section" style="margin-bottom: 30px;">
    <h3 style="background: #0066cc; color: white; padding: 10px;">6. Revaluation (GRAP 103 para 29-39)</h3>
    <div style="padding: 15px; border: 1px solid #ddd;">
      <table class="table table-bordered">
        <?php if (!empty($grapData['last_revaluation_date'])) { ?>
        <tr>
          <th width="30%">Last Revaluation Date</th>
          <td><?php echo $grapData['last_revaluation_date']; ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['revaluation_amount'])) { ?>
        <tr>
          <th>Revaluation Amount</th>
          <td>R <?php echo number_format($grapData['revaluation_amount'], 2); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['valuation_method'])) { ?>
        <tr>
          <th>Valuation Method</th>
          <td>
            <?php 
              $valMethodLabels = ['market_approach' => 'Market Approach', 'cost_approach' => 'Cost Approach', 'income_approach' => 'Income Approach'];
              echo isset($valMethodLabels[$grapData['valuation_method']]) ? $valMethodLabels[$grapData['valuation_method']] : $grapData['valuation_method'];
            ?>
          </td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['valuer_credentials'])) { ?>
        <tr>
          <th>Valuer Credentials</th>
          <td><?php echo $grapData['valuer_credentials']; ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </section>

  <!-- GRAP 103 Disclosure Requirements -->
  <section class="report-section" style="margin-bottom: 30px;">
    <h3 style="background: #0066cc; color: white; padding: 10px;">7. GRAP 103 Disclosure Requirements (para 40-54)</h3>
    <div style="padding: 15px; border: 1px solid #ddd;">
      <table class="table table-bordered">
        <?php if (!empty($grapData['heritage_significance_rating'])) { ?>
        <tr>
          <th width="30%">Heritage Significance Rating</th>
          <td><?php echo $grapData['heritage_significance_rating']; ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['restrictions_use_disposal'])) { ?>
        <tr>
          <th>Restrictions on Use/Disposal</th>
          <td><?php echo nl2br(htmlspecialchars($grapData['restrictions_use_disposal'])); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['conservation_commitments'])) { ?>
        <tr>
          <th>Conservation Commitments</th>
          <td><?php echo nl2br(htmlspecialchars($grapData['conservation_commitments'])); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['insurance_coverage_required'])) { ?>
        <tr>
          <th>Insurance Coverage Required</th>
          <td>R <?php echo number_format($grapData['insurance_coverage_required'], 2); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['insurance_coverage_actual'])) { ?>
        <tr>
          <th>Insurance Coverage Actual</th>
          <td>R <?php echo number_format($grapData['insurance_coverage_actual'], 2); ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!empty($grapData['insurance_coverage_required']) && !empty($grapData['insurance_coverage_actual'])) { ?>
        <tr>
          <th>Insurance Gap</th>
          <td>
            R <?php echo number_format($grapData['insurance_coverage_required'] - $grapData['insurance_coverage_actual'], 2); ?>
            <?php if ($grapData['insurance_coverage_actual'] < $grapData['insurance_coverage_required']) { ?>
              <span style="color: red;"> (Under-insured)</span>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </section>

  <!-- Compliance Summary -->
  <section class="report-section" style="margin-bottom: 30px;">
    <h3 style="background: #0066cc; color: white; padding: 10px;">8. GRAP 103 Compliance Summary</h3>
    <div style="padding: 15px; border: 1px solid #ddd;">
      <p><strong>Report prepared in accordance with:</strong></p>
      <ul>
        <li>GRAP 103: Heritage Assets</li>
        <li>GRAP 17: Property, Plant and Equipment</li>
        <li>GRAP 21: Impairment of Non-cash-generating Assets</li>
      </ul>
      
      <p><strong>Compliance status:</strong></p>
      <ul>
        <li>Recognition criteria met: <?php echo (!empty($grapData['recognition_status']) && $grapData['recognition_status'] == 'recognised') ? '✓ Yes' : '✗ No'; ?></li>
        <li>Measurement basis documented: <?php echo !empty($grapData['measurement_basis']) ? '✓ Yes' : '✗ No'; ?></li>
        <li>Depreciation policy defined: <?php echo !empty($grapData['depreciation_policy']) ? '✓ Yes' : '✗ No'; ?></li>
        <li>Disclosure requirements met: <?php echo !empty($grapData['heritage_significance_rating']) ? '✓ Yes' : '✗ No'; ?></li>
      </ul>
    </div>
  </section>

  <?php } else { ?>
    <div class="alert alert-warning">
      <strong>No GRAP Financial Data Available</strong>
      <p>This heritage asset does not have GRAP 103 compliance data recorded. Please complete the financial information in the edit form.</p>
    </div>
  <?php } ?>

  <!-- Action Buttons -->
  <div class="form-actions" style="margin-top: 30px;">
    <?php echo link_to(__('← Back to Object'), [$resource, 'module' => 'sfMuseumPlugin'], ['class' => 'btn btn-secondary']); ?>
    <?php echo link_to(__('Edit GRAP Data'), [$resource, 'module' => 'sfMuseumPlugin', 'action' => 'edit'], ['class' => 'btn btn-primary']); ?>
    <button onclick="window.print();" class="btn btn-success">Print Report</button>
  </div>

</div>

<?php end_slot(); ?>

<style>
@media print {
  .form-actions { display: none; }
  .report-header { border: 2px solid #000 !important; }
  .report-section h3 { background: #000 !important; }
}
</style>