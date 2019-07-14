<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Access Items') ?>
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

			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportAccess')), array('method' => 'get')) ?>

			<?php echo $form->renderHiddenFields() ?>

			<div id='typeOfReport' style="display: none">
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
		<th style="width: 110px"><?php echo __('Identifier') ?></th>
		<th style="width: 250px"><?php echo __('Title') ?></th>
		<th><?php echo __('Refusal') ?></th>
		<th><?php echo __('Sensitive') ?></th>
		<th><?php echo __('Publish') ?></th>
		<th><?php echo __('Classification') ?></th>
		<th><?php echo __('Restriction') ?></th>


        <?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
          <th style="width: 110px"><?php echo __('Updated'); ?></th>
        <?php else: ?>
          <th style="width: 110px"><?php echo __('Created'); ?></th>
        <?php endif; ?>
      </tr>
    </thead><tbody>
	<?php
      foreach ($pager->getResults() as $result): ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
			<?php 
				$infoObjectExist = QubitInformationObject::getById($result->object_id);
				if (isset($infoObjectExist)) {
				
					foreach (QubitRelation::getRelationsBySubjectId($result->id) as $item2)
					{ 
						$this->informationObjects = QubitInformationObject::getById($item2->objectId); ?>
						<?php if (isset($this->informationObjects->identifier)) { ?> <td><?php echo link_to($this->informationObjects->identifier, array($this->informationObjects, 'module' => 'informationobject')) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->title)) { ?> <td><?php echo $this->informationObjects->title ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php		
				}
			?>

			<?php if (isset($result->refusalId)) { ?> 
				<?php if (QubitTerm::getById($result->refusalId) == "Please Select") {?> <td>-</td> <?php } else { ?>
				<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->refusalId))) ?></td> <?php } ?>
			<?php } else { ?> 
				<td>-</td> 
			<?php }	?>
			<?php if (isset($result->sensitivityId)) { ?> 
				<?php if (QubitTerm::getById($result->sensitivityId) == "Please Select") {?> <td>-</td> <?php } else { ?>
				<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->sensitivityId))) ?></td> <?php } ?>
			<?php } else { ?> 
				<td>-</td> 
			<?php }	?>
			<?php if (isset($result->publishId)) { ?> 
				<?php if (QubitTerm::getById($result->publishId) == "Please Select") {?> <td>-</td> <?php } else { ?>
				<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->publishId))) ?></td> <?php } ?>
			<?php } else { ?> 
				<td>-</td> 
			<?php }	?>
			<?php if (isset($result->classificationId)) { ?> 
				<?php if (QubitTerm::getById($result->publishId) == "Please Select") {?> <td>-</td> <?php } else { ?>
				<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->classificationId))) ?></td> <?php } ?>
			<?php } else { ?> 
				<td>-</td> 
			<?php }	?>
			<?php if (isset($result->restrictionId)) { ?> 
				<?php if (QubitTerm::getById($result->restrictionId) == "Please Select") {?> <td>-</td> <?php } else { ?>
				<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->restrictionId))) ?></td> <?php } ?>
			<?php } else { ?> 
				<td>-</td> 
			<?php }	?>
			<td>
				<?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
				<?php echo $result->updatedAt ?>
				<?php else: ?>
				<?php echo $result->createdAt ?>
				<?php endif; ?>
			</td>

        </tr>
		<?php } ?>
				

      <?php endforeach; ?>

    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
<?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>
