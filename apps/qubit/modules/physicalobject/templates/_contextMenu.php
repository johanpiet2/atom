<section id="physical-objects">

  <h4><?php echo sfConfig::get('app_ui_label_physicalobject') ?></h4>

  <div class="content">
    <ul>

      <?php foreach ($physicalObjects as $item): ?>
			<?php $informationObj = QubitInformationObject::getById($resource->id);?>
		<table>
			<!--tr>
				<td>
		      		<li><?php echo "Name: " . link_to_if(QubitAcl::check($resource, 'update'), render_title($item), array($resource, 'module' => 'informationobject', 'action' => 'editPhysicalObjects')) ?>
				</td>
			</tr-->
			<tr>
				<td>
				  <?php if (isset($item->location) && $sf_user->isAuthenticated()): ?>
				  	<?php echo "Location: " . $item->getLocation(array('cultureFallback' => 'true')) ?>	
				  <?php else: ?>
				  	<?php echo "Location: -"  ?>	
				  <?php endif; ?>	
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($informationObj->shelf) && $sf_user->isAuthenticated()): ?>
				  	<?php echo "Shelf: " . $informationObj->shelf ?>			 
				  <?php else: ?>
				  	<?php echo "Shelf: -"  ?>	
				  <?php endif; ?>	
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($informationObj->row) && $sf_user->isAuthenticated()): ?>
				  	<?php echo "Row: " . $informationObj->row ?>			 
				  <?php else: ?>
				  	<?php echo "Row: -" ?>	
				  <?php endif; ?>	
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($informationObj->row) && $sf_user->isAuthenticated()): ?>
				  	<?php echo "Bin/Box: " . $informationObj->row ?>			 
				  <?php else: ?>
				  	<?php echo "Bin/Box: -" ?>	
				  <?php endif; ?>	
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($item->uniqueIdentifier) && $sf_user->isAuthenticated()): ?>
				  	<?php echo "Identifier: " . $item->getUniqueIdentifier(array('cultureFallback' => 'true')) ?>
				  <?php else: ?>
				  	<?php echo "Identifier: -" ?>	
				  <?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($item->descriptionTitle) && $sf_user->isAuthenticated()): ?>
				  	<?php echo "Description: " . $item->getDescriptionTitle(array('cultureFallback' => 'true')) ?>
				  <?php else: ?>
				  	<?php echo "Description: -" ?>	
				  <?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($item->periodCovered) && $sf_user->isAuthenticated()): ?>
				  	<?php //echo "Period: " . $item->getPeriodCovered(array('cultureFallback' => 'true')) ?>
				  <?php else: ?>
				  	<?php //echo "Period: -" ?>	
				  <?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($item->extent) && $sf_user->isAuthenticated()): ?>
				  	<?php //echo "Extent: " . $item->getExtent(array('cultureFallback' => 'true')) ?>
				  <?php else: ?>
				  	<?php //echo "Extent: -" ?>	
				  <?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($item->findingAids) && $sf_user->isAuthenticated()): ?>
				  	<?php //echo "Finding Aids: " . $item->getFindingAids(array('cultureFallback' => 'true')) ?>
				  <?php else: ?>
				  	<?php //echo "Finding Aids: -" ?>	
				  <?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($item->accrualSpace) && $sf_user->isAuthenticated()): ?>
				  	<?php //echo "Space: " . $item->getAccrualSpace(array('cultureFallback' => 'true')) ?>
				  <?php else: ?>
				  	<?php //echo "Space: -" ?>	
				  <?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($item->forms) && $sf_user->isAuthenticated()): ?>
				  	<?php //echo "Forms: " . $item->getForms(array('cultureFallback' => 'true')) ?>
				  <?php else: ?>
				  	<?php //echo "Forms: -" ?>	
				  <?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
				  <?php if (isset($item->type)): ?>
				    <?php //echo "Type: " . $item->type ?>
				  <?php else: ?>
				  	<?php //echo "Type: -" ?>	
				  <?php endif; ?>
				</td>
			</tr>
        </li>
      <?php endforeach; ?>
</table>	
    </ul>
  </div>

</section>
