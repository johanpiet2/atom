<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Physical Storage') ?>
  </h1>
<?php end_slot() ?>

<?php slot('sidebar') ?>
<?php echo $form->renderGlobalErrors() ?>
<section class="sidebar-widget">

	<body onload="javascript:NewCal('dateStart','ddmmyyyy',false,false,24,true);renderCalendar('dateStart','div0');
			  javascript:NewCal('dateEnd','ddmmyyyy',false,false,24,true);renderCalendar('dateEnd','div1');toggleOff('div3');">
  
		<div>
	        <button type="submit" class="btn"><?php echo link_to(__('Back to reports'), array('module' => 'reports', 'action' => 'reportSelect'), array('title' => __('Back to reports'))) ?></button>
		</div>
		<h4><?php echo __('Filter options') ?></h4>
		<div>

			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportPhysicalStorage')), array('method' => 'get')) ?>

			<?php echo $form->renderHiddenFields() ?>

			<div id='divTypeOfReport' style="display: none"> 
				<?php echo $form->className->label('Types of Reports')->renderRow() ?>
			</div>

			<?php if (false): ?>
			<?php echo __('Date range') ?>
			<?php echo $form->dateStart->renderError() ?>
			<?php echo $form->dateEnd->renderError() ?>
			<?php echo __('%1% to %2%', array(
			  '%1%' => $form->dateStart->render(),
			  '%2%' => $form->dateEnd->render())) ?>
			<?php endif; ?>

			<?php echo $form->dateOf->renderRow() ?>
			
			<td>
				<?php $currentDate = date('d/m/Y H:i:s', strtotime("-3 months"));
				echo $form->dateStart->renderRow(array('value' => $currentDate,'readonly'=>'true','onchange' => 'checkBigger();'),'Start Date') ?>
				<div id="div0">Auto fill datepicker - Time Period - This will be deleted automatically</div>
			</td>

			<td>
				<?php $currentDate = date('d/m/Y H:i:s');
				echo $form->dateEnd->renderRow(array('value' => $currentDate,'readonly'=>'true','onchange' => ''),'End Date') ?>
				<div id="div1">Auto fill datepicker - dateEnd - This will be deleted automatically</div>
			</td>
						
	        <button type="submit" class="btn"><?php echo __('Search') ?></button>
      </form>

	</div>

</section>
<?php end_slot() ?>

<?php slot('content') ?>

  <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
    <thead>
      <tr>
		<th><?php echo __('Unique Identifier'); ?></th>
		<th><?php echo __('Description/Title'); ?></th>
		<th><?php echo __('Method of Storage'); ?></th>
		<th><?php echo __('Location'); ?></th>
		<th><?php echo __('Period Covered'); ?></th>
		<th><?php echo __('Extent'); ?></th>
		<th><?php echo __('Accrual Space'); ?></th>
		<th><?php echo __('Forms'); ?></th>
  
        <?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
          <th style="width: 110px"><?php echo __('Updated'); ?></th>
        <?php else: ?>
          <th style="width: 110px"><?php echo __('Created'); ?></th>
        <?php endif; ?>
      </tr>
    </thead><tbody>
     <?php foreach ($pager->getResults() as $result): ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
			<?php if (isset($result->id)) { ?> 
				<td><?php echo link_to(render_title($result->getUniqueIdentifier(array('cultureFallback' => true))), array($result, 'module' => 'physicalobject')) ?></td>
			<?php } else { ?> 
				<td>-</td> 
			<?php }	?>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getDescriptionTitle(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->type)) { ?> <td><?php echo $result->type ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->id)) { ?> 
				<td><?php echo $result->getLocation(array('cultureFallback' => true)) ?></td> 
			<?php } else { ?> 
				<td>-</td> 
			<?php }	?>
			
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getPeriodCovered(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getExtent(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getAccrualSpace(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getForms(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>

          </td>
		<td>
			<?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
			<?php echo $result->updatedAt ?>
			<?php else: ?>
			<?php echo $result->createdAt ?>
			<?php endif; ?>
		</td>

        </tr>

      <?php endforeach; ?>

    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
<?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>
