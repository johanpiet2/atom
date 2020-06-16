<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse newest additions') ?>
  </h1>
<?php end_slot() ?>

<?php slot('sidebar') ?>
<?php echo $form->renderGlobalErrors() ?>
<section class="sidebar-widget">

	<body onload="javascript:NewCal('dateStart','ddmmyyyy',false,false,24,true);renderCalendar('dateStart','div0');
			  javascript:NewCal('dateEnd','ddmmyyyy',false,false,24,true);renderCalendar('dateEnd','div1');toggleOff('div3');">
  
		<h4><?php echo __('Filter options') ?></h4>
		<div>

			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportUpdates')), array('method' => 'get')) ?>

			<?php echo $form->renderHiddenFields() ?>

			<?php echo $form->className->label('Types of Reports')->renderRow(array('onchange'=>'toggleOff("div3");')) ?>

			<div id='divBookedOut' style="display: none"> 
					<tr>
						<td><?php echo "Include (Booked Out only)" ?></td>
					</tr>
					<tr>
						<td><p style="color:#424242"><input type="checkbox" name="cbBookedOut" value="true" checked="true">Booked Out Overdue</p></td>
					</tr>
			</div>
			
			<div id='div3' style="display: none">
				<table>
					<tr>
						<td><?php echo "Include (Archival description only)" ?></td>
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

				<?php //echo $form->formats
					//	->label(__('Archive Type'))
					//	->renderRow() ?>

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

				<?php //echo $form->hard
						//->label(__('Digital copy available'))
						//->renderRow() ?>
			
				<?php //echo $form->digital
						//->label(__('Hard copy available'))
						//->renderRow() ?>

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
<script type="text/javascript">
	function toggleOff(id)
	{
	//alert("id =="+id+" className="+document.getElementById("className").value);
		if (id == "div3")
		{
			if ("QubitInformationObject" == document.getElementById("className").value)
			{
				document.getElementById(id).style.display="block";
				document.getElementById('divBookedOut').style.display="none";
			}
			else if (document.getElementById("className").value == "QubitBookoutObject")
			{
				//alert("id1 =="+id+" className="+document.getElementById("className").value);
				document.getElementById('divBookedOut').style.display="block";
				document.getElementById(id).style.display="none";
			}
			else
			{
				//alert("id3 =="+id+" className="+document.getElementById("className").value);
				document.getElementById(id).style.display="none";
				document.getElementById('divBookedOut').style.display="none";
			} 
		}
		
/*		if (id=="div3")
		{
			if ("/atom/index.php/document-permanantly-not-available-reason-as-text-field" == document.getElementById("digital").value)
			{
				document.getElementById(id).style.display="block";
				alert("id block =="+id);
			}
			else
			{
				//document.getElementById(id).style.display="none";
				//alert("id none =="+id);
			}
		}
*/
	}
</script>

<?php end_slot() ?>

