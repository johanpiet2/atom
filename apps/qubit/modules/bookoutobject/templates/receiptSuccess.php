<div id="preview-message">
  <?php echo __('Print preview') ?>
  <?php echo link_to('Close', array($resource, 'module' => 'bookoutobject')) ?>
</div>

<h1 class="do-print"><?php echo sfConfig::get('app_ui_label_bookoutobject') ?></h1>

<h1 >
  <?php echo $resource ?>
</h1>

<table class="sticky-enabled">
	<tbody>
     <?php 
     
     foreach ($pager->getResults() as $result): ?>
      <table>
		<tr>
			<td><?php echo __('Identifier') ?></td><td><?php if (isset($resource->identifier)) { ?> <td><?php echo $resource->identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Title/Description') ?></td><td><?php if (isset($resource)) { ?> <td><?php echo $resource ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Unique Identifier') ?></td><td><?php if (isset($result->unique_identifier)) { ?> <td><?php echo $result->unique_identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Dispatcher') ?></td><td><?php if (isset($result->dispatcherId)) { ?> <td><?php echo $result->dispatcherId ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Receiver type') ?></td><td><?php if (isset($result->requestor_type)) { ?> <td><?php 
			if ($result->requestor_type == 1) {
				echo "Researcher";
			} else if ($result->requestor_type == 2) {
				echo "Authority record/Client office";
			} else {
				echo "Service provider";
			}
			 ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Receiver Name') ?></td><td><?php if (isset($result->requestor_type)) { ?> <td><?php 
			if ($result->requestor_type == 1) {
				$receiver = QubitResearcher::getById($result->service_provider);
			} else if ($result->requestor_type == 2) {
				$receiver = QubitActor::getById($result->service_provider);
			} else {
				$receiver = QubitServiceProvider::getById($result->service_provider);
			}
			echo $receiver;
			 ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Contact person') ?></td><td></td><?php 
			
			if (isset($result->requestor_type)) { ?> 
				<?php 
				$receiver = QubitContactInformation::getByActorId($result->service_provider);
				if (isset($receiver->contactPerson)) { ?> <td><?php 
					echo $receiver->contactPerson;
				 ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
			 <?php }  else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Street address') ?></td><td></td><?php 
			
			if (isset($result->requestor_type)) { ?> 
				<?php 
				$receiver = QubitContactInformation::getByActorId($result->service_provider);
				if (isset($receiver->streetAddress)) { ?> <td><?php 
					echo $receiver->streetAddress;
				 ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
			 <?php }  else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Telephone number') ?></td><td></td><?php 
			
			if (isset($result->requestor_type)) { ?> 
				<?php 
				$receiver = QubitContactInformation::getByActorId($result->service_provider);
				if (isset($receiver->telephone)) { ?> <td><?php 
					echo $receiver->telephone;
				 ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
			 <?php }  else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Cellphone number') ?></td><td></td><?php 
			
			if (isset($result->requestor_type)) { ?> 
				<?php 
				$receiver = QubitContactInformation::getByActorId($result->service_provider);
				if (isset($receiver->cell)) { ?> <td><?php 
					echo $receiver->cell;
				 ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
			 <?php }  else { ?> <td>-</td> <?php }	?></td>
		</tr>

		<tr>
			<td><?php echo __('Return date/time') ?></td><td><?php if (isset($result->id)) { ?> <td><?php 
				$date = $result->getTime_period(array('cultureFallback' => true));
				$date = str_replace('/', '-', $date);

				$date = date('Y-m-d', strtotime("+1 day"));
				echo date('d-m-Y', strtotime("+1 day", strtotime($date)));
			
			?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Remarks') ?></td><td><?php if (isset($result->id)) { ?> <td><?php echo $result->getRemarks(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php echo __('Condition') ?></td><td><?php if (isset($result->record_condition)) { ?> <td><?php echo $result->record_condition ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
      <?php endforeach; ?>
      </table>
      <table>
		<tr>
			<td height="100"></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo "_________________________" ?></td>
			<td></td>
			<td><?php echo "_________________________" ?></td>
			<td></td>
			<td><u><?php echo date("d-m-Y") ?></u></td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo "Name" ?></td>
			<td></td>
			<td><?php echo "Signature" ?></td>
			<td></td>
			<td><?php echo "Date" ?></td>
		</tr>

      </table>

  </tbody>
</table>

