<?php decorate_with('layout_2col'); ?>

<?php slot('title'); ?>
  <h1><?php echo __('Browse Donor Report'); ?></h1>
  <div style="margin-bottom: 1rem;">
    <a href="<?php echo url_for(['module' => 'reports', 'action' => 'reportSelect']); ?>" class="c-btn">
      <i class="fa fa-arrow-left"></i> <?php echo __("Back to Reports"); ?>
    </a>
  </div>
<?php end_slot(); ?>

<?php slot('sidebar'); ?>

  <section class="sidebar-widget">
    
    <h4><?php echo __('Filter options'); ?></h4>

    <?php echo $form->renderFormTag(url_for(['module' => 'reports', 'action' => 'reportDonor']), ['method' => 'get']); ?>

      <?php echo $form->renderHiddenFields(); ?>

      <div class="form-item">
        <label><?php echo __('Culture'); ?></label>
        <?php echo $form['culture']->render(); ?>
      </div>

      <div class="form-item">
        <label><?php echo __('Date start'); ?></label>
        <?php echo $form['dateStart']->render(); ?>
      </div>

      <div class="form-item">
        <label><?php echo __('Date end'); ?></label>
        <?php echo $form['dateEnd']->render(); ?>
      </div>

      <div class="form-item">
        <label><?php echo __('Date of'); ?></label>
        <?php echo $form['dateOf']->render(); ?>
      </div>

      <div class="form-item">
        <label><?php echo __('Results per page'); ?></label>
        <?php echo $form['limit']->render(); ?>
      </div>

      <section>
        <input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Search'); ?>"/>
      </section>

      <div style="margin-top: 1rem;">
        <button type="button" onclick="exportTableToCSV()" class="c-btn" style="width:100%;">
          <i class="fa fa-download"></i> <?php echo __('Export CSV'); ?>
        </button>
      </div>

    </form>

  </section>

<?php end_slot(); ?>

<?php slot('content'); ?>

  <?php if (isset($results) && count($results) > 0) { ?>
    
    <div class="alert alert-info">
      <?php echo __('Found %1% results', ['%1%' => $total]); ?>
    </div>

    <div style="margin-bottom: 1rem; font-size: 0.85rem;">
      <strong><?php echo __('Show/Hide Columns'); ?>:</strong><br/>
      <label><input type="checkbox" onclick="toggleColumn(0)" checked> <?php echo __('Name'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(1)" checked> <?php echo __('Contact Person'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(2)" checked> <?php echo __('Street Address'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(3)" checked> <?php echo __('City'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(4)" checked> <?php echo __('Region'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(5)" checked> <?php echo __('Country'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(6)" checked> <?php echo __('Postal Code'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(7)" checked> <?php echo __('Telephone'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(8)" checked> <?php echo __('Email'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(9)" checked> <?php echo __('Website'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(10)" checked> <?php echo __('Culture'); ?></label>
      <label><input type="checkbox" onclick="toggleColumn(11)" checked> <?php echo __('Created'); ?></label>
    </div>

    <script>
    function toggleColumn(colNum) {
      var table = document.getElementById('reportTable');
      var rows = table.getElementsByTagName('tr');
      
      for (var i = 0; i < rows.length; i++) {
        var cell = rows[i].cells[colNum];
        if (cell) {
          if (cell.style.display === 'none') {
            cell.style.display = '';
          } else {
            cell.style.display = 'none';
          }
        }
      }
    }

    function exportTableToCSV() {
      var table = document.getElementById('reportTable');
      var csv = [];
      var rows = table.querySelectorAll('tr');

      for (var i = 0; i < rows.length; i++) {
        var row = [];
        var cols = rows[i].querySelectorAll('td, th');
        
        for (var j = 0; j < cols.length; j++) {
          if (cols[j].style.display !== 'none') {
            var text = cols[j].innerText.replace(/"/g, '""');
            row.push('"' + text + '"');
          }
        }
        csv.push(row.join(','));
      }

      var csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
      var downloadLink = document.createElement('a');
      downloadLink.download = 'donor_report_' + new Date().getTime() + '.csv';
      downloadLink.href = window.URL.createObjectURL(csvFile);
      downloadLink.style.display = 'none';
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
    }
    </script>

    <div class="table-responsive" style="max-height: 600px; overflow: auto;">
      <table id="reportTable" class="table table-bordered table-striped table-sm">
        <thead>
          <tr>
            <th><?php echo __('Authorized Form Of Name'); ?></th>
            <th><?php echo __('Contact Person'); ?></th>
            <th><?php echo __('Street Address'); ?></th>
            <th><?php echo __('City'); ?></th>
            <th><?php echo __('Region'); ?></th>
            <th><?php echo __('Country'); ?></th>
            <th><?php echo __('Postal Code'); ?></th>
            <th><?php echo __('Telephone'); ?></th>
            <th><?php echo __('Email'); ?></th>
            <th><?php echo __('Website'); ?></th>
            <th><?php echo __('Culture'); ?></th>
            <th><?php echo __('Created'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $item) { ?>
            <tr>
              <td><?php echo isset($item->authorizedFormOfName) ? link_to($item->authorizedFormOfName, ['module' => 'donor', 'slug' => $item->id]) : '-'; ?></td>
              <td><?php echo $item->contactPerson ?? '-'; ?></td>
              <td><?php echo $item->streetAddress ?? '-'; ?></td>
              <td><?php echo $item->city ?? '-'; ?></td>
              <td><?php echo $item->region ?? '-'; ?></td>
              <td><?php echo $item->countryCode ?? '-'; ?></td>
              <td><?php echo $item->postalCode ?? '-'; ?></td>
              <td><?php echo $item->telephone ?? '-'; ?></td>
              <td><?php echo $item->email ?? '-'; ?></td>
              <td><?php echo $item->website ?? '-'; ?></td>
              <td><?php echo $item->culture ?? '-'; ?></td>
              <td><?php echo $item->createdAt ?? '-'; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

  <?php } else { ?>
    <div class="alert alert-warning">
      <?php echo __('No results found.'); ?>
    </div>
  <?php } ?>

<?php end_slot(); ?>
