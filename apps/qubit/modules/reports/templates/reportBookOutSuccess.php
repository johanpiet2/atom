<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Booked Out Items') ?>
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

			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportBookOut')), array('method' => 'get')) ?>

			<?php echo $form->renderHiddenFields() ?>

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
		<th><?php echo __('Identifier') ?></th>
		<th><?php echo __('Title') ?></th>
		<th><?php echo __('Name of Requestor') ?></th>
		<th><?php echo __('Dispatcher') ?></th>
		<th><?php echo __('Requested Period') ?></th>
		<th><?php echo __('Remarks/Comments') ?></th>

      </tr>
    </thead><tbody>
    <?php  foreach ($pager->getResults() as $result): ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
    	<?php
			$bookOutSubjectId = QubitRelation::getObjectsBySubjectId($result->id);  

			if (isset($bookOutSubjectId)) 
			{ 
			    foreach ($bookOutSubjectId as $relation)
				{
					$informationObjectsBookOut = QubitInformationObject::getById($relation->objectId); 
				}
				if (isset($informationObjectsBookOut)) 
				{ 
					?> <td><?php echo link_to($informationObjectsBookOut->identifier, array($informationObjectsBookOut, 'module' => 'informationobject')) ?></td> <?php
				} 
				else { ?> 
					<td>-</td> <?php 
				} 
			} 
			else { ?> 
				<td>-</td> <?php 
			} ?>

			<?php if (isset($result->name)) { ?> <td><?php echo $result->name ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		
			<?php if (isset($result->requestorId)) { ?> <td><?php echo $result->requestorId ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->dispatcherId)) { ?> <td><?php echo $result->dispatcherId ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getTime_period(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getRemarks(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>

          </td>

        </tr>

      <?php endforeach; ?>

    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
<?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>