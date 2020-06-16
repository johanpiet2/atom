<?php $path = sfConfig::get('sf_relative_url_root', preg_replace('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : ''))) ?>
<div id="preview-message">
  <?php echo __('Print preview') ?>
  <?php echo link_to('Close', array($resource, 'module' => 'bookoutobject')) ?>
</div>

<tbody>
	<div id="logo-floater">
		<tr>
			<td><h1><img alt="" id="logo" src="<?php echo $path ?>/images/dacLogo.png"/><div></div></h1></td><td><h3>The address</h3></td>
		</tr>
	</div>

	<div>
		<tr><td>REMOVAL OF RECORDS FROM THE NARSSA PREMISES</td></tr><br>
		<tr><td>(Note:<i><b> This authorisation must be handed to the security officer on duty at the access control point for inspection before exiting the National Archives and Records Service of South Africa's premises)</i></b></td></tr><br>
		<tr><td>PREMISSION HAS BEEN GRANTED TO THE FOLLOWING OFFICIAL TO REMOVE THE RECORDS INDICATED BELOW FROM THE NARSSA PREMISIS:</td></tr><br><br><br>

		<tr><td>Requestor Name & Surname: </td>_________________________<td>Date: </td><td>____________________________</td></tr><br><br>
		<tr><td>Time: </td>_________________________<td>Department/Section:</td><td>____________________________</td></tr><br><br>
		<tr><td>Contact Number: </td>_________________________<td>Signature:</td><td>____________________________</td></tr><br><br>
	<table border="1">
		<tr>
			<td rowspan="2" bgcolor="#d4d9d6">DESCRIPTION OF RECORDS</td>
			<td rowspan="2" bgcolor="#d4d9d6">FILE REFERENCE NUMBER</td>
			<td rowspan="2" bgcolor="#d4d9d6">FOUND (Yes/No)<br>TBC/NARSSA</td>
		</tr>

     <?php 
     
     foreach ($pager->getResults() as $result): ?>
		<tr>
			<td><?php if (isset($resource)) { ?> <td><?php echo $resource ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
		<tr>
			<td><?php if (isset($resource->identifier)) { ?> <td><?php echo $resource->identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
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
	</div> 


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

