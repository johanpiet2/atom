<div>
  <h3><?php echo sfConfig::get('app_ui_label_bookinobject') ?></h3>
    <h2><?php echo __('Bookin Items') ?></h2>
  <div class="content">
    <ul>
      <?php foreach ($bookinObjects as $item): ?>
	  <table>
		<tr>
			<td>
			<li>
				Title: <?php echo link_to_if(QubitAcl::check($resource, 'update'), render_title($item), array($item, 'module' => 'bookinobject'))?> 
					   <?php echo  '<img src="/var/www/prod/images/yellow.png" width="16" height="16">'.'<b><font color="Green">'."This item is available" ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php if (isset($item->requestor)): ?>
				Requestor: <?php echo $item->requestor ?>
				<?php endif; ?>
			</td>
		</tr>		
		<tr>
			<td>
				<?php if (isset($item->receiver)): ?>
				Receiver: <?php echo $item->receiver ?>
				<?php endif; ?>	
			</td>		
	   </tr>
	   <tr>
			<td>
				<?php if (isset($item->time_period) && $sf_user->isAuthenticated()): ?>
      			Return Date: <?php echo $item->getTime_period(array('cultureFallback' => 'true')) ?>
				<?php endif; ?>
			</td>
		</tr>		  
        </li>
      <?php endforeach; ?>
	 </table> 
    </ul>
  </div>
</div>
