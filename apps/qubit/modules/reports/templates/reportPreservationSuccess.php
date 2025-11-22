<?php decorate_with('layout_2col'); ?>

<?php slot('title'); ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', ['width' => '42', 'height' => '42']); ?>
    <?php echo __('Browse Preservation Items'); ?>
  </h1>
<?php end_slot(); ?>

<?php slot('sidebar'); ?>
<?php echo $form->renderGlobalErrors(); ?>
<section class="sidebar-widget">

	<body onload="javascript:NewCal('dateStart','ddmmyyyy',false,false,24,true);renderCalendar('dateStart','div0');
			  javascript:NewCal('dateEnd','ddmmyyyy',false,false,24,true);renderCalendar('dateEnd','div1');toggleOff('div3');">
  
		<div>
	        <button type="submit" class="btn"><?php echo link_to(__('Back to reports'), ['module' => 'reports', 'action' => 'reportSelect'], ['title' => __('Back to reports')]); ?></button>
		</div>
		<h4><?php echo __('Filter options'); ?></h4>
		<div>

			<?php echo $form->renderFormTag(url_for(['module' => 'reports', 'action' => 'reportPreservation']), ['method' => 'get']); ?>

			<?php echo $form->renderHiddenFields(); ?>

			<div id='typeOfReport' style="display: none"> 
				<?php echo $form->className->label('Types of Reports')->renderRow(); ?>
			</div>

			<?php if (sfConfig::get('app_multi_repository')) { ?>
				<?php echo $form->repositories
			    ->label(__('Repository'))
			    ->renderRow(); ?>
			<?php } ?>
			
			<td>
			  <?php echo render_field($form->dateStart->label(__('Date Start')), null, ['type' => 'date']); ?>
			<td>
			  <?php echo render_field($form->dateEnd->label(__('Date End')), null, ['type' => 'date']); ?>
			</td>						
						
	        <button type="submit" class="btn"><?php echo __('Search'); ?></button>
      </form>

	</div>

</section>
<?php end_slot(); ?>

<?php slot('content'); ?>

  <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
    <thead>
      <tr>
		<th style="width: 110px"><?php echo __('Identifier'); ?></th>
		<th style="width: 250px"><?php echo __('Title'); ?></th>
		<th><?php echo __('Usability'); ?></th>
		<th><?php echo __('Measure'); ?></th>
		<th><?php echo __('Availabile'); ?></th>
		<th><?php echo __('Restoration'); ?></th>
		<th><?php echo __('Conservation'); ?></th>
		<th><?php echo __('Method of Storage'); ?></th>
		<th><?php echo __('Condition'); ?></th>

        <?php if ('CREATED_AT' != $form->getValue('dateOf')) { ?>
          <th style="width: 110px"><?php echo __('Updated'); ?></th>
        <?php } else { ?>
          <th style="width: 110px"><?php echo __('Created'); ?></th>
        <?php } ?>
      </tr>
    </thead><tbody>
    <?php foreach ($pager->getResults() as $result) { ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd'; ?>">
		<?php	$infoObjectExist = QubitInformationObject::getById($result->object_id);
            if (isset($infoObjectExist)) {
                    foreach (QubitRelation::getRelationsBySubjectId($result->id) as $item2) {
                        $this->informationObjects = QubitInformationObject::getById($item2->objectId); ?>
						<?php if (isset($this->informationObjects->identifier)) { ?> <td><?php echo link_to($this->informationObjects->identifier, [$this->informationObjects, 'module' => 'informationobject']); ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->title)) { ?> <td><?php echo $this->informationObjects->title; ?></td> <?php } else { ?> <td>-</td> <?php }
                    }
                ?>
					
				<?php if ('Please Select' == QubitTerm::getById($result->usabilityId)) {?> <td><?php echo '-'; ?></td> <?php } else { ?>
		        	<td><?php echo __('%1%', ['%1%' => QubitTerm::getById($result->usabilityId)]); ?></td> <?php } ?>
				<?php if ('Please Select' == QubitTerm::getById($result->measureId)) {?> <td><?php echo '-'; ?></td> <?php } else { ?>
		        	<td><?php echo __('%1%', ['%1%' => QubitTerm::getById($result->measureId)]); ?></td> <?php } ?>
				<?php if ('Please Select' == QubitTerm::getById($result->availabilityId)) {?> <td><?php echo '-'; ?></td> <?php } else { ?>
		        	<td><?php echo __('%1%', ['%1%' => QubitTerm::getById($result->availabilityId)]); ?></td> <?php } ?>
				<?php if ('Please Select' == QubitTerm::getById($result->conservationId)) {?> <td><?php echo '-'; ?></td> <?php } else { ?>
		        	<td><?php echo __('%1%', ['%1%' => QubitTerm::getById($result->conservationId)]); ?></td> <?php } ?>
				<?php if ('Please Select' == QubitTerm::getById($result->restorationId)) {?> <td><?php echo '-'; ?></td> <?php } else { ?>
		        	<td><?php echo __('%1%', ['%1%' => QubitTerm::getById($result->restorationId)]); ?></td> <?php } ?>
				<?php if ('Please Select' == QubitTerm::getById($result->typeId)) {?> <td><?php echo '-'; ?></td> <?php } else { ?>
		        	<td><?php echo __('%1%', ['%1%' => QubitTerm::getById($result->typeId)]); ?></td> <?php } ?>
				<?php if ('Please Select' == QubitTerm::getById($result->conditionId)) {?> <td><?php echo '-'; ?></td> <?php } else { ?>
		        	<td><?php echo __('%1%', ['%1%' => QubitTerm::getById($result->conditionId)]); ?></td> <?php } ?>
		      </td>
				<td>
					<?php if ('CREATED_AT' != $form->getValue('dateOf')) { ?>
					<?php echo $result->updatedAt; ?>
					<?php } else { ?>
					<?php echo $result->createdAt; ?>
					<?php } ?>
				</td>
			<?php } ?>
        </tr>

      <?php } ?>

    </tbody>
  </table>

<?php end_slot(); ?>

<?php slot('after-content'); ?>
<?php echo get_partial('default/pager', ['pager' => $pager]); ?>
<?php end_slot(); ?>
