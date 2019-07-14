<section id="bookout-objects">
	<h3><?php echo __('Booked out') ?></h3>

	<div class="content">
		<?php foreach ($bookoutObjects as $item): ?>
			<table>
				<tr>
					<td>
						<?php echo  '<img src="../images/cancel48.png" width="16" height="16">'.'<b><font color="Red">'."This item is not available" ?>				
					</td>
				</tr>
				<tr>	 
					<td>
						<?php if (isset($item->dispatcherId)): ?>
							Dispatcher: <?php echo $item->dispatcherId ?>
						<?php else: ?>
							<?php echo "Dispatcher: -"  ?>	
						<?php endif; ?>		  
					</td>
				</tr>
				<tr>
					<td>
						<?php if (isset($item->requestorId)): ?>
							Requestor: <?php echo $item->requestorId ?>
						<?php endif; ?>	
					</td>
				</tr>
				<tr>
					<td>
						<?php 
							$query = QubitBookoutObjectI18n::getById($item->id);
							if ($query->requestor_type == "3"): //service provider
								$sp = QubitServiceProvider::getById($query->service_provider);
							elseif($query->requestor_type == "1"): //researcher
								$sp = QubitResearcher::getById($query->service_provider);
							elseif($query->requestor_type == "2"): //authority record
								$sp = QubitActor::getById($query->service_provider);
							else:

							endif;						
						?>

						<?php if (isset($sp)): ?>
							<?php if ($query->requestor_type == "3"): ?>
								Service Provider: <?php echo $sp ?>
							<?php elseif($query->requestor_type == "1"): ?>
								Researcher: <?php echo $sp ?>
							<?php elseif($query->requestor_type == "2"): ?>
								Authority Record: <?php echo $sp ?>
							<?php endif;	?>	
						<?php else: ?>
							<?php echo "Requestor: -"  ?>	
						<?php endif; ?>	
					</td>
				</tr>
				<tr>
					<td>
					</td>
				</tr>
				<tr>
					<td>
						<?php if (isset($item->time_period) && $sf_user->isAuthenticated()): ?>
							Dispatched: <?php echo $item->getTime_period(array('cultureFallback' => 'true')) ?>
						<?php else: ?>
							<?php echo "Dispatched: -"  ?>	
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php if (isset($item->record_condition) && $sf_user->isAuthenticated()): ?>
							Record Condition: <?php echo $item->getRecord_condition(array('cultureFallback' => 'true')) ?>
						<?php else: ?>
							<?php echo "Record Condition: -"  ?>	
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php if (isset($item->remarks) && $sf_user->isAuthenticated()): ?>
							Remarks: <?php echo $item->remarks ?>
						<?php else: ?>
							<?php echo "Remarks: -"  ?>	
						<?php endif; ?>
					</td>
				</tr>
			</table>  
		<?php endforeach; ?>
	</div>
</section>
