<section class="actions">
  <ul>

      <?php if (QubitAcl::check($resource, 'update') || (QubitAcl::check($resource, 'translate'))): ?>
        <li><?php echo link_to(__('Edit'), array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('class' => 'c-btn c-btn-submit')) ?></li>
      <?php endif; ?>

      <?php if (QubitAcl::check($resource, 'delete')): ?>
        <li><?php echo link_to(__('Delete'), array($resource, 'module' => 'informationobject', 'action' => 'delete'), array('class' => 'c-btn c-btn-delete')) ?></li>
      <?php endif; ?>

      <?php if (QubitAcl::check($resource, 'create')): ?>
        <li><?php echo link_to(__('Add new'), array('module' => 'informationobject', 'action' => 'add', 'parent' => url_for(array($resource, 'module' => 'informationobject'))), array('class' => 'c-btn')) ?></li>
        <li><?php echo link_to(__('Duplicate'), array('module' => 'informationobject', 'action' => 'copy', 'source' => $resource->id), array('class' => 'c-btn')) ?></li>
      <?php endif; ?>

      <?php if (QubitAcl::check($resource, 'update') || sfContext::getInstance()->getUser()->hasGroup(QubitAclGroup::EDITOR_ID)): ?>

        <li><?php echo link_to(__('Move'), array($resource, 'module' => 'default', 'action' => 'move'), array('class' => 'c-btn')) ?></li>

        <li class="divider"></li>

        <li>
          <div class="btn-group dropup">
            <a class="c-btn dropdown-toggle" data-toggle="dropdown" href="#">
              <?php echo __('More') ?>
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">

              <li><?php echo link_to(__('Rename'), array($resource, 'module' => 'informationobject', 'action' => 'rename')) ?></li>

              <?php if (QubitAcl::check($resource, 'publish')): ?>
                <li><?php echo link_to(__('Update publication status'), array($resource, 'module' => 'informationobject', 'action' => 'updatePublicationStatus')) ?></li>
              <?php endif; ?>

              <li class="divider"></li>

              <li><?php echo link_to(__('Link physical storage'), array($resource, 'module' => 'informationobject', 'action' => 'editPhysicalObjects')) ?></li>

              <li class="divider"></li>

              <?php if (0 < count($resource->digitalObjectsRelatedByobjectId) && QubitDigitalObject::isUploadAllowed()): ?>
              
				<?php for ($n = 0; $n < count($resource->digitalObjectsRelatedByobjectId); $n++) 
					{ ?>
                		<li><?php echo link_to(__('Edit '.$resource->digitalObjectsRelatedByobjectId[$n] .' %1%', array('%1%' => mb_strtolower(sfConfig::get('app_ui_label_digitalobject')))), array($resource->digitalObjectsRelatedByobjectId[$n], 'module' => 'digitalobject', 'action' => 'edit')) // SITA list of linked digital objects ?></li>

				<?php } 	?>
		          <?php if (QubitDigitalObject::isUploadAllowed()): ?>
			          <?php if (5 > count($resource->digitalObjectsRelatedByobjectId)): //only allow max 5 digital objects ?>
			            <li><?php echo link_to(__('Link %1%', array('%1%' => mb_strtolower(sfConfig::get('app_ui_label_digitalobject')))), array($resource, 'module' => 'object', 'action' => 'addDigitalObject')) ?></li>
			          <?php endif; // has digital object ?>
	              <?php endif; // has digital object ?>
              
              <?php elseif (QubitDigitalObject::isUploadAllowed()): ?>
	              <?php if (5 > count($resource->digitalObjectsRelatedByobjectId)): //only allow max 5 digital objects ?>
	                <li><?php echo link_to(__('Link %1%', array('%1%' => mb_strtolower(sfConfig::get('app_ui_label_digitalobject')))), array($resource, 'module' => 'object', 'action' => 'addDigitalObject')) ?></li>
	              <?php endif; // has digital object ?>
              <?php endif; // has digital object ?>

              <?php if ((null === $resource->repository || 0 != $resource->repository->uploadLimit) && QubitDigitalObject::isUploadAllowed()): ?>
                <li><?php echo link_to(__('Import digital objects'), array($resource, 'module' => 'informationobject', 'action' => 'multiFileUpload')) ?></li>
              <?php endif; // upload quota is non-zero ?>

              <li class="divider"></li>

              <li><?php echo link_to(__('Create new rights'), array($resource,  'sf_route' => 'slug/default', 'module' => 'right', 'action' => 'edit')) ?></li>
              <?php if ($resource->hasChildren()): ?>
                <li><?php echo link_to(__('Manage rights inheritance'), array($resource,  'sf_route' => 'slug/default', 'module' => 'right', 'action' => 'manage')) ?></li>
              <?php endif; ?>

              <?php if (sfConfig::get('app_audit_log_enabled', false)): ?>
                <li class="divider"></li>

                <li><?php echo link_to(__('View modification history'), array($resource, 'module' => 'informationobject', 'action' => 'modifications')) ?></li>
              <?php endif; ?>
              <li class="divider"></li>

			<?php $bookinRecords && $bookoutRecords == 0 ?>
			<?php ($bookinRecords = count(get_component('bookinobject', 'contextMenu', array($resource, 'module' => 'informationobject', 'action' => 'editBookinObjects'),array('resource' => $resource)))) ?>
			<?php ($bookoutRecords = count(get_component('bookoutobject', 'contextMenu', array($resource, 'module' => 'informationobject', 'action' => 'editBookoutObjects'),array('resource' => $resource)))) ?>
	
			<?php if ( $bookoutRecords == 0): ?>
				<?php if (QubitAcl::check($resource, 'bookOut')): ?>
					<li><?php echo link_to(__('Book Out'), array($resource, 'module' => 'informationobject', 'action' => 'editBookoutObjects')) ?></li>
				<?php endif; // Display correct button ?>
			<?php elseif( !$bookoutRecords == 0 && $bookinRecords == 0 ) : ?>
				<?php  $this->bookoutObjects = array() ?>
				<?php  foreach (QubitRelation::getRelatedSubjectsByObjectId('QubitBookoutObject', $resource->id, array('requestorId' => QubitTaxonomy::BOOKOUT_TYPE_ID)) as $item): ?>
			    <?php  $this->bookoutObjects[$item->id] = $item ?>
					<?php if (QubitAcl::check($resource, 'rebookOut')): ?>
						<li><?php echo link_to(__('Re-Book Out'), array($item, 'module' => 'bookoutobject', 'action' => 'editBookout')) ?></li>			
					<?php endif; // Display correct button ?>
					<?php if (QubitAcl::check($resource, 'rebookOut')): ?>
						<li><?php echo link_to(__('Book In'), array($item, 'module' => 'informationobject', 'action' => 'editBookinObjects' )) ?></li>
					<?php endif; // Display correct button ?>
				<?php endforeach; ?>
			<?php elseif( !$bookinRecords == 0 ) : ?>
				<?php if (QubitAcl::check($resource, 'bookOut')): ?>
	     			<li><?php echo link_to(__('Book Out'), array($resource, 'module' => 'informationobject', 'action' => 'editBookoutObjects' )) ?></li>
				<?php endif; // Display correct button ?>
			<?php else: ?>
     			<li><?php echo link_to(__('last step'), array($resource, 'module' => 'informationobject', 'action' => 'editBookoutObjects' )) ?></li>
			<?php endif; // Display correct button ?>
			
			<?php //Display preservation Add button only if no preservation exist Johan Pieterse SITA 19 June 2014 ?>		
			<?php $preservationRecords == 0 ?>	
			<?php //Count to see number of records with preservation ?>
			<?php $preservationRecords = count(get_component('presevationobject', 'contextMenu', array($resource, 'module' => 'informationobject', 'action' => 'editPresevationObjects'),array('resource' => $resource))) ?>

			<?php if ( $preservationRecords == 0 ): ?>
				<?php if (QubitAcl::check($resource, 'createPreservation')): ?>
					<li><?php echo link_to(__('Add Presevation'), array($resource, 'module' => 'informationobject', 'action' => 'editPresevationObjects')) ?></li>
				<?php endif; // Display correct button ?>
			<?php else: ?>
				<?php $this->presevationObjects = array() ?>
				<?php foreach (QubitRelation::getRelatedSubjectsByObjectId('QubitPresevationObject', $resource->id, array('typeId' => QubitTaxonomy::PRESERVATION_TYPE_ID)) as $item): ?>
				<?php   $this->presevationObjects[$item->id] = $item ?>
					<?php if (QubitAcl::check($resource, 'editPreservation')): ?>
						<li><?php echo link_to(__('Edit Presevation'), array($item, 'module' => 'presevationobject', 'action' => 'editPreservation')) ?></li>
					<?php endif; // Display correct button ?>
				<?php endforeach; ?>
			<?php endif; // Display correct button ?>

			<?php //Display Access Add button only if no access exist Johan Pieterse SITA 19 August 2014 ?>		
			<?php $accessRecords == 0 ?>	
			<?php //Count to see number of records with Access ?>
			<?php $accessRecords = count(get_component('accessobject', 'contextMenu', array($resource, 'module' => 'informationobject', 'action' => 'editAccessObjects'),array('resource' => $resource))) ?>

			<?php if ( $accessRecords == 0 ): ?>
				<?php if (QubitAcl::check($resource, 'createAccess')): ?>
					<li><?php echo link_to(__('Add Access'), array($resource, 'module' => 'informationobject', 'action' => 'editAccessObjects')) ?></li>
				<?php endif; // Display correct button ?>
			<?php else: ?>
				<?php $this->accessObjects = array() ?>
				<?php foreach (QubitRelation::getRelatedSubjectsByObjectId('QubitAccessObject', $resource->id, array('typeId' => QubitTaxonomy::ACCESS_TYPE_ID)) as $item): ?>
			    <?php   $this->accessObjects[$item->id] = $item ?>
					<?php if (QubitAcl::check($resource, 'createAccess')): ?>
						<li><?php echo link_to(__('Edit Access'), array($item, 'module' => 'accessobject', 'action' => 'editAccess')) ?></li>
					<?php endif; // Display correct button ?>
				<?php endforeach; ?>
			<?php endif; // Display correct button ?>

			<?php // Find if it is published
				foreach ($resource->getAccessObjects() as $item)
				{ 
					if (isset($item->published)) 
					{  
					 if ($item->published == 1)
					 {
			 ?>
					 	<li><?php echo link_to(__('Unpublish'), array($item, 'module' => 'reports', 'action' => 'browseUnPublish')) ?></li>
<?php
					 }
					}
				}
			?>
			<?php if (QubitAcl::check($resource, 'auditTrail')): ?>
				<li><?php echo link_to(__('Audit Trail'), array('module' => 'reports', 'action' => 'auditArchivalDescription', 'source' => $resource->id)) ?></li>
			<?php endif; // Display correct button ?>


            </ul>
          </div>
        </li>

        <li>
          <input type="button" id="fullwidth-treeview-reset-button" class="c-btn c-btn-submit" value="<?php echo __('Reset') ?>" />
          <input type="button" id="fullwidth-treeview-more-button" class="c-btn c-btn-submit" data-label="<?php echo __('%1% more') ?>" value="" />
          <span id="fullwidth-treeview-collection-url" data-collection-url="<?php echo url_for(array($resource->getCollectionRoot(), 'module' => 'informationobject')) ?>"></span>
        </li>

      <?php endif; // user has update permission ?>

  </ul>
</section>
