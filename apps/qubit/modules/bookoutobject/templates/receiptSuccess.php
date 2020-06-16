<?php $path = sfConfig::get('sf_relative_url_root', preg_replace('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : ''))) ?>
<div id="preview-message">
  <?php echo __('Print preview') ?>
  <?php echo link_to('Close', array($resource, 'module' => 'bookoutobject')) ?>
</div>
<thead>
	<table border="0">
		<div id="logo-floater">
			<br>
			<tr>
				<td width="20%"><h1><img alt="" id="logo" src="<?php echo $path ?>/images/dacLogo.png"/><div></div></h1></td>
				<td><table border="0"><tr><td align="right" width="80%"><h4>PRIVATE BAG X897 PRETORIA 0001 SOUTH AFRICA T +27 12 441 3000 F +27 12 441 3699<BR>PRIVATE BAG X9015 CAPE TOWN 8000 SOUTH AFRICA T +27 21 465 5620 F +27 21 465 5624</h4></td></table></td>
			</tr>
		</div>
	</table>
	 <div align="right">SS/007/01NASA</div> 
		<tr>
			<center><td><h1>REMOVAL PERMIT FOR RECORDS</h1></td></center>
		</tr>


</thead>

<tbody>
	<div>
		<tr><td>REMOVAL OF RECORDS FROM THE NARSSA PREMISES</td></tr><br>
		<tr><td>(Note:<i><b> This authorisation must be handed to the security officer on duty at the access control point for inspection before exiting the National Archives and Records Service of South Africa's premises)</i></b></td></tr><br>
		<tr><td>PREMISSION HAS BEEN GRANTED TO THE FOLLOWING OFFICIAL TO REMOVE THE RECORDS INDICATED BELOW FROM THE NARSSA PREMISIS:</td></tr><br><br><br>

		<tr><td>Requestor Name & Surname: </td>______________________________________________<td>Date: </td><td><u><?php echo date("d-m-Y") ?></u></td></tr><br><br>
		<tr><td>Time: </td>______________________________________________<td>Department/Section:</td><td>______________________________________________</td></tr><br><br>
		<tr><td>Contact Number: </td>______________________________________________<td>Signature:</td><td>______________________________________________</td></tr><br><br>
	<table border="1">
		<tr>
			<td bgcolor="#d4d9d6">DESCRIPTION OF RECORDS</td>
			<td bgcolor="#d4d9d6">FILE REFERENCE NUMBER</td>
			<td bgcolor="#d4d9d6">REMARK</td>
			<td bgcolor="#d4d9d6">CONDITION</td>
			<td bgcolor="#d4d9d6">RETURN<br>Date/Time</td>
			<td bgcolor="#d4d9d6">FOUND (Yes/No)<br>TBC/NARSSA</td>
		</tr>

     <?php 
     
     foreach ($pager->getResults() as $result): ?>
		<tr>
			<?php if (isset($resource)) { ?> <td><?php echo $resource ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
			<?php if (isset($resource->identifier)) { ?> <td><?php echo $resource->identifier ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getRemarks(array('cultureFallback' => true)) ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
			<?php if (isset($result->record_condition)) { ?> <td><?php echo $result->record_condition ?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
			<?php if (isset($result->id)) { ?> <td><?php 
				$date = $result->getTime_period(array('cultureFallback' => true));
				$date = str_replace('/', '-', $date);

				$date = date('Y-m-d', strtotime("+1 day"));
				echo date('d-m-Y', strtotime("+1 day", strtotime($date)));
			
			?></td> <?php } else { ?> <td>-</td> <?php }	?></td>
		</tr>
      <?php endforeach; ?>
	</table>
	</div> 
	<div>
	<br><br>
      <table border="1">

		<tr>
			<td>Item(s) will be temprorarily removed<br><i><b>(Indicate with an "X")</b></i></td>
			<td><p style="border:1px; border-style:solid; border-color:#000000; padding: 1em;">YES</p><p style="border:1px; border-style:solid; border-color:#000000; padding: 1em;"> NO</p></td>
			<td>Item(s) will be permanently removed<br><i><b>(Indicate with an "X")</b></i></td>
			<td><p style="border:1px; border-style:solid; border-color:#000000; padding: 1em;">YES</p><p style="border:1px; border-style:solid; border-color:#000000; padding: 1em;"> NO</p></td>
		</tr>
      </table>
	</div>
	<br>
	<p style="page-break-after: always;">&nbsp;</p>
	<br>
	<br>
	<div>
		<tr><td><b>RECOMMENDED BY</b> (<i>To be completed by NARSSA/Provincial Archive)</i>):</td></tr><br><br><br>
		<tr><td>Name & Surname: </td>______________________________________________<td>  Signature: </td><td>______________________________________________</td></tr><br><br>
		<tr><td>Date: </td>______________________________________________<td>  Contact Number:</td><td>______________________________________________</td></tr><br><br><br>

		<tr><td><b>APPROVED BY SECTION HEAD</b> (<i>To be completed by NARSSA/Provincial Archive)</i>):</td></tr><br><br><br>
		<tr><td>Name & Surname: </td>_____________________________________<td>  Signature: </td><td>_____________________________________</td></tr><br><br>
		<tr><td>Date: </td>_____________________________________<td>  Contact Number:</td><td>_____________________________________</td></tr><br><br>

		<tr><td><b>Removal of items</b> (<i>To be completed by NARSSA/Provincial Archive)</i>):</td></tr><br><br><br>
		<tr><td>Name & Surname: </td>_____________________________________<td>  Signature: </td><td>_____________________________________</td></tr><br><br>
		<tr><td>Date: </td>_____________________________________<td>  Contact Number:</td><td>_____________________________________</td></tr><br><br>

		<tr><td><b><u>Return of items:</u></b></td></tr><br><br>
		<tr><td>I (<i><b>Name & Surname of Official Returning the Records</b></i>): </td>____________________________________________<td>  declare that I returned the item(s) indicated in this Removal Permit. </td><td></td></tr><br><br>
		<tr><td>Signature: </td>_____________________________________<td>  Date:</td><td>_____________________________________</td></tr><br><br>

		<tr><td>I (<i><b>Name & Surname of Official Security Officer on duty</b></i>): </td>____________________________________________<td>  declare that the item(s) indicated was/were returned by the person indicated and authorised on this Removal Permit in this Removal Permit. </td><td></td></tr><br><br>
		<tr><td>Signature: </td>_____________________________________<td>  Date:</td><td>_____________________________________</td></tr><br><br>
	</div>
  </tbody>
</table>
<table border="2">
	<div>
		<tr>
			<td height="200" width="50%" align="center"><i>Official stamp by client office upon requesting the records</i>
			</td>&nbsp&nbsp&nbsp&nbsp
			<td height="200" width="50%" align="center"><i>Official stamp by National/Provincial Archive upon approving of the records to be removed from the premisis</i>
			</td>
		</tr>
		<br><br>

	</div>
</table>


