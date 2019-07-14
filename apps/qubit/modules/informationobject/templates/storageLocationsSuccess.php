<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <style>
    table, thead {
      border-collapse: collapse;
      border: 1px solid black;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 2px;
    }
  </style>
</head>

<body>
  <h1 class="do-print"><?php echo $this->i18n->__('Physical storage locations') ?></h1>

  <h1 class="label">
    <?php echo render_title($resource) ?>
  </h1>

  <table class="sticky-enabled">
    <thead>
      <tr>
		  <th>
		    <?php echo __('#') ?>
		  </th><th>
		    <?php echo __('Name') ?>
		  </th><th>
		    <?php echo __('Location') ?>
		  </th><th>
		    <?php echo __('Unique Identifier') ?>		
		  </th><th>
		    <?php echo __('Description/Title') ?>
		  </th><th>
		    <?php echo __('Period Covered') ?>
		  </th><th>
		    <?php echo __('Extent') ?>
		  </th><th>
		    <?php echo __('Accrual Space') ?>
		  </th><th>
		    <?php echo __('Shelf') ?>
		  </th><th>
		    <?php echo __('Row') ?>		
		  </th><th>
		    <?php echo __('Bin') ?>		
		  </th><th>
		    <?php echo __('Forms') ?>
		  </th><th>
		    <?php echo __('Type') ?>
		  </th>
      </tr>
    </thead><tbody>
      <?php $row = 1; foreach ($results as $item): ?>
        <tr>
		    <td>
		      <?php echo $row++ ?>
		    </td><td>
	            <?php echo link_to(render_title($item->getName(array('cultureFallback' => true))), sfConfig::get('app_siteBaseUrl').'/'.$item->slug) ?>
		    </td><td>
	            <?php echo render_value_inline($item->getLocation(array('cultureFallback' => true))) ?>
		    </td><td>
		      <?php echo $item->uniqueIdentifier ?>		  
		    </td><td>
		      <?php echo $item->descriptionTitle ?>
		    </td><td>
		      <?php echo $item->periodCovered ?>
		    </td><td>
		      <?php echo $item->extent ?>
		    </td><td>
		      <?php echo $item->findingAids ?>
		    </td><td>
		      <?php echo $item->accrualSpace ?>
		    </td><td>
		      <?php echo $item->shelf ?>
		    </td><td>
		      <?php echo $item->rowNumber ?>		  
		    </td><td>
		      <?php echo $item->bin ?>		  
		    </td><td>
		      <?php echo $item->forms ?>
		    </td><td>
	            <?php echo render_value_inline($item->getType(array('cultureFallback' => true))) ?>
		    </td>
          <td>
            <?php echo $row++ ?>
          </td><td>
            <?php //echo link_to(render_title($item->getName(array('cultureFallback' => true))), sfConfig::get('app_siteBaseUrl').'/'.$item->slug) ?>
          </td><td>
            <?php //echo render_value_inline($item->getLocation(array('cultureFallback' => true))) ?>
          </td><td>
            <?php //echo render_value_inline($item->getType(array('cultureFallback' => true))) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div id="result-count">
    <?php echo $this->i18n->__('Showing %1% results', array('%1%' => count($results))) ?>
  </div>
</body>
</html>
