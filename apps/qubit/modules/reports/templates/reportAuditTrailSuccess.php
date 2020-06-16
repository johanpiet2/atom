<?php if (isset($pager) && $pager->getNbResults() || sfConfig::get('app_enable_institutional_scoping')): ?>
  <?php decorate_with('layout_2col') ?>
<?php else: ?>
  <?php decorate_with('layout_1col') ?>
<?php endif; ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Audit Trail') ?>
		<?php if (isset($pager) && $pager->getNbResults()): ?>
        	<?php echo __('Showing %1% results', array('%1%' => $pager->getNbResults())) ?>
		<?php else: ?>
			<?php echo __('No results found') ?>
		<?php endif; ?>
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
			<?php echo $form->userActivity->label('User Activity')->renderRow() ?>
		
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
	  <?php foreach ($pager->getResults() as $item): ?>
			<?php if (isset($item["CLASS_NAME"])) {?>
				<?php if ($item["CLASS_NAME"] == "QubitDigitalObject") { ?>
					<th><?php echo __('Item Description') ?></th>
					<th><?php echo __('Identifier') ?></th>
					<th><?php echo __('Archival Institution') ?></th>
					<th><?php echo __('Didital Object') ?></th>
					<th><?php echo __('Action') ?></th>
					<th><?php echo __('User') ?></th>
					<th><?php echo __('Component') ?></th>
					<th><?php echo __('Action Date') ?></th>
				<?php } else if ($item["CLASS_NAME"] == "QubitUser"){ ?>
					<th><?php echo __('Item Description') ?></th>
					<th><?php echo __('Action') ?></th>
					<th><?php echo __('User') ?></th>
					<th><?php echo __('Component') ?></th>
					<th><?php echo __('Action Date') ?></th>
				<?php } else if ($item["CLASS_NAME"] == "QubitActor"){ ?>
					<th><?php echo __('Item Description') ?></th>
					<th><?php echo __('Action') ?></th>
					<th><?php echo __('User') ?></th>
					<th><?php echo __('Component') ?></th>
					<th><?php echo __('Action Date') ?></th>
				<?php } else if ($item["CLASS_NAME"] == "QubitRegistry"){ ?>
					<th><?php echo __('Item Description') ?></th>
					<th><?php echo __('Identifier') ?></th>
					<th><?php echo __('Action') ?></th>
					<th><?php echo __('User') ?></th>
					<th><?php echo __('Component') ?></th>
					<th><?php echo __('Action Date') ?></th>
				<?php } else if ($item["CLASS_NAME"] == "QubitTaxonomy"){ ?>
					<th><?php echo __('Item Description') ?></th>
					<th><?php echo __('Identifier') ?></th>
					<th><?php echo __('Action') ?></th>
					<th><?php echo __('User') ?></th>
					<th><?php echo __('Component') ?></th>
					<th><?php echo __('Action Date') ?></th>
				<?php } else if ($item["CLASS_NAME"] == "QubitDonor"){ ?>
					<th><?php echo __('Item Description') ?></th>
					<th><?php echo __('Action') ?></th>
					<th><?php echo __('User') ?></th>
					<th><?php echo __('Component') ?></th>
					<th><?php echo __('Action Date') ?></th>
				<?php } else { ?>
					<th><?php echo __('Item Description') ?></th>
					<th><?php echo __('Identifier') ?></th>
					<th><?php echo __('Archival Institution') ?></th>
					<th><?php echo __('Action') ?></th>
					<th><?php echo __('User') ?></th>
					<th><?php echo __('Component') ?></th>
					<th><?php echo __('Action Date') ?></th>
				<?php } ?>
			<?php } else { ?>
				<th><?php echo __('Item Description') ?></th>
				<th><?php echo __('Component') ?></th>
				<th><?php echo __('Action') ?></th>
				<th><?php echo __('User') ?></th>
				<th><?php echo __('Action Date') ?></th>

			<?php } ?>
				
			<?php break; ?>
		<?php endforeach; ?>
      </tr>
    </thead>
	<tbody>

	<?php if ((double)$pager->getNbResults() > 0) { ?>
		<?php $actionOld  = "" ?>
		<?php $userOld    = "" ?>
		<?php $createdOld = "" ?>
		<?php $action     = "" ?>
		<?php $user       = "" ?>
		<?php $created    = "" ?>
		
		<?php foreach ($pager->getResults() as $item): ?>
			<?php if ($item["ACTION"] != 'delete') { ?>
				<?php if ($item["DB_TABLE"] != 'access_log') { ?>
					<?php $action  = $item["ACTION"] ?>
					<?php $user    = $item["USER"] ?>
					<?php $created = $item["ACTION_DATE_TIME"] ?>
					<tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
					<?php if (isset($item["CLASS_NAME"])) {?>
					<?php "QubitInformationObject" ?>
						<?php if ($item["CLASS_NAME"] == "QubitInformationObject") { ?>
							<td><?php echo link_to($item["TITLE"], array('module' => 'reports', 'action' => 'auditArchivalDescription', 'source' => $item["RECORD_ID"])) ?></td> 
							<td><?php echo $item["IDENTIFIER"] ?></td> 
							<td><?php echo $item["AUTHORIZED_FORM_OF_NAME"] ?></td> 
							
					<?php "QubitAccessObject" ?>
						<?php } elseif ($item["CLASS_NAME"] == "QubitAccessObject") { ?>
							<?php $accessObjectsAudit = QubitAccessObject::getById($item["RECORD_ID"]); //To Fix ?>
							<td><?php echo $item["TITLE"] //echo link_to($item["TITLE"], array('module' => 'reports', 'action' => 'auditAccess', 'source' => $item["RECORD_ID"])) ?></td>  
							<td><?php echo $item["IDENTIFIER"] ?></td> 
							<td><?php echo $item["AUTHORIZED_FORM_OF_NAME"] ?></td> 

					<?php "QubitRepository" ?>
						<?php } elseif ($item["CLASS_NAME"] == "QubitRepository") { ?>
							<td><?php echo link_to($item["AUTHORIZED_FORM_OF_NAME"], array('module' => 'reports', 'action' => 'auditRepository', 'source' => $item["RECORD_ID"])) ?></td> 
							<td><?php echo $item["IDENTIFIER"] ?></td>
							<td><?php echo "N/A" ?></td>
							
					<?php "QubitActor" ?>
						<?php } elseif ($item["CLASS_NAME"] == "QubitActor") { ?>
							<td><?php echo link_to($item["AUTHORIZED_FORM_OF_NAME"], array('module' => 'reports', 'action' => 'auditActor', 'source' => $item["RECORD_ID"])) ?></td> 
							
					<?php "QubitBookoutObject" ?>
						<?php } elseif ($item["CLASS_NAME"] == "QubitBookoutObject") { ?>
								<td><?php echo $item["TITLE"] //echo link_to($item["TITLE"], array('module' => 'reports', 'action' => 'auditBookOut', 'source' => $item["ID"])) ?></td> 
							<td><?php echo $item["IDENTIFIER"] ?></td>
							<td><?php echo $item["AUTHORIZED_FORM_OF_NAME"] ?></td>

					<?php "QubitBookinObject" ?>
						<?php } elseif ($item["CLASS_NAME"] == "QubitBookinObject") { ?>
							<?php $bookinObjectsAudit = QubitBookinObject::getById($item["RECORD_ID"]); ?>
							<?php if ($bookinObjectsAudit == null) { ?>
								<td><?php echo link_to("Book In missing", array('module' => 'reports', 'action' => 'auditBookIn', 'source' => $item["ID"])) ?></td> 
							<?php } else { ?>
								<td><?php echo link_to($bookinObjectsAudit, array('module' => 'reports', 'action' => 'auditBookIn', 'source' => $item["ID"])) ?></td> 
							<?php } ?>
							<td><?php echo "-" ?></td>
							<td><?php echo "-" ?></td>

					<?php //QubitDigitalObject to fix?> 
						<?php } elseif ($item["CLASS_NAME"] == "QubitDigitalObject") { ?>
							<td><?php echo $item["TITLE"] //echo link_to($item["TITLE"], array('module' => 'reports', 'action' => 'auditDigitalObject', 'source' => $item["ID"])) ?></td>
							<td><?php echo $item["IDENTIFIER"] ?></td>
							<td><?php echo $item["AUTHORIZED_FORM_OF_NAME"] ?></td>
							<td><?php echo $item["NAME"] ?></td>

					<?php //QubitDonor?>
						<?php } elseif ($item["CLASS_NAME"] == "QubitDonor") { ?>
							<td><?php echo link_to($item["AUTHORIZED_FORM_OF_NAME"], array('module' => 'reports', 'action' => 'auditDonor', 'source' => $item["RECORD_ID"])) ?></td> 

					<?php //QubitPhysicalObject?>
						<?php } elseif ($item["CLASS_NAME"] == "QubitPhysicalObject") { ?>
							<td><?php echo link_to($item["NAME"], array('module' => 'reports', 'action' => 'auditPhysicalStorage', 'source' => $item["RECORD_ID"])) ?></td> 
							<td><?php echo $item["UNIQUEIDENTIFIER"] ?> </td>
							<td><?php echo $item["AUTHORIZED_FORM_OF_NAME"] ?> </td>
							
						<?php "QubitPresevationObject" ?>
							<?php } elseif ($item["CLASS_NAME"] == "QubitPresevationObject") { ?>
								<?php $presevationObjectsAudit = QubitPresevationObject::getById($item["RECORD_ID"]); ?>
								<td><?php echo $item["TITLE"] //echo link_to($presevationObjectsAudit, array('module' => 'reports', 'action' => 'auditPreservation', 'source' => $item["RECORD_ID"])) ?></td> 

						<?php "QubitRegistry" ?>
							<?php } else if ($item["CLASS_NAME"] == "QubitRegistry") { ?>
								<td><?php echo link_to($item["AUTHORIZED_FORM_OF_NAME"], array('module' => 'reports', 'action' => 'auditRegistry', 'source' => $item["RECORD_ID"])) ?></td> 
								<td><?php echo $item["CORPORATE_BODY_IDENTIFIERS"] ?></td>
	 
						<?php "QubitServiceProvider" ?>
							<?php } elseif ($item["CLASS_NAME"] == "QubitServiceProvider") { ?>
								<?php $actorObjectsAudit = QubitActor::getById($item["RECORD_ID"]); ?>
								<?php if ($actorObjectsAudit == null) { ?>
									<td><?php echo link_to("Actor", array('module' => 'reports', 'action' => 'auditServiceProvider', 'source' => $item["RECORD_ID"])) ?></td> 
								<?php } else { ?>
									<td><?php echo link_to($actorObjectsAudit, array('module' => 'reports', 'action' => 'auditServiceProvider', 'source' => $item["RECORD_ID"])) ?></td> 
								<?php } ?>
								<td><?php echo $actorObjectsAudit->corporateBodyIdentifiers ?></td>
								<td><?php echo $item["AUTHORIZED_FORM_OF_NAME"] ?></td> 
								
						<?php "QubitResearcher" ?>
							<?php } elseif ($item["CLASS_NAME"] == "QubitResearcher") { ?>
								<?php $actorObjectsAudit = QubitActor::getById($item["RECORD_ID"]); ?>
								<?php if ($actorObjectsAudit == null) { ?>
									<td><?php echo link_to("Actor", array('module' => 'reports', 'action' => 'auditResearcher', 'source' => $item["RECORD_ID"])) ?></td> 
								<?php } else { ?>
									<td><?php echo link_to($actorObjectsAudit, array('module' => 'reports', 'action' => 'auditResearcher', 'source' => $item["RECORD_ID"])) ?></td> 
								<?php } ?>
								<td><?php echo $actorObjectsAudit->corporateBodyIdentifiers ?></td>
								<td><?php echo $item["AUTHORIZED_FORM_OF_NAME"] ?></td> 

						<?php "QubitUser" ?>
							<?php } elseif ($item["CLASS_NAME"] == "QubitUser") { ?>
								<?php $actorObjectsAudit = QubitActor::getById($item["RECORD_ID"]); ?>
								<?php if ($actorObjectsAudit == null) { ?>
									<td><?php echo link_to("Actor missing", array('module' => 'reports', 'action' => 'auditActor', 'source' => $item["RECORD_ID"])) ?></td> 
								<?php } else { ?>
									<td><?php echo link_to($actorObjectsAudit, array('module' => 'reports', 'action' => 'auditActor', 'source' => $item["RECORD_ID"])) ?></td> 
								<?php } ?>
							<?php } else { ?>
								<td><?php echo $item["ID"] ?></td> 
							<?php } ?>

						<?php } else { ?>
								<td><?php echo link_to($item["RECORD_ID"], array('module' => 'reports', 'action' => 'reportDeleted', 'source' => $item["RECORD_ID"])) ?></td> 
						<?php } ?>
						<td><?php echo $item["ACTION"] ?></td> 
						<td><?php echo $item["USER"] ?></td> 
						
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
								<?php if ($item["DB_TABLE"] == 'acl_group_i18n'): ?>
									<td><?php echo "Permissions/Groups" ?></td> 
								<?php else: ?>
									<td><?php echo "Taxonomy/Term" ?></td> 
								<?php endif; ?>
							<?php } else if ($item["CLASS_NAME"] == 'QubitBookinObject') { ?>
								<td><?php echo "Book In" ?></td> 
								
							<?php } else if ($item["CLASS_NAME"] == 'QubitBookoutObject') { ?>
								<td><?php echo "Book Out" ?></td> 
								
							<?php } else if ($item["CLASS_NAME"] == 'QubitAccessObject') { ?>
								<td><?php echo "Access" ?></td> 
								
							<?php } else if ($item["CLASS_NAME"] == 'QubitPresevationObject') { ?>
								<td><?php echo "Preservation" ?></td> 
								
							<?php } else if ($item["CLASS_NAME"] == 'QubitDigitalObject') { ?>
								<td><?php echo "Digital Object" ?></td> 
								
							<?php } else if ($item["CLASS_NAME"] == 'QubitObjectTermRelation') { ?>
								<td><?php echo "Object Term Relation" ?></td> 
								
							<?php } else { ?>
								<td><?php echo $item["CLASS_NAME"] ?></td> 

							<?php } ?>

						<?php } ?>
						<td><?php echo $item["ACTION_DATE_TIME"] ?></td> 
					</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<?php if ($item["DB_TABLE"] == 'QubitInformationObject') { ?>
						<td><?php echo "Archival Description" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'qubitActor') { ?>
						<td><?php echo "Actor/Authority Record" ?></td> 

					<?php } else if ($item["DB_TABLE"] == 'QubitRepository') { ?>
						<td><?php echo "Archival Institution" ?></td>
						 
					<?php } else if ($item["DB_TABLE"] == 'QubitResearcher') { ?>
						<td><?php echo "Researcher" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitServiceProvider') { ?>
						<td><?php echo "Service Provider" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitPhysicalObject') { ?>
						<td><?php echo "Physical Storage" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitRegistry') { ?>
						<td><?php echo "Registry" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitRearcher') { ?>
						<td><?php echo "Rearcher" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitActor') { ?>
						<td><?php echo "Actor/Authority Record" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitUser') { ?>
						<td><?php echo "User" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitDonor') { ?>
						<td><?php echo "Donor" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitTerm') { ?>
						<td><?php echo "Taxonomy/Term" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitBookinObject') { ?>
						<td><?php echo "Book In" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitBookoutObject') { ?>
						<td><?php echo "Book Out" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitAccessObject') { ?>
						<td><?php echo "Access" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitPresevationObject') { ?>
						<td><?php echo "Preservation" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitDigitalObject') { ?>
						<td><?php echo "Digital Object" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitObjectTermRelation') { ?>
						<td><?php echo "Object Term Relation" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitAccession') { ?>
						<td><?php echo "Accession" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitTaxonomy') { ?>
						<td><?php echo "Taxonomy" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitFunction') { ?>
						<td><?php echo "Function" ?></td> 
						
					<?php } else if ($item["DB_TABLE"] == 'QubitDeaccession') { ?>
						<td><?php echo "Deaccession" ?></td> 
						
					<?php } else { ?>
						<td><?php echo $item["DB_TABLE"] ?></td> 
						<td><?php echo "-" ?></td> 
						<td><?php echo "-" ?></td> 
						<td><?php echo "-" ?></td> 
						<td><?php echo "-" ?></td> 
						<td><?php echo "-" ?></td> 
					<?php } ?>
					<td><?php echo link_to($item["DB_TABLE"], array('module' => 'reports', 'action' => 'auditDeleted', 'source' => $item["ID"])) ?></td> 
					<td><?php echo "Delete" ?></td> 
					<td><?php echo $item["USER"] ?></td> 
					<td><?php echo $item["ACTION_DATE_TIME"] ?></td> 
				</tr>
			<?php } ?>
			
		<?php endforeach; ?>
	<?php } else { ?>
		<?php decorate_with('layout_2col') ?>
	<?php } ?>
		</tbody>
	</table>

<?php end_slot() ?>

<?php if (isset($pager)): ?>
  <?php slot('after-content') ?>
    <?php echo get_partial('default/pager', array('pager' => $pager)) ?>
  <?php end_slot() ?>
<?php endif; ?>
