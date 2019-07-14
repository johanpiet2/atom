<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Archival Description') ?>
  </h1>
<?php end_slot() ?>

<?php slot('sidebar') ?>
<?php echo $form->renderGlobalErrors() ?>
<section class="sidebar-widget">

	<body onload="javascript:NewCal('dateStart','ddmmyyyy',false,false,24,true);renderCalendar('dateStart','div0');
			  javascript:NewCal('dateEnd','ddmmyyyy',false,false,24,true);renderCalendar('dateEnd','div1');">
  
		<div>
	        <button type="submit" class="btn"><?php echo link_to(__('Back to reports'), array('module' => 'reports', 'action' => 'reportSelect'), array('title' => __('Back to reports'))) ?></button>
		</div>
		<h4><?php echo __('Filter options') ?></h4>
		<div>

			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportInformationObject')), array('method' => 'get')) ?>

			<?php echo $form->renderHiddenFields() ?>

			<div id='divTypeOfReport' style="display: none"> 
				<?php echo $form->className->label('Types of Reports')->renderRow() ?>
			</div>
			
			<div id='div3'>
				<table>
					<tr>
						<td><?php echo "Include" ?></td>
					</tr>
					<tr>
						<td><p style="color:#424242"><input type="checkbox" name="cbPreservationObject" value="true" checked="true">Preservation</p></td>
					</tr>
					<tr>
						<td><p style="color:#424242"><input type="checkbox" name="cbAccessObject" value="true" checked="true">Access</p></td>
					</tr>
					<tr>
						<td><p style="color:#424242"><input type="checkbox" name="cbPhysicalStorageObject" value="true" checked="true">Physical Storage</p></td>
					</tr>
				</table>

				<?php echo $form->publicationStatus
				  ->label(__('Publication status (%1% only)', array('%1%' => sfConfig::get('app_ui_label_informationobject'))))
				  ->renderRow() ?>
	 
				  <?php echo $form->levelOfDescription
					->label(__('Level of description'))
					->renderRow() ?>
					
				<?php echo $form->physicalStorage
					->label(__('Physical Storage'))
					->renderRow() ?>

				<?php if (sfConfig::get('app_multi_repository')): ?>
					<?php echo $form->repositories
						->label(__('Repository'))
						->renderRow() ?>
				<?php endif; ?>
				
				<?php echo $form->authorityRecords
					->label(__('Authority Records'))
					->renderRow() ?>
				
				<?php echo $form->registries
					->label(__('Register'))
					->renderRow() ?>

				<?php echo $form->condition
						->label(__('Condition'))
						->renderRow() ?>

				<?php echo $form->usability
						->label(__('Usability'))
						->renderRow() ?>

				<?php echo $form->measure
						->label(__('Measure'))
						->renderRow() ?>

				<?php echo $form->availability
						->label(__('Availabile'))
						->renderRow() ?>

				<?php echo $form->refusal
						->label(__('Refusal'))
						->renderRow() ?>

				<?php echo $form->restoration
						->label(__('Restoration'))
						->renderRow() ?>

				<?php echo $form->conservation
						->label(__('Conservation'))
						->renderRow() ?>

				<?php echo $form->type
						->label(__('Type'))
						->renderRow() ?>
					
				<?php echo $form->sensitivity
						->label(__('Sensitive'))
						->renderRow() ?>
			
				<?php echo $form->publish
						->label(__('Published'))
						->renderRow() ?>
			
				<?php echo $form->classification
						->label(__('Classification'))
						->renderRow() ?>
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
		<th style="width: 2500px"><?php echo __('Title') ?></th>
		<th style="width: 200px"><?php echo __('Alternate Title') ?></th>
		<th style="width: 200px"><?php echo __('Extent And Medium') ?></th>
		<th style="width: 200px"><?php echo __('Archival History') ?></th>
		<th style="width: 200px"><?php echo __('Acquisition') ?></th>
		<th style="width: 200px"><?php echo __('Scope And Content') ?></th>
		<th style="width: 200px"><?php echo __('Appraisal') ?></th>
		<th style="width: 200px"><?php echo __('Accruals') ?></th>
		<th style="width: 200px"><?php echo __('Arrangement') ?></th>
		<th style="width: 200px"><?php echo __('Access Conditions') ?></th>
		<th style="width: 200px"><?php echo __('Reproduction Conditions') ?></th>
		<th style="width: 200px"><?php echo __('Physical Characteristics') ?></th>
		<th style="width: 200px"><?php echo __('Finding Aids') ?></th>
		<th style="width: 200px"><?php echo __('Location Of Originals') ?></th>
		<th style="width: 200px"><?php echo __('Location Of Copies') ?></th>
		<th style="width: 110px"><?php echo __('Row') ?></th>
		<th style="width: 110px"><?php echo __('Shelf') ?></th>
		<th style="width: 110px"><?php echo __('Bin/Box') ?></th>
		<th style="width: 110px"><?php echo __('RelatedUnits Of Description') ?></th>
		<th style="width: 110px"><?php echo __('Institution Responsible Identifier') ?></th>
		<th style="width: 200px"><?php echo __('Rules') ?></th>
		<th style="width: 200px"><?php echo __('Sources') ?></th>
		<th style="width: 200px"><?php echo __('Revision History') ?></th>
		<th style="width: 110px"><?php echo __('Culture') ?></th>

		<?php if (isset($_GET['cbPreservationObject'])): ?>
				<th><?php echo __('Usability') ?></th>
				<th><?php echo __('Measure') ?></th>
				<th><?php echo __('Availabile') ?></th>
				<th><?php echo __('Restoration') ?></th>
				<th><?php echo __('Method of Storage') ?></th>
				<th><?php echo __('Archive Type') ?></th>
    	<?php endif; ?>
      
		<?php if (isset($_GET['cbAccessObject'])): ?>
			<th><?php echo __('Refusal') ?></th>
			<th><?php echo __('Sensitive') ?></th>
			<th><?php echo __('Publish') ?></th>
			<th><?php echo __('Classification') ?></th>
			<th><?php echo __('Restriction') ?></th>
    	<?php endif; ?>
      
		<?php if (isset($_GET['cbPhysicalStorageObject'])): ?>
			<th><?php echo __('Strong Room') ?></th>
			<th><?php echo __('Unique Identifier') ?></th>
			<th><?php echo __('Location') ?></th>
    	<?php endif; ?>

        <?php if ('QubitInformationObject' == $className && 0 < sfConfig::get('app_multi_repository')): ?>
			<th style="width: 110px"><?php echo __('Repository') ?></th>
        <?php endif; ?>
        
        <?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
          <th style="width: 110px"><?php echo __('Updated'); ?></th>
        <?php else: ?>
          <th style="width: 110px"><?php echo __('Created'); ?></th>
        <?php endif; ?>
      </tr>
    </thead><tbody>
    <?php foreach ($pager->getResults() as $result): ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
              <?php $status = $result->getPublicationStatus() ?>
              <?php if (isset($status) && $status->statusId == QubitTerm::PUBLICATION_STATUS_DRAFT_ID): ?><span class="note2"></span><?php endif; ?>

				<?php if (isset($result->identifier)) { ?> <td><?php echo link_to($result->identifier, array($result, 'module' => 'informationobject')) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->title)) { ?> <td><?php echo $result->title ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->alternateTitle)) { ?> <td><?php echo $result->alternateTitle ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->extentAndMedium)) { ?> <td><?php echo $result->extentAndMedium ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->archivalHistory)) { ?> <td><?php echo $result->archivalHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->acquisition)) { ?> <td><?php echo $result->acquisition ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->scopeAndContent)) { ?> <td><?php echo $result->scopeAndContent ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->appraisal)) { ?> <td><?php echo $result->appraisal ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->accruals)) { ?> <td><?php echo $result->accruals ?></td> <?php } else { ?> <td>-</td> <?php }	?>

				<?php if (isset($result->arrangement)) { ?> <td><?php echo $result->arrangement ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->accessConditions)) { ?> <td><?php echo $result->accessConditions ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->reproductionConditions)) { ?> <td><?php echo $result->reproductionConditions ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->physicalCharacteristics)) { ?> <td><?php echo $result->physicalCharacteristics ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->findingAids)) { ?> <td><?php echo $result->findingAids ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->locationOfOriginals)) { ?> <td><?php echo $result->locationOfOriginals ?></td> <?php } else { ?><td>-</td> <?php }	?>
				<?php if (isset($result->locationOfCopies)) { ?> <td><?php echo $result->locationOfCopies ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->row)) { ?> <td><?php echo $result->row ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->shelf)) { ?> <td><?php echo $result->shelf ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->bin)) { ?> <td><?php echo $result->bin ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->relatedUnitsOfDescription)) { ?> <td><?php echo $result->relatedUnitsOfDescription ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->institutionResponsibleIdentifier)) { ?> <td><?php echo $result->institutionResponsibleIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->rules)) { ?> <td><?php echo $result->rules ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->sources)) { ?> <td><?php echo $result->sources ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->revisionHistory)) { ?> <td><?php echo $result->revisionHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if ($result->culture) { ?> <td><?php echo $result->culture ?></td> <?php } else { ?> <td>-</td> <?php }	?>

				<?php if (isset($_GET['cbPreservationObject'])): ?>
			    	<?php
	    			$this->informationObjectPreservation = QubitInformationObject::getById($result->id);
	    			if ($this->informationObjectPreservation->getPresevationObjects()->count() > 0)
	    			{
						foreach ($this->informationObjectPreservation->getPresevationObjects() as $item)
						{
							?>
							<?php if (isset($item->usabilityId)) { ?> 
								<?php if (QubitTerm::getById($item->usabilityId) == "Please Select") {?> 
									<td>-</td> 
								<?php } else { ?>
									<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->usabilityId))) ?></td> 
								<?php } ?>
							<?php } else { ?> 
								<td>-</td> 
							<?php }	?>
							<?php if (isset($item->measureId)) { ?> 
								<?php if (QubitTerm::getById($item->measureId) == "Please Select") {?> <td>-</td> <?php } else { ?>
								<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->measureId))) ?></td> <?php } ?>
							<?php } else { ?> 
								<td>-</td> 
							<?php }	?>
							<?php if (isset($item->availabilityId)) { ?> 
								<?php if (QubitTerm::getById($item->availabilityId) == "Please Select") {?> <td>-</td> <?php } else { ?>
								<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->availabilityId))) ?></td> <?php } ?>
							<?php } else { ?> 
								<td>-</td> 
							<?php }	?>
							<?php if (isset($item->restorationId)) { ?> 
								<?php if (QubitTerm::getById($item->restorationId) == "Please Select") {?> <td>-</td> <?php } else { ?>
								<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->restorationId))) ?></td> <?php } ?>
							<?php } else { ?> 
								<td>-</td> 
							<?php }	?>
							<?php if (isset($item->conservationId)) { ?> 
								<?php if (QubitTerm::getById($item->conservationId) == "Please Select") {?> <td>-</td> <?php } else { ?>
								<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->conservationId))) ?></td> <?php } ?>
							<?php } else { ?> 
								<td>-</td> 
							<?php }	?>
							<?php if (isset($item->typeId)) { ?> 
								<?php if (QubitTerm::getById($item->typeId) == "Please Select") {?> <td>-</td> <?php } else { ?>
								<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->typeId))) ?></td> <?php } ?>
							<?php } else { ?> 
								<td>-</td> 
							<?php }	?>
							<?php		
							continue;
						}
					}
					else
					{
						?>
						<td>-</td>
						<td>-</td>
						<td>-</td>
						<td>-</td>
						<td>-</td>
						<td>-</td>
						<?php		
					}
					?>
			    <?php endif; ?>

				<?php if (isset($_GET['cbAccessObject'])): ?>
			    	<?php
		    			$this->informationObjectAccess = QubitInformationObject::getById($result->id);
		    			
		    			if ($this->informationObjectAccess->getAccessObjects()->count() > 0)
		    			{
							foreach ($this->informationObjectAccess->getAccessObjects() as $item)
							{ 
								?>
								<?php if (isset($item->refusalId)) { ?> 
									<?php if (QubitTerm::getById($item->refusalId) == "Please Select") {?> <td>-</td> <?php } else { ?>
									<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->refusalId))) ?></td> <?php } ?>
								<?php } else { ?> 
									<td>-</td> 
								<?php }	?>
								<?php if (isset($item->sensitivityId)) { ?> 
									<?php if (QubitTerm::getById($item->sensitivityId) == "Please Select") {?> <td>-</td> <?php } else { ?>
									<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->sensitivityId))) ?></td> <?php } ?>
								<?php } else { ?> 
									<td>-</td> 
								<?php }	?>
								<?php if (isset($item->publishId)) { ?> 
									<?php if (QubitTerm::getById($item->publishId) == "Please Select") {?> <td>-</td> <?php } else { ?>
									<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->publishId))) ?></td> <?php } ?>
								<?php } else { ?> 
									<td>-</td> 
								<?php }	?>
								<?php if (isset($item->classificationId)) { ?> 
									<?php if (QubitTerm::getById($item->classificationId) == "Please Select") {?> <td>-</td> <?php } else { ?>
									<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->classificationId))) ?></td> <?php } ?>
								<?php } else { ?> 
									<td>-</td> 
								<?php }	?>
								<?php if (isset($item->restrictionId)) { ?> 
									<?php if (QubitTerm::getById($item->restrictionId) == "Please Select") {?> <td>-</td> <?php } else { ?>
									<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($item->restrictionId))) ?></td> <?php } ?>
								<?php } else { ?> 
									<td>-</td> 
								<?php }	?>

								<?php 
								continue;
							}
						}
						else
						{
							?>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<?php		
						}
					?>

			    <?php endif; ?>

		        
          <?php if ($className == 'QubitInformationObject'  && 0 < sfConfig::get('app_multi_repository')): ?>
            <td>
              <?php if (null !== $repository = $result->getRepository(array('inherit' => true))): ?>
                <?php echo $repository->getAuthorizedFormOfName(array('cultureFallback' => true)) ?>
              <?php endif; ?>
            </td>
          <?php elseif('QubitTerm' == $className): ?>
            <td><?php echo $result->taxonomy->getName(array('cultureFallback' => true)) ?></td>
          <?php endif; ?>

		<?php if ('QubitBookoutObject' == $className): 
			if ($this->start_dateDays->d > 2): ?>
				<td>
					<?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
					<?php echo $result->updatedAt ?>
					<?php else: ?>
					<?php echo $result->createdAt ?>
					<?php endif; ?>
				</td>
			<?php endif; ?>
		<?php 
		else: ?>
			<td>
				<?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
				<?php echo $result->updatedAt ?>
				<?php else: ?>
				<?php echo $result->createdAt ?>
				<?php endif; ?>
			</td>
		<?php endif; ?>
        </tr>
      <?php endforeach; ?>

    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
<?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>
