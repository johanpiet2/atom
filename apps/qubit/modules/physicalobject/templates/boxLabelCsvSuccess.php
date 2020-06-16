<div id="preview-message">

  <?php echo ('Print preview') ?>
  
</div>

<h1 class="do-print"><?php echo sfConfig::get('app_ui_label_physicalobject') ?></h1>

<table class="sticky-enabled">
  <thead>
    <tr>
      <th>
        <?php echo __('Name') ?>
      </th><th>
        <?php echo __('Location') ?>
      </th><th>
        <?php echo __('Unique Identifier') ?>
      </th><th>
        <?php echo __('Description Title') ?>
      </th><th>
        <?php echo __('Period Covered') ?>
      </th><th>
        <?php echo __('Extend') ?>
      </th><th>
        <?php echo __('Finding Aids') ?>
      </th><th>
        <?php echo __('Forms') ?>
      </th><th>
        <?php echo __('Accrual Space') ?>
      </th><th>
        <?php echo __('Type') ?>
      </th>
    </tr>
  </thead><tbody>
    <?php foreach ($pager->getResults() as $item): ?>
      <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
          <?php echo $item->name ?>
        </td><td>
          <?php echo $item->location ?>
		</td><td>
          <?php echo $item->uniqueIdentifier ?>
        </td><td>
          <?php echo $item->descriptionTitle ?>
        </td><td>
          <?php echo $item->periodCovered ?>
        </td><td>
          <?php echo $item->extend ?>
        </td><td>
          <?php echo $item->findingAids ?>
        </td><td>
          <?php echo $item->accrualSpace ?>
        </td><td>
          <?php echo $item->forms ?>
        </td><td>
          <?php echo $item->type ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div id="result-count">
  <?php echo __('Showing %1% results', array('%1%' => $foundcount)) ?>
</div>