<?php slot('content') ?>

  <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
    <thead>
      <tr>
        <?php if ('QubitInformationObject' == $className && 0 < sfConfig::get('app_multi_repository')): ?>
			<th style="width: 110px"><?php echo __('ID') ?></th>
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
          
         <?php elseif ('QubitActor' == $className): ?>
			<th style="width: 110px"><?php echo __('Authorized Form Of Name') ?></th>
			<th style="width: 110px"><?php echo __('Dates Of Existence') ?></th>
			<th style="width: 110px"><?php echo __('History') ?></th>
			<th style="width: 110px"><?php echo __('Places') ?></th>
			<th style="width: 110px"><?php echo __('Legal Status') ?></th>
			<th style="width: 110px"><?php echo __('Mandates') ?></th>
			<th style="width: 110px"><?php echo __('Internal Structures') ?></th>
			<th style="width: 110px"><?php echo __('General Context') ?></th>
			<th style="width: 110px"><?php echo __('Institution Responsible') ?></th>
			<th style="width: 110px"><?php echo __('Rules') ?></th>
			<th style="width: 110px"><?php echo __('Sources') ?></th>
			<th style="width: 110px"><?php echo __('Revision History') ?></th>
 			<th style="width: 110px"><?php echo __('Corporate Body Identifiers') ?></th>
			<th style="width: 110px"><?php echo __('Entity Type Id') ?></th>
			<th style="width: 110px"><?php echo __('Corporate Body Identifiers') ?></th>
			<th style="width: 110px"><?php echo __('Description Status') ?></th>
			<th style="width: 110px"><?php echo __('Description Detail') ?></th>
			<th style="width: 110px"><?php echo __('Description Identifier') ?></th>
			<th style="width: 110px"><?php echo __('Source Standard') ?></th>

        <?php elseif ('QubitRepository' == $className): ?>
			<th style="width: 110px"><?php echo __('Identifier') ?></th>
			<th style="width: 110px"><?php echo __('Description Status') ?></th>
			<th style="width: 110px"><?php echo __('Description Detail') ?></th>
			<th style="width: 110px"><?php echo __('Description identifier') ?></th>
			<th style="width: 200px"><?php echo __('Geocultural Context') ?></th>
			<th style="width: 200px"><?php echo __('Collecting Policies') ?></th>
			<th style="width: 200px"><?php echo __('Buildings') ?></th>
			<th style="width: 200px"><?php echo __('Holdings') ?></th>
			<th style="width: 200px"><?php echo __('FindingAids') ?></th>
			<th style="width: 200px"><?php echo __('Opening Times') ?></th>
			<th style="width: 200px"><?php echo __('Access Conditions') ?></th>
			<th style="width: 200px"><?php echo __('Disabled Access') ?></th>
			<th style="width: 200px"><?php echo __('Research Services') ?></th>
			<th style="width: 200px"><?php echo __('Reproduction Services') ?></th>
			<th style="width: 200px"><?php echo __('Public Facilities') ?></th>
			<th style="width: 200px"><?php echo __('Description Institution Identifier') ?></th>
			<th style="width: 200px"><?php echo __('Description Rules') ?></th>
			<th style="width: 200px"><?php echo __('Description Sources') ?></th>
			<th style="width: 200px"><?php echo __('Description Revision History') ?></th>

			<?php elseif ('QubitRegistry' == $className) : ?>
			<th style="width: 110px"><?php echo __('Identifier') ?></th>
			<th style="width: 110px"><?php echo __('Authorized Form Of Name') ?></th>

			<?php elseif ('QubitPresevationObject' == $className): ?>
			<th style="width: 110px"><?php echo __('ID') ?></th>
			<th><?php echo __('Usability') ?></th>
			<th><?php echo __('Measure') ?></th>
			<th><?php echo __('Availabile') ?></th>
			<th><?php echo __('Restoration') ?></th>
			<th><?php echo __('Method of Storage') ?></th>
			<th><?php echo __('Archive type') ?></th>

			<th style="width: 110px"><?php echo __('ID') ?></th>
			<th style="width: 110px"><?php echo __('Identifier') ?></th>
			<th style="width: 250px"><?php echo __('Title') ?></th>
			<th style="width: 110px"><?php echo __('Alternate Title') ?></th>
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
			
        <?php elseif ('QubitAccessObject' == $className): ?>
			<th><?php echo __('ID') ?></th>
			<th><?php echo __('Refusal') ?></th>
			<th><?php echo __('Sensitive') ?></th>
			<th><?php echo __('Publish') ?></th>
			<th><?php echo __('Classification') ?></th>
			<th><?php echo __('Restriction') ?></th>

			<th style="width: 110px"><?php echo __('ID') ?></th>
			<th style="width: 110px"><?php echo __('Identifier') ?></th>
			<th style="width: 250px"><?php echo __('Title') ?></th>
			<th style="width: 110px"><?php echo __('Alternate Title') ?></th>
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
			
        <?php elseif ('QubitPhysicalObject' == $className): ?>
			<th><?php echo __('Method of Storage'); ?></th>
			<th><?php echo __('Location'); ?></th>
			<th><?php echo __('Unique Identifier'); ?></th>
			<th><?php echo __('Description/Title'); ?></th>
			<th><?php echo __('Period Covered'); ?></th>
			<th><?php echo __('Extent'); ?></th>
			<th><?php echo __('Accrual Space'); ?></th>
			<th><?php echo __('Forms'); ?></th>
  
        <?php elseif ('QubitBookoutObject' == $className): ?>
			<th><?php echo __('Identifier') ?></th>
			<th><?php echo __('Title') ?></th>
			<th><?php echo __('Name of Requestor') ?></th>
			<th><?php echo __('Dispatcher') ?></th>
			<th><?php echo __('Location') ?></th>
			<th><?php echo __('Time Period') ?></th>
			<th><?php echo __('Remarks/Comments') ?></th>
			<th><?php echo __('Unique Identifier') ?></th>
			<th><?php echo __('Strong Room') ?></th>
			<th><?php echo __('Shelf') ?></th>
			<th><?php echo __('Row') ?></th>
			<th><?php echo __('Availability') ?></th>
			<th><?php echo __('Record Condition') ?></th>

        <?php elseif ('QubitBookinObject' == $className): ?>
			<th><?php echo __('Identifier') ?></th>
			<th><?php echo __('Title') ?></th>
			<th><?php echo __('Name of Requestor') ?></th>
			<th><?php echo __('Location') ?></th>
			<th><?php echo __('Time Period') ?></th>
			<th><?php echo __('Remarks/Comments') ?></th>
			<th><?php echo __('Unique Identifier') ?></th>
			<th><?php echo __('Strong Room') ?></th>
			<th><?php echo __('Shelf') ?></th>
			<th><?php echo __('Row') ?></th>
			<th><?php echo __('Record Condition') ?></th>

        <?php elseif ('QubitTerm' == $className): ?>
          <th><?php echo __('Taxonomy'); ?></th>
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
      <?php 
      foreach ($pager->getResults() as $result): ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">


            <?php if ('QubitInformationObject' == $className): ?>

              <?php echo link_to(render_title($result->getTitle(array('cultureFallback' => true))), array($result, 'module' => 'informationobject')) ?>
              <?php $status = $result->getPublicationStatus() ?>
              <?php if (isset($status) && $status->statusId == QubitTerm::PUBLICATION_STATUS_DRAFT_ID): ?><span class="note2"><?php echo ' ('.$status->status.')' ?></span><?php endif; ?>

				<td><?php echo $result->id ?></td>
				<?php if (isset($result->identifier)) { ?> <td><?php echo $result->identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
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

				<?php if (isset($_GET['cbPhysicalStorageObject'])): ?>
			    	<?php
		    			$this->informationObjectPhysicalStorage = QubitInformationObject::getById($result->id);
		    			
		    			if ($this->informationObjectPhysicalStorage->getPhysicalObjects()->count() > 0)
		    			{
							foreach ($this->informationObjectPhysicalStorage->getPhysicalObjects() as $item)
							{ 
								?>
								<?php if (isset($item)) { ?> 
									<td><?php echo $item->__toString() ?></td> 
								<?php } else { ?> 
									<td>-</td> 
								<?php }	?>
								<?php if (isset($item)) { ?> 
									<td><?php echo $item->uniqueIdentifier ?></td>
								<?php } else { ?> 
									<td>-</td> 
								<?php }	?>
								<?php if (isset($item)) { ?> 
									<td><?php echo $item->location ?></td>
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
							<?php		
						}
					?>
			    <?php endif; ?>

            <?php elseif ('QubitPresevationObject' == $className): ?>
				<td><?php echo $result->id ?></td>
				<?php if (QubitTerm::getById($result->usabilityId) == "Please Select") {?> <td><?php echo "-" ?></td> <?php } else { ?>
	            	<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->usabilityId))) ?></td> <?php } ?>
				<?php if (QubitTerm::getById($result->measureId) == "Please Select") {?> <td><?php echo "-" ?></td> <?php } else { ?>
	            	<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->measureId))) ?></td> <?php } ?>
				<?php if (QubitTerm::getById($result->availabilityId) == "Please Select") {?> <td><?php echo "-" ?></td> <?php } else { ?>
	            	<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->availabilityId))) ?></td> <?php } ?>
				<?php if (QubitTerm::getById($result->conservationId) == "Please Select") {?> <td><?php echo "-" ?></td> <?php } else { ?>
	            	<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->conservationId))) ?></td> <?php } ?>
				<?php if (QubitTerm::getById($result->typeId) == "Please Select") {?> <td><?php echo "-" ?></td> <?php } else { ?>
	            	<td><?php echo __('%1%', array('%1%' => QubitTerm::getById($result->typeId))) ?></td> <?php } ?>
            	<?php
					foreach (QubitRelation::getRelationsBySubjectId($result->id) as $item2)
					{ 
						$this->informationObjects = QubitInformationObject::getById($item2->objectId); ?>
						<td>
				<td><?php echo $this->informationObjects->id ?></td>
						<?php if (isset($this->informationObjects->identifier)) { ?> <td><?php echo $this->informationObjects->identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->title)) { ?> <td><?php echo $this->informationObjects->title ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->alternateTitle)) { ?> <td><?php echo $this->informationObjects->alternateTitle ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->extentAndMedium)) { ?> <td><?php echo $this->informationObjects->extentAndMedium ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->archivalHistory)) { ?> <td><?php echo $this->informationObjects->archivalHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->acquisition)) { ?> <td><?php echo $this->informationObjects->acquisition ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->scopeAndContent)) { ?> <td><?php echo $this->informationObjects->scopeAndContent ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->appraisal)) { ?> <td><?php echo $this->informationObjects->appraisal ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->accruals)) { ?> <td><?php echo $this->informationObjects->accruals ?></td> <?php } else { ?> <td>-</td> <?php }	?>

						<?php if (isset($this->informationObjects->arrangement)) { ?> <td><?php echo $this->informationObjects->arrangement ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->accessConditions)) { ?> <td><?php echo $this->informationObjects->accessConditions ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->reproductionConditions)) { ?> <td><?php echo $this->informationObjects->reproductionConditions ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->physicalCharacteristics)) { ?> <td><?php echo $this->informationObjects->physicalCharacteristics ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->findingAids)) { ?> <td><?php echo $this->informationObjects->findingAids ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->locationOfOriginals)) { ?> <td><?php echo $this->informationObjects->locationOfOriginals ?></td> <?php } else { ?><td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->locationOfCopies)) { ?> <td><?php echo $this->informationObjects->locationOfCopies ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->row)) { ?> <td><?php echo $this->informationObjects->row ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->shelf)) { ?> <td><?php echo $this->informationObjects->shelf ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->bin)) { ?> <td><?php echo $this->informationObjects->bin ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->relatedUnitsOfDescription)) { ?> <td><?php echo $this->informationObjects->relatedUnitsOfDescription ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->institutionResponsibleIdentifier)) { ?> <td><?php echo $this->informationObjects->institutionResponsibleIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->rules)) { ?> <td><?php echo $this->informationObjects->rules ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->sources)) { ?> <td><?php echo $this->informationObjects->sources ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->revisionHistory)) { ?> <td><?php echo $this->informationObjects->revisionHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if ($this->informationObjects->culture) { ?> <td><?php echo $this->informationObjects->culture ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php		
					}
				?>

            <?php elseif ('QubitAccessObject' == $className): ?>
				<td><?php echo $result->id ?></td>
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
				<?php 
					foreach (QubitRelation::getRelationsBySubjectId($result->id) as $item2)
					{ 
						$this->informationObjects = QubitInformationObject::getById($item2->objectId); ?>
						<td><?php echo $this->informationObjects->id ?></td>
						<?php if (isset($this->informationObjects->identifier)) { ?> <td><?php echo $this->informationObjects->identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->title)) { ?> <td><?php echo $this->informationObjects->title ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->alternateTitle)) { ?> <td><?php echo $this->informationObjects->alternateTitle ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->extentAndMedium)) { ?> <td><?php echo $this->informationObjects->extentAndMedium ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->archivalHistory)) { ?> <td><?php echo $this->informationObjects->archivalHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->acquisition)) { ?> <td><?php echo $this->informationObjects->acquisition ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->scopeAndContent)) { ?> <td><?php echo $this->informationObjects->scopeAndContent ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->appraisal)) { ?> <td><?php echo $this->informationObjects->appraisal ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->accruals)) { ?> <td><?php echo $this->informationObjects->accruals ?></td> <?php } else { ?> <td>-</td> <?php }	?>

						<?php if (isset($this->informationObjects->arrangement)) { ?> <td><?php echo $this->informationObjects->arrangement ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->accessConditions)) { ?> <td><?php echo $this->informationObjects->accessConditions ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->reproductionConditions)) { ?> <td><?php echo $this->informationObjects->reproductionConditions ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->physicalCharacteristics)) { ?> <td><?php echo $this->informationObjects->physicalCharacteristics ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->findingAids)) { ?> <td><?php echo $this->informationObjects->findingAids ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->locationOfOriginals)) { ?> <td><?php echo $this->informationObjects->locationOfOriginals ?></td> <?php } else { ?><td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->locationOfCopies)) { ?> <td><?php echo $this->informationObjects->locationOfCopies ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->row)) { ?> <td><?php echo $this->informationObjects->row ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->shelf)) { ?> <td><?php echo $this->informationObjects->shelf ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->bin)) { ?> <td><?php echo $this->informationObjects->bin ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->relatedUnitsOfDescription)) { ?> <td><?php echo $this->informationObjects->relatedUnitsOfDescription ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->institutionResponsibleIdentifier)) { ?> <td><?php echo $this->informationObjects->institutionResponsibleIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->rules)) { ?> <td><?php echo $this->informationObjects->rules ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->sources)) { ?> <td><?php echo $this->informationObjects->sources ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if (isset($this->informationObjects->revisionHistory)) { ?> <td><?php echo $this->informationObjects->revisionHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php if ($this->informationObjects->culture) { ?> <td><?php echo $this->informationObjects->culture ?></td> <?php } else { ?> <td>-</td> <?php }	?>
						<?php		
					}
				?>

            <?php elseif ('QubitActor' == $className): ?>

              <?php $name = render_title($result->getAuthorizedFormOfName(array('cultureFallback' => true))) ?>
              <?php echo link_to($name, array($result, 'module' => 'actor')) ?>

 			  <?php if ($result->authorizedFormOfName) { ?> <td><?php echo $result->authorizedFormOfName ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->datesOfExistence) { ?> <td><?php echo $result->datesOfExistence ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->history) { ?> <td><?php echo $result->history ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->places) { ?> <td><?php echo $result->places ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->legalStatus) { ?> <td><?php echo $result->legalStatus ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->mandates) { ?> <td><?php echo $result->mandates ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->internalStructures) { ?> <td><?php echo $result->internalStructures ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->generalContext) { ?> <td><?php echo $result->generalContext ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->institutionResponsibleIdentifier) { ?> <td><?php echo $result->institutionResponsibleIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->rules) { ?> <td><?php echo $result->rules ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->sources) { ?> <td><?php echo $result->sources ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->revisionHistory) { ?> <td><?php echo $result->revisionHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>

 			  <?php if ($result->corporateBodyIdentifiers) { ?> <td><?php echo $result->corporateBodyIdentifiers ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->entityTypeId) { ?> <td><?php echo QubitTerm::getById($result->entityTypeId) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->corporateBodyIdentifiers) { ?> <td><?php echo $result->corporateBodyIdentifiers ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descriptionStatusId) { ?> <td><?php echo QubitTerm::getById($result->descriptionStatusId) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descriptionDetailId) { ?> <td><?php echo QubitTerm::getById($result->descriptionDetailId) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descriptionIdentifier) { ?> <td><?php echo $result->descriptionIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->sourceStandard) { ?> <td><?php echo $result->sourceStandard ?></td> <?php } else { ?> <td>-</td> <?php }	?>
            <?php elseif ('QubitFunction' == $className): ?>
              <?php $name = render_title($result->getAuthorizedFormOfName(array('cultureFallback' => true))) ?>
              <?php echo link_to($name, array($result, 'module' => 'digitalobject')) ?>

            <?php elseif ('QubitRepository' == $className): ?>

              <?php $name = render_title($result->getAuthorizedFormOfName(array('cultureFallback' => true))) ?>
              <?php echo link_to($name, array($result, 'module' => 'repository')) ?>

 			  <?php if ($result->identifier) { ?> <td><?php echo $result->identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descStatusId) { ?> <td><?php echo QubitTerm::getById($result->descStatusId) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descDetailId) { ?> <td><?php echo QubitTerm::getById($result->descDetailId) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descIdentifier) { ?> <td><?php echo $result->descIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->geoculturalContext) { ?> <td><?php echo $result->geoculturalContext ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->collectingPolicies) { ?> <td><?php echo $result->collectingPolicies ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->buildings) { ?> <td><?php echo $result->buildings ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->holdings) { ?> <td><?php echo $result->holdings ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->findingAids) { ?> <td><?php echo $result->findingAids ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->openingTimes) { ?> <td><?php echo $result->openingTimes ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->accessConditions) { ?> <td><?php echo $result->accessConditions ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->disabledAccess) { ?> <td><?php echo $result->disabledAccess ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->researchServices) { ?> <td><?php echo $result->researchServices ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->reproductionServices) { ?> <td><?php echo $result->reproductionServices ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->publicFacilities) { ?> <td><?php echo $result->publicFacilities ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descInstitutionIdentifier) { ?> <td><?php echo $result->descInstitutionIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descRules) { ?> <td><?php echo $result->descRules ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descSources) { ?> <td><?php echo $result->descSources ?></td> <?php } else { ?> <td>-</td> <?php }	?>
 			  <?php if ($result->descRevisionHistory) { ?> <td><?php echo $result->descRevisionHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			  			  
            <?php elseif ('QubitRegistry' == $className): ?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->id ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (($result->getAuthorizedFormOfName(array('cultureFallback' => true)))) { ?> <td><?php echo $result->getAuthorizedFormOfName(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>

		  <?php elseif ('QubitTerm' == $className): ?>
              <?php $name = render_title($result->getName(array('cultureFallback' => true))) ?>
              <?php echo link_to($name, array($result, 'module' => 'term')) ?>

	        <?php elseif ('QubitPhysicalObject' == $className): ?>
				<?php if (isset($result->type)) { ?> <td><?php echo $result->type ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> 
					<td><?php echo $result->getLocation(array('cultureFallback' => true)) ?></td> 
				<?php } else { ?> 
					<td>-</td> 
				<?php }	?>
				
				<?php if (isset($result->id)) { ?> 
					<td><?php echo $result->getUniqueIdentifier(array('cultureFallback' => true)) ?></td> 
				<?php } else { ?> 
					<td>-</td> 
				<?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getDescriptionTitle(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getPeriodCovered(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getExtent(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getAccrualSpace(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getForms(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>

		    <?php elseif ('QubitBookoutObject' == $className):
    			$bookOutSubjectId = QubitRelation::getObjectsBySubjectId($result->id);  

				if (isset($_GET['cbBookedOut'])):
	    			$bookedOutTime=$result->getTime_period(array('cultureFallback' => true));
					if (isset($bookedOutTime)):
						$pos = strpos($bookedOutTime, "/");
						if ($pos !== false) { 
							$dayB = substr($bookedOutTime, 0, $pos); 
						}
						$bookedOutTime = substr($bookedOutTime, $pos+1);
						$pos = strpos($bookedOutTime, "/");
						if ($pos !== false) { 
							$monthB = substr($bookedOutTime, 0, $pos);
						}
						$bookedOutTime = substr($bookedOutTime, $pos+1);
						$formatedDate = $monthB."/".$dayB."/".$bookedOutTime;
						
						$start_date = new DateTime($formatedDate);
						$currentDate = new DateTime(date('d M yy H:i:s'));
						
						$since_start = $start_date->diff($currentDate);
						$this->start_dateDays = $since_start;
		        		QubitXMLImport::addLog("bookedOutTime...since_start:".$since_start->d, "Booked Out overdue", get_class($this), false);
		        		
						if ($since_start->d > 2):
							if (isset($bookOutSubjectId)) 
							{ 
								foreach ($bookOutSubjectId as $relation)
								{
									$informationObjectsBookOut = QubitInformationObject::getById($relation->objectId); 
								}
								if (isset($informationObjectsBookOut)) 
								{ 
									?> <td><?php echo $informationObjectsBookOut->identifier ?></td> <?php
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
							<?php if (isset($result->id)) { ?> <td><?php echo $result->getLocation(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
							<?php if (isset($result->id)) { ?> <td><?php echo $result->getTime_period(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
							<?php if (isset($result->id)) { ?> <td><?php echo $result->getRemarks(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
							<?php if (isset($result->unique_identifier)) { ?> <td><?php echo $result->unique_identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
							<?php if (isset($result->strong_room)) { ?> <td><?php echo $result->strong_room ?></td> <?php } else { ?> <td>-</td> <?php }	?>
							<?php if (isset($result->shelf)) { ?> <td><?php echo $result->shelf ?></td> <?php } else { ?> <td>-</td> <?php }	?>
							<?php if (isset($result->row)) { ?> <td><?php echo $result->row ?></td> <?php } else { ?> <td>-</td> <?php }	?>
							<?php if (isset($result->availability)) { ?> <td><?php echo $result->availability ?></td> <?php } else { ?> <td>-</td> <?php }	?>
							<?php if (isset($result->record_condition)) { ?> <td><?php echo $result->record_condition ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					    <?php endif; ?>
			    	<?php endif; ?>
			  					  
		        <?php 
		        
		        else:
					if (isset($bookOutSubjectId)) 
					{ 
					    foreach ($bookOutSubjectId as $relation)
						{
							$informationObjectsBookOut = QubitInformationObject::getById($relation->objectId); 
						}
						if (isset($informationObjectsBookOut)) 
						{ 
							?> <td><?php echo $informationObjectsBookOut->identifier ?></td> <?php
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
					<?php if (isset($result->id)) { ?> <td><?php echo $result->getLocation(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result->id)) { ?> <td><?php echo $result->getTime_period(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result->id)) { ?> <td><?php echo $result->getRemarks(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result->unique_identifier)) { ?> <td><?php echo $result->unique_identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result->strong_room)) { ?> <td><?php echo $result->strong_room ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result->shelf)) { ?> <td><?php echo $result->shelf ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result->row)) { ?> <td><?php echo $result->row ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result->availability)) { ?> <td><?php echo $result->availability ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result->record_condition)) { ?> <td><?php echo $result->record_condition ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			    <?php endif; ?>

		    <?php elseif ('QubitBookinObject' == $className):
		        QubitXMLImport::addLog("getRelationsBySubjectId...".$result->object_id, "getRelationsBySubjectId", get_class($this), false);
				//print_r($result);
				$objectId = QubitBookinObjectI18n::getById($result->id);
				
		        QubitXMLImport::addLog("objectId->object_id.eeeeee..".count($objectId), "Book In Report", get_class($this), false);
		        QubitXMLImport::addLog("objectId->object_id.eeeeee..".$objectId->id, "Book In Report", get_class($this), false);
				
//var_dump($objectId[0][0]);
					
				$informationObjectsBookIn = QubitInformationObject::getById($objectId[0][0]); 
				if (isset($informationObjectsBookIn)) 
				{ 
					?> <td><?php echo $informationObjectsBookIn->identifier ?></td> <?php
				} 
				else { ?> 
					<td>eeee<?php echo $objectId->object_id ?></td> <?php 
				} 
 ?>
				<?php if (isset($result->name)) { ?> <td><?php echo $result->name ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		    
				<?php if (isset($result->requestorId)) { ?> <td><?php echo $result->requestorId ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getLocation(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getTime_period(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getRemarks(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->unique_identifier)) { ?> <td><?php echo $result->unique_identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->strong_room)) { ?> <td><?php echo $result->strong_room ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->shelf)) { ?> <td><?php echo $result->shelf ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->row)) { ?> <td><?php echo $result->row ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->record_condition)) { ?> <td><?php echo $result->record_condition ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			  				  
            <?php endif; ?>

          </td>
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
