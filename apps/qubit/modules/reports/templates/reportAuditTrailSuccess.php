<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Audit Trail') ?>
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
			<form>
			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportAuditTrail')), array('method' => 'get')) ?>
			<?php echo $form->renderHiddenFields() ?>
			<?php echo $form->actionUser->label('User')->renderRow() ?>
			<?php echo $form->userAction->label('User Action')->renderRow() ?>

			<div id='divAuditTrail'> 
				<tr>
		            <td><p style="color:#424242"><input type="checkbox" name="cbAuditTrail" value="1"  />Deleted Items</td>
				</tr>
			</div>
			
			<?php if (false): ?>
			<?php echo __('Date range') ?>
			<?php echo $form->dateStart->renderError() ?>
			<?php echo $form->dateEnd->renderError() ?>
			<?php echo __('%1% to %2%', array(
			  '%1%' => $form->dateStart->render(),
			  '%2%' => $form->dateEnd->render())) ?>
			<?php endif; ?>

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
		<th><?php echo __('Title') ?></th>
		<th><?php echo __('Action') ?></th>
		<th><?php echo __('User') ?></th>
		<th><?php echo __('Component') ?></th>
		<th><?php echo __('Action Date') ?></th>
      </tr>
    </thead><tbody>

	<?php $actionOld  = "" ?>
	<?php $userOld    = "" ?>
	<?php $createdOld = "" ?>
	<?php $action     = "" ?>
	<?php $user       = "" ?>
	<?php $created    = "" ?>
	
	<?php foreach ($auditObjects as $item): ?>
		<?php if ($item[4] != 'access_log') { ?>
			<?php $action  = $item[3] ?>
			<?php $user    = $item[6] ?>
			<?php $created = $item[7] ?>
			<?php if (($action != $actionOld) || ($user != $userOld) || ($created != $createdOld)) { ?>
				<tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
				<?php if (isset($item["CLASS_NAME"])) {?>

					<?php if ($item["CLASS_NAME"] == "QubitInformationObject") { ?>
						<?php $informationObjectsAudit = QubitInformationObject::getById($item[1]); ?>
						<?php if ($informationObjectsAudit == null) { ?>
							<td><?php echo link_to("Deleted", array('module' => 'reports', 'action' => 'auditArchivalDescription', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($informationObjectsAudit, array('module' => 'reports', 'action' => 'auditArchivalDescription', 'source' => $item[1])) ?></td> 
						<?php } ?>

					<?php } elseif ($item["CLASS_NAME"] == "QubitAccessObject") { ?>
						<?php $accessObjectsAudit = QubitAccessObject::getById($item[1]); ?>
						<?php if ($accessObjectsAudit == null) { ?>
							<td><?php echo link_to("QubitAccessObject to do", array('module' => 'reports', 'action' => 'auditAccess', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($accessObjectsAudit, array('module' => 'reports', 'action' => 'auditAccess', 'source' => $item[1])) ?></td> 
						<?php } ?>

					<?php } elseif ($item["CLASS_NAME"] == "QubitPresevationObject") { ?>
						<?php $presevationObjectsAudit = QubitPresevationObject::getById($item[1]); ?>
						<?php if ($presevationObjectsAudit == null) { ?>
							<td><?php echo link_to("QubitPresevationObject to do", array('module' => 'reports', 'action' => 'auditPreservation', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($presevationObjectsAudit, array('module' => 'reports', 'action' => 'auditPreservation', 'source' => $item[1])) ?></td> 
						<?php } ?>

					<?php } else if ($item["CLASS_NAME"] == "QubitRegistry") { ?>
						<?php $actorObjectsAudit = QubitActor::getById($item[1]); ?>
						<?php if ($actorObjectsAudit == null) { ?>
							<td><?php echo link_to("Actor", array('module' => 'reports', 'action' => 'auditRegistry', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($actorObjectsAudit, array('module' => 'reports', 'action' => 'auditRegistry', 'source' => $item[1])) ?></td> 
						<?php } ?>
					<?php } elseif ($item["CLASS_NAME"] == "QubitRepository") { ?>
						<?php $actorObjectsAudit = QubitActor::getById($item[1]); ?>
						<?php if ($actorObjectsAudit == null) { ?>
							<td><?php echo link_to("Actor", array('module' => 'reports', 'action' => 'auditRepository', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($actorObjectsAudit, array('module' => 'reports', 'action' => 'auditRepository', 'source' => $item[1])) ?></td> 
						<?php } ?>
						
					<?php } elseif ($item["CLASS_NAME"] == "QubitServiceProvider") { ?>
						<?php $actorObjectsAudit = QubitActor::getById($item[1]); ?>
						<?php if ($actorObjectsAudit == null) { ?>
							<td><?php echo link_to("Actor", array('module' => 'reports', 'action' => 'auditServiceProvider', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($actorObjectsAudit, array('module' => 'reports', 'action' => 'auditServiceProvider', 'source' => $item[1])) ?></td> 
						<?php } ?>
						
					<?php } elseif ($item["CLASS_NAME"] == "QubitResearcher") { ?>
						<?php $actorObjectsAudit = QubitActor::getById($item[1]); ?>
						<?php if ($actorObjectsAudit == null) { ?>
							<td><?php echo link_to("Actor", array('module' => 'reports', 'action' => 'auditResearcher', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($actorObjectsAudit, array('module' => 'reports', 'action' => 'auditResearcher', 'source' => $item[1])) ?></td> 
						<?php } ?>
						
					<?php } elseif ($item["CLASS_NAME"] == "QubitServiceProvider") { ?>
						<?php $serviceProviderObjectsAudit = QubitActor::getById($item[1]); ?>
						<?php if ($serviceProviderObjectsAudit == null) { ?>
							<td><?php echo link_to("Service Provider missing", array('module' => 'reports', 'action' => 'auditServiceProvider', 'source' => $item[0])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($serviceProviderObjectsAudit, array('module' => 'reports', 'action' => 'auditServiceProvider', 'source' => $item[1])) ?></td> 
						<?php } ?>
						
					<?php } elseif ($item["CLASS_NAME"] == "QubitPhysicalObject") { ?>
						<?php $physicalObjectObjectsAudit = QubitPhysicalObject::getById($item[1]); ?>
						<?php if ($physicalObjectObjectsAudit == null) { ?>
							<td><?php echo link_to("Physical Object missing", array('module' => 'reports', 'action' => 'auditPhysicalStorage', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($physicalObjectObjectsAudit, array('module' => 'reports', 'action' => 'auditPhysicalStorage', 'source' => $item[1])) ?></td> 
						<?php } ?>

					<?php } elseif ($item["CLASS_NAME"] == "QubitRegistry") { ?>
						<?php $registryObjectsAudit = QubitRegistry::getById($item[1]); ?>
						<?php if ($registryObjectsAudit == null) { ?>
							<td><?php echo link_to("Registry missing", array('module' => 'reports', 'action' => 'auditRegistry', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($registryObjectsAudit, array('module' => 'reports', 'action' => 'auditRegistry', 'source' => $item[1])) ?></td> 
						<?php } ?>

					<?php } elseif ($item["CLASS_NAME"] == "QubitResearcher") { ?>
						<?php $researcherObjectsAudit = QubitRegistry::getById($item[1]); ?>
						<?php if ($researcherObjectsAudit == null) { ?>
							<td><?php echo link_to("Researcher missing", array('module' => 'reports', 'action' => 'auditResearcher', 'source' => $item[0])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($researcherObjectsAudit, array('module' => 'reports', 'action' => 'auditResearcher', 'source' => $item[0])) ?></td> 
						<?php } ?>

					<?php } elseif ($item["CLASS_NAME"] == "QubitUser") { ?>
						<?php $actorObjectsAudit = QubitActor::getById($item[1]); ?>
						<?php if ($actorObjectsAudit == null) { ?>
							<td><?php echo link_to("Actor missing", array('module' => 'reports', 'action' => 'auditActor', 'source' => $item[1])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($actorObjectsAudit, array('module' => 'reports', 'action' => 'auditActor', 'source' => $item[1])) ?></td> 
						<?php } ?>
						
					<?php } elseif ($item["CLASS_NAME"] == "QubitDonor") { ?>
						<?php $donorObjectsAudit = QubitDonor::getById($item[1]); ?>
						<?php if ($donorObjectsAudit == null) { ?>
							<td><?php echo link_to("Donor missing", array('module' => 'reports', 'action' => 'auditDonor', 'source' => $item[0])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($donorObjectsAudit, array('module' => 'reports', 'action' => 'auditDonor', 'source' => $item[0])) ?></td> 
						<?php } ?>

					<?php } elseif ($item["CLASS_NAME"] == "QubitTerm") { ?>
						<?php $taxonomyObjectsAudit = QubitTerm::getById($item[1]); ?>
						<?php if ($taxonomyObjectsAudit == null) { ?>
							<td><?php echo link_to("Taxonomy/Term missing", array('module' => 'reports', 'action' => 'auditTaxonomy', 'source' => $item[0])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($taxonomyObjectsAudit, array('module' => 'reports', 'action' => 'auditTaxonomy', 'source' => $item[0])) ?></td> 
						<?php } ?>

					<?php } elseif ($item["CLASS_NAME"] == "QubitBookinObject") { ?>
						<?php $bookinObjectsAudit = QubitBookinObject::getById($item[1]); ?>
						<?php if ($bookinObjectsAudit == null) { ?>
							<td><?php echo link_to("Book In missing", array('module' => 'reports', 'action' => 'auditBookIn', 'source' => $item[0])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($bookinObjectsAudit, array('module' => 'reports', 'action' => 'auditBookIn', 'source' => $item[0])) ?></td> 
						<?php } ?>

					<?php } elseif ($item["CLASS_NAME"] == "QubitBookoutObject") { ?>
						<?php $bookOutObjectsAudit = QubitBookoutObject::getById($item[1]); ?>
						<?php if ($bookOutObjectsAudit == null) { ?>
							<td><?php echo link_to("Book Out missing", array('module' => 'reports', 'action' => 'auditBookOut', 'source' => $item[0])) ?></td> 
						<?php } else { ?>
							<td><?php echo link_to($bookOutObjectsAudit, array('module' => 'reports', 'action' => 'auditBookOut', 'source' => $item[0])) ?></td> 
						<?php } ?>

					<?php } else { ?>
						<td><?php echo $item[0] ?></td> 
					<?php } ?>

				<?php } else { ?>
				
					<?php $deletedItem = QubitAuditObject::getById($item[1]); ?>
					<?php for ($i=0; $i < count($deletedItem); $i++) { ?>
							<?php if ($deletedItem["DB_TABLE"] == 'repository') { ?>
							<td><?php echo $deletedItem[4] ?></td> 
							
						<?php } ?>
				<?php } ?>
					
					
				
						<td><?php echo link_to($item[1], array('module' => 'reports', 'action' => 'reportDeleted', 'source' => $item[1])) ?></td> 
				<?php } ?>
				<td><?php echo $item[3] ?></td> 
				<td><?php echo $item[6] ?></td> 
				
				<?php if (isset($item["CLASS_NAME"])) {?>
					<?php if ($item["CLASS_NAME"] == 'QubitInformationObject') { ?>
						<td><?php echo "Archival Description" ?></td> 
					<?php } else if ($item["CLASS_NAME"] == 'qubitActor') { ?>
						<?php if ($item["CLASS_NAME"] == "QubitRegistry") { ?>
							<td><?php echo "Registry" ?></td> 
						<?php } elseif ($item["CLASS_NAME"] == "QubitRepository") { ?>
							<td><?php echo "Repository" ?></td> 
						<?php } else { ?>
							<td><?php echo "Actor/Authority Record" ?></td> 
						<?php } ?>
					<?php } else if ($item["CLASS_NAME"] == 'QubitRepository') { ?>
						<td><?php echo "Archival Institution" ?></td>
						 
					<?php } else if ($item["CLASS_NAME"] == 'QubitResearcher') { ?>
						<td><?php echo "Researcher" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitServiceProvider') { ?>
						<td><?php echo "Service Provider" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitPhysicalObject') { ?>
						<td><?php echo "Physical Storage" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitRegistry') { ?>
						<td><?php echo "Registry" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitRearcher') { ?>
						<td><?php echo "Rearcher" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitActor') { ?>
						<td><?php echo "Actor/Authority Record" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitUser') { ?>
						<td><?php echo "User" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitDonor') { ?>
						<td><?php echo "Donor" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitTerm') { ?>
						<td><?php echo "Taxonomy/Term" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitBookinObject') { ?>
						<td><?php echo "Book In" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitBookoutObject') { ?>
						<td><?php echo "Book Out" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitAccessObject') { ?>
						<td><?php echo "Access" ?></td> 
						
					<?php } else if ($item["CLASS_NAME"] == 'QubitPresevationObject') { ?>
						<td><?php echo "Preservation" ?></td> 
						
					<?php } else { ?>
						<td><?php echo $item["CLASS_NAME"] ?></td> 

					<?php } ?>
				<?php } else { ?>
					<td><?php echo $item["DB_TABLE"] ?></td> 
				<?php } ?>
				<td><?php echo $item[7] ?></td> 
			</tr>
			<?php } ?>
			<?php $actionOld  = $item[3] ?>
			<?php $userOld    = $item[6] ?>
			<?php $createdOld = $item[7] ?>
		<?php } ?>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
	<?php //echo //get_partial('default/pager', array('pager' => $pager2)) ?>
<?php end_slot() ?>
