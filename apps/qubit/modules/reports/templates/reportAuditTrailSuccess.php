<?php if (isset($pager) && $pager->getNbResults() || sfConfig::get('app_enable_institutional_scoping')) { ?>
  <?php decorate_with('layout_2col'); ?>
<?php } else { ?>
  <?php decorate_with('layout_1col'); ?>
<?php } ?>

<?php slot('title'); ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', ['width' => '42', 'height' => '42']); ?>
    <?php echo __('Browse Audit Trail'); ?>
		<?php if (isset($pager) && $pager->getNbResults()) { ?>
        	<?php echo __('Showing %1% results', ['%1%' => $pager->getNbResults()]); ?>
		<?php } else { ?>
			<?php echo __('No results found'); ?>
		<?php } ?>
  </h1>
<?php end_slot(); ?>

<?php slot('sidebar'); ?>
<?php echo $form->renderGlobalErrors(); ?>
<section class="sidebar-widget">
	<body>

		<div>
	        <button type="submit" class="btn"><?php echo link_to(__('Back to reports'), ['module' => 'reports', 'action' => 'reportSelect'], ['title' => __('Back to reports')]); ?></button>
		</div>
		<h4><?php echo __('Filter options'); ?></h4>
		<div>
			<form>
			<?php echo $form->renderFormTag(url_for(['module' => 'reports', 'action' => 'reportAuditTrail']), ['method' => 'get']); ?>
			<?php echo $form->renderHiddenFields(); ?>
			<?php echo $form->actionUser->label('User')->renderRow(); ?>
			<?php echo $form->userAction->label('User Action')->renderRow(); ?>
			<?php echo $form->userActivity->label('User Activity')->renderRow(); ?>
		
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
	  <?php foreach ($pager->getResults() as $item) { ?>
			<?php if (isset($item['CLASS_NAME'])) {?>
				<?php if ('QubitDigitalObject' == $item['CLASS_NAME']) { ?>
					<th><?php echo __('Item Description'); ?></th>
					<th><?php echo __('Identifier'); ?></th>
					<th><?php echo __('Archival Institution'); ?></th>
					<th><?php echo __('Didital Object'); ?></th>
					<th><?php echo __('Action'); ?></th>
					<th><?php echo __('User'); ?></th>
					<th><?php echo __('Component'); ?></th>
					<th><?php echo __('Action Date'); ?></th>
				<?php } elseif ('QubitUser' == $item['CLASS_NAME']) { ?>
					<th><?php echo __('Item Description'); ?></th>
					<th><?php echo __('Action'); ?></th>
					<th><?php echo __('User'); ?></th>
					<th><?php echo __('Component'); ?></th>
					<th><?php echo __('Action Date'); ?></th>
				<?php } elseif ('QubitActor' == $item['CLASS_NAME']) { ?>
					<th><?php echo __('Item Description'); ?></th>
					<th><?php echo __('Action'); ?></th>
					<th><?php echo __('User'); ?></th>
					<th><?php echo __('Component'); ?></th>
					<th><?php echo __('Action Date'); ?></th>
				<?php } elseif ('QubitRegistry' == $item['CLASS_NAME']) { ?>
					<th><?php echo __('Item Description'); ?></th>
					<th><?php echo __('Identifier'); ?></th>
					<th><?php echo __('Action'); ?></th>
					<th><?php echo __('User'); ?></th>
					<th><?php echo __('Component'); ?></th>
					<th><?php echo __('Action Date'); ?></th>
				<?php } elseif ('QubitTaxonomy' == $item['CLASS_NAME']) { ?>
					<th><?php echo __('Item Description'); ?></th>
					<th><?php echo __('Identifier'); ?></th>
					<th><?php echo __('Action'); ?></th>
					<th><?php echo __('User'); ?></th>
					<th><?php echo __('Component'); ?></th>
					<th><?php echo __('Action Date'); ?></th>
				<?php } elseif ('QubitDonor' == $item['CLASS_NAME']) { ?>
					<th><?php echo __('Item Description'); ?></th>
					<th><?php echo __('Action'); ?></th>
					<th><?php echo __('User'); ?></th>
					<th><?php echo __('Component'); ?></th>
					<th><?php echo __('Action Date'); ?></th>
				<?php } else { ?>
					<th><?php echo __('Item Description'); ?></th>
					<th><?php echo __('Identifier'); ?></th>
					<th><?php echo __('Archival Institution'); ?></th>
					<th><?php echo __('Action'); ?></th>
					<th><?php echo __('User'); ?></th>
					<th><?php echo __('Component'); ?></th>
					<th><?php echo __('Action Date'); ?></th>
				<?php } ?>
			<?php } else { ?>
				<th><?php echo __('Item Description'); ?></th>
				<th><?php echo __('Component'); ?></th>
				<th><?php echo __('Action'); ?></th>
				<th><?php echo __('User'); ?></th>
				<th><?php echo __('Action Date'); ?></th>

			<?php } ?>
				
			<?php break; ?>
		<?php } ?>
      </tr>
    </thead>
	<tbody>

	<?php if ((float) $pager->getNbResults() > 0) { ?>
		<?php $actionOld = ''; ?>
		<?php $userOld = ''; ?>
		<?php $createdOld = ''; ?>
		<?php $action = ''; ?>
		<?php $user = ''; ?>
		<?php $created = ''; ?>
		
		<?php foreach ($pager->getResults() as $item) { ?>
			<?php if ('delete' != $item['ACTION']) { ?>
				<?php if ('access_log' != $item['DB_TABLE']) { ?>
					<?php $action = $item['ACTION']; ?>
					<?php $user = $item['USER']; ?>
					<?php $created = $item['ACTION_DATE_TIME']; ?>
					<tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd'; ?>">
					<?php if (isset($item['CLASS_NAME'])) {?>
					<?php 'QubitInformationObject'; ?>
						<?php if ('QubitInformationObject' == $item['CLASS_NAME']) { ?>
							<td><?php echo link_to($item['TITLE'], ['module' => 'reports', 'action' => 'auditArchivalDescription', 'source' => $item['RECORD_ID']]); ?></td> 
							<td><?php echo $item['IDENTIFIER']; ?></td> 
							<td><?php echo $item['AUTHORIZED_FORM_OF_NAME']; ?></td> 
							
					<?php 'QubitAccessObject'; ?>
						<?php } elseif ('QubitAccessObject' == $item['CLASS_NAME']) { ?>
							<?php $accessObjectsAudit = QubitAccessObject::getById($item['RECORD_ID']); // To Fix?>
							<td><?php echo $item['TITLE']; // echo link_to($item["TITLE"], array('module' => 'reports', 'action' => 'auditAccess', 'source' => $item["RECORD_ID"]))?></td>  
							<td><?php echo $item['IDENTIFIER']; ?></td> 
							<td><?php echo $item['AUTHORIZED_FORM_OF_NAME']; ?></td> 

					<?php 'QubitRepository'; ?>
						<?php } elseif ('QubitRepository' == $item['CLASS_NAME']) { ?>
							<td><?php echo link_to($item['AUTHORIZED_FORM_OF_NAME'], ['module' => 'reports', 'action' => 'auditRepository', 'source' => $item['RECORD_ID']]); ?></td> 
							<td><?php echo $item['IDENTIFIER']; ?></td>
							<td><?php echo 'N/A'; ?></td>
							
					<?php 'QubitActor'; ?>
						<?php } elseif ('QubitActor' == $item['CLASS_NAME']) { ?>
							<td><?php echo link_to($item['AUTHORIZED_FORM_OF_NAME'], ['module' => 'reports', 'action' => 'auditActor', 'source' => $item['RECORD_ID']]); ?></td> 
							
					<?php 'QubitBookoutObject'; ?>
						<?php } elseif ('QubitBookoutObject' == $item['CLASS_NAME']) { ?>
								<td><?php echo $item['TITLE']; // echo link_to($item["TITLE"], array('module' => 'reports', 'action' => 'auditBookOut', 'source' => $item["ID"]))?></td> 
							<td><?php echo $item['IDENTIFIER']; ?></td>
							<td><?php echo $item['AUTHORIZED_FORM_OF_NAME']; ?></td>

					<?php 'QubitBookinObject'; ?>
						<?php } elseif ('QubitBookinObject' == $item['CLASS_NAME']) { ?>
							<?php $bookinObjectsAudit = QubitBookinObject::getById($item['RECORD_ID']); ?>
							<?php if (null == $bookinObjectsAudit) { ?>
								<td><?php echo link_to('Book In missing', ['module' => 'reports', 'action' => 'auditBookIn', 'source' => $item['ID']]); ?></td> 
							<?php } else { ?>
								<td><?php echo link_to($bookinObjectsAudit, ['module' => 'reports', 'action' => 'auditBookIn', 'source' => $item['ID']]); ?></td> 
							<?php } ?>
							<td><?php echo '-'; ?></td>
							<td><?php echo '-'; ?></td>

					<?php // QubitDigitalObject to fix?> 
						<?php } elseif ('QubitDigitalObject' == $item['CLASS_NAME']) { ?>
							<td><?php echo $item['TITLE']; // echo link_to($item["TITLE"], array('module' => 'reports', 'action' => 'auditDigitalObject', 'source' => $item["ID"]))?></td>
							<td><?php echo $item['IDENTIFIER']; ?></td>
							<td><?php echo $item['AUTHORIZED_FORM_OF_NAME']; ?></td>
							<td><?php echo $item['NAME']; ?></td>

					<?php // QubitDonor?>
						<?php } elseif ('QubitDonor' == $item['CLASS_NAME']) { ?>
							<td><?php echo link_to($item['AUTHORIZED_FORM_OF_NAME'], ['module' => 'reports', 'action' => 'auditDonor', 'source' => $item['RECORD_ID']]); ?></td> 

					<?php // QubitPhysicalObject?>
						<?php } elseif ('QubitPhysicalObject' == $item['CLASS_NAME']) { ?>
							<td><?php echo link_to($item['NAME'], ['module' => 'reports', 'action' => 'auditPhysicalStorage', 'source' => $item['RECORD_ID']]); ?></td> 
							<td><?php echo $item['UNIQUEIDENTIFIER']; ?> </td>
							<td><?php echo $item['AUTHORIZED_FORM_OF_NAME']; ?> </td>
							
						<?php 'QubitPresevationObject'; ?>
							<?php } elseif ('QubitPresevationObject' == $item['CLASS_NAME']) { ?>
								<?php $presevationObjectsAudit = QubitPresevationObject::getById($item['RECORD_ID']); ?>
								<td><?php echo $item['TITLE']; // echo link_to($presevationObjectsAudit, array('module' => 'reports', 'action' => 'auditPreservation', 'source' => $item["RECORD_ID"]))?></td> 

						<?php 'QubitRegistry'; ?>
							<?php } elseif ('QubitRegistry' == $item['CLASS_NAME']) { ?>
								<td><?php echo link_to($item['AUTHORIZED_FORM_OF_NAME'], ['module' => 'reports', 'action' => 'auditRegistry', 'source' => $item['RECORD_ID']]); ?></td> 
								<td><?php echo $item['CORPORATE_BODY_IDENTIFIERS']; ?></td>
	 
						<?php 'QubitServiceProvider'; ?>
							<?php } elseif ('QubitServiceProvider' == $item['CLASS_NAME']) { ?>
								<?php $actorObjectsAudit = QubitActor::getById($item['RECORD_ID']); ?>
								<?php if (null == $actorObjectsAudit) { ?>
									<td><?php echo link_to('Actor', ['module' => 'reports', 'action' => 'auditServiceProvider', 'source' => $item['RECORD_ID']]); ?></td> 
								<?php } else { ?>
									<td><?php echo link_to($actorObjectsAudit, ['module' => 'reports', 'action' => 'auditServiceProvider', 'source' => $item['RECORD_ID']]); ?></td> 
								<?php } ?>
								<td><?php echo $actorObjectsAudit->corporateBodyIdentifiers; ?></td>
								<td><?php echo $item['AUTHORIZED_FORM_OF_NAME']; ?></td> 
								
						<?php 'QubitResearcher'; ?>
							<?php } elseif ('QubitResearcher' == $item['CLASS_NAME']) { ?>
								<?php $actorObjectsAudit = QubitActor::getById($item['RECORD_ID']); ?>
								<?php if (null == $actorObjectsAudit) { ?>
									<td><?php echo link_to('Actor', ['module' => 'reports', 'action' => 'auditResearcher', 'source' => $item['RECORD_ID']]); ?></td> 
								<?php } else { ?>
									<td><?php echo link_to($actorObjectsAudit, ['module' => 'reports', 'action' => 'auditResearcher', 'source' => $item['RECORD_ID']]); ?></td> 
								<?php } ?>
								<td><?php echo $actorObjectsAudit->corporateBodyIdentifiers; ?></td>
								<td><?php echo $item['AUTHORIZED_FORM_OF_NAME']; ?></td> 

						<?php 'QubitUser'; ?>
							<?php } elseif ('QubitUser' == $item['CLASS_NAME']) { ?>
								<?php $actorObjectsAudit = QubitActor::getById($item['RECORD_ID']); ?>
								<?php if (null == $actorObjectsAudit) { ?>
									<td><?php echo link_to('Actor missing', ['module' => 'reports', 'action' => 'auditActor', 'source' => $item['RECORD_ID']]); ?></td> 
								<?php } else { ?>
									<td><?php echo link_to($actorObjectsAudit, ['module' => 'reports', 'action' => 'auditActor', 'source' => $item['RECORD_ID']]); ?></td> 
								<?php } ?>
							<?php } else { ?>
								<td><?php echo $item['ID']; ?></td> 
							<?php } ?>

						<?php } else { ?>
								<td><?php echo link_to($item['RECORD_ID'], ['module' => 'reports', 'action' => 'reportDeleted', 'source' => $item['RECORD_ID']]); ?></td> 
						<?php } ?>
						<td><?php echo $item['ACTION']; ?></td> 
						<td><?php echo $item['USER']; ?></td> 
						
						<?php if (isset($item['CLASS_NAME'])) {?>
							<?php if ('QubitInformationObject' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Archival Description'; ?></td> 
							<?php } elseif ('qubitActor' == $item['CLASS_NAME']) { ?>
								<?php if ('QubitRegistry' == $item['CLASS_NAME']) { ?>
									<td><?php echo 'Registry'; ?></td> 
								<?php } elseif ('QubitRepository' == $item['CLASS_NAME']) { ?>
									<td><?php echo 'Repository'; ?></td> 
								<?php } else { ?>
									<td><?php echo 'Actor/Authority Record'; ?></td> 
								<?php } ?>
							<?php } elseif ('QubitRepository' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Archival Institution'; ?></td>
								 
							<?php } elseif ('QubitResearcher' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Researcher'; ?></td> 
								
							<?php } elseif ('QubitServiceProvider' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Service Provider'; ?></td> 
								
							<?php } elseif ('QubitPhysicalObject' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Physical Storage'; ?></td> 
								
							<?php } elseif ('QubitRegistry' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Registry'; ?></td> 
								
							<?php } elseif ('QubitRearcher' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Rearcher'; ?></td> 
								
							<?php } elseif ('QubitActor' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Actor/Authority Record'; ?></td> 
								
							<?php } elseif ('QubitUser' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'User'; ?></td> 
								
							<?php } elseif ('QubitDonor' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Donor'; ?></td> 
								
							<?php } elseif ('QubitTerm' == $item['CLASS_NAME']) { ?>
								<?php if ('acl_group_i18n' == $item['DB_TABLE']) { ?>
									<td><?php echo 'Permissions/Groups'; ?></td> 
								<?php } else { ?>
									<td><?php echo 'Taxonomy/Term'; ?></td> 
								<?php } ?>
							<?php } elseif ('QubitBookinObject' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Book In'; ?></td> 
								
							<?php } elseif ('QubitBookoutObject' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Book Out'; ?></td> 
								
							<?php } elseif ('QubitAccessObject' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Access'; ?></td> 
								
							<?php } elseif ('QubitPresevationObject' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Preservation'; ?></td> 
								
							<?php } elseif ('QubitDigitalObject' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Digital Object'; ?></td> 
								
							<?php } elseif ('QubitObjectTermRelation' == $item['CLASS_NAME']) { ?>
								<td><?php echo 'Object Term Relation'; ?></td> 
								
							<?php } else { ?>
								<td><?php echo $item['CLASS_NAME']; ?></td> 

							<?php } ?>

						<?php } ?>
						<td><?php echo $item['ACTION_DATE_TIME']; ?></td> 
					</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<?php if ('QubitInformationObject' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Archival Description'; ?></td> 
						
					<?php } elseif ('qubitActor' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Actor/Authority Record'; ?></td> 

					<?php } elseif ('QubitRepository' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Archival Institution'; ?></td>
						 
					<?php } elseif ('QubitResearcher' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Researcher'; ?></td> 
						
					<?php } elseif ('QubitServiceProvider' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Service Provider'; ?></td> 
						
					<?php } elseif ('QubitPhysicalObject' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Physical Storage'; ?></td> 
						
					<?php } elseif ('QubitRegistry' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Registry'; ?></td> 
						
					<?php } elseif ('QubitRearcher' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Rearcher'; ?></td> 
						
					<?php } elseif ('QubitActor' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Actor/Authority Record'; ?></td> 
						
					<?php } elseif ('QubitUser' == $item['DB_TABLE']) { ?>
						<td><?php echo 'User'; ?></td> 
						
					<?php } elseif ('QubitDonor' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Donor'; ?></td> 
						
					<?php } elseif ('QubitTerm' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Taxonomy/Term'; ?></td> 
						
					<?php } elseif ('QubitBookinObject' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Book In'; ?></td> 
						
					<?php } elseif ('QubitBookoutObject' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Book Out'; ?></td> 
						
					<?php } elseif ('QubitAccessObject' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Access'; ?></td> 
						
					<?php } elseif ('QubitPresevationObject' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Preservation'; ?></td> 
						
					<?php } elseif ('QubitDigitalObject' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Digital Object'; ?></td> 
						
					<?php } elseif ('QubitObjectTermRelation' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Object Term Relation'; ?></td> 
						
					<?php } elseif ('QubitAccession' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Accession'; ?></td> 
						
					<?php } elseif ('QubitTaxonomy' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Taxonomy'; ?></td> 
						
					<?php } elseif ('QubitFunction' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Function'; ?></td> 
						
					<?php } elseif ('QubitDeaccession' == $item['DB_TABLE']) { ?>
						<td><?php echo 'Deaccession'; ?></td> 
						
					<?php } else { ?>
						<td><?php echo $item['DB_TABLE']; ?></td> 
						<td><?php echo '-'; ?></td> 
						<td><?php echo '-'; ?></td> 
						<td><?php echo '-'; ?></td> 
						<td><?php echo '-'; ?></td> 
						<td><?php echo '-'; ?></td> 
					<?php } ?>
					<td><?php echo link_to($item['DB_TABLE'], ['module' => 'reports', 'action' => 'auditDeleted', 'source' => $item['ID']]); ?></td> 
					<td><?php echo 'Delete'; ?></td> 
					<td><?php echo $item['USER']; ?></td> 
					<td><?php echo $item['ACTION_DATE_TIME']; ?></td> 
				</tr>
			<?php } ?>
			
		<?php } ?>
	<?php } else { ?>
		<?php decorate_with('layout_2col'); ?>
	<?php } ?>
		</tbody>
	</table>

<?php end_slot(); ?>

<?php if (isset($pager)) { ?>
  <?php slot('after-content'); ?>
    <?php echo get_partial('default/pager', ['pager' => $pager]); ?>
  <?php end_slot(); ?>
<?php } ?>
