<h1><?php echo __('Audit Physical Storage') ?></h1>

<table class="sticky-enabled">
  <thead>
    <tr>
      <th>

      </th>
    </tr>
  </thead><tbody>    
    <section class="actions">
      <ul>
		<li><input class="c-btn c-btn-submit" type="button" onclick="history.back();" value="Back"></li>
      </ul>
    </section>
	<?php $auditObjectsArr = array(); ?>
  	<?php foreach ($auditObjectsOlder as $item):  ?>
		<?php $auditObjectsArr[] = array($item[0],$item[1],$item[2],$item[3],$item[4],$item[5],$item[6],$item[7],$item[8],$item[9]); ?>
    <?php endforeach;  ?>

  	<?php foreach ($auditObjects as $item): ?>
  	
       <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
    		<?php echo "<hr>" ?>
			<table border=1>
			<tr>
				<td colspan=3>
					<b>Record ID: 
					<?php echo $item[1] ?>
					</b>
				</td>
			</tr>
			<tr>
				<td>Name</td><td colspan=2><?php echo $item[8] . " - " . $item[9] ?></td> 
			</tr>
			
			<?php if ($item["USER_ACTION"] == "insert"): ?> 
				<?php $dAction = "Inserted into " ?> 
			<?php elseif ($item["USER_ACTION"] == "update"): ?>
				<?php $dAction = "Updated " ?> 
			<?php elseif ($item["USER_ACTION"] == "delete"): ?>
				<?php $dAction = "Deleted from" ?> 
			<?php else: ?>
				<?php $dAction = $item["USER_ACTION"] ?> 
			<?php endif; ?>
 
			<?php if ($item["DB_TABLE"] == "physical_object"): ?> 
				<?php $dTable = "Physical Storage" ?> 
			<?php elseif ($item["DB_TABLE"] == "physical_object_i18n"): ?> 
				<?php $dTable = "Physical Storage Extend" ?> 
			<?php else: ?>
				<?php $dTable = $item["DB_TABLE"] ?> 
			<?php endif; ?>
 
 			<?php $user = ""; ?>
 			<?php $date = ""; ?>


			<?php $rOlder = doGetTableValue($auditObjectsArr, $item["ID"], $item["DB_TABLE"]); ?>
			<?php $rOlderValues = explode("~!~",$rOlder); ?>
			<?php $dTableOlder = $rOlderValues[0] ?> 
			<?php $dActionOlder = $rOlderValues[1] ?> 
			<?php $user = $rOlderValues[2]; ?>
			<?php $date = $rOlderValues[3]; ?>
			
			<tr>
				<td><b>Field</b></td> <td><b>Old Value</b</td> <td><b>New Value</b</td> 
			</tr>
			<tr>
				<td>ID</td> <td><?php //echo $item[0] ?></td> <td><?php echo $item[0] ?></td> 
			</tr>
			<tr>
				<td>User</td> <td><?php echo $user ?></td> <td><?php echo $item[6] ?></td> 
			</tr>
			<tr>
				<td>Date & Time</td> <td><?php echo $date ?></td> <td><?php echo $item["ACTION_DATE_TIME"] ?></td> 
			</tr>
			<tr>
				<td>Action</td> <td><?php echo $dActionOlder . $dTableOlder ?></td> <td><?php echo $dAction . $dTable ?></td> 
			</tr>
			
			<tr>
				<td colspan=3>
			<?php echo '<b>DB QUERY: </b>' . "<br>" ?>  
			</tr>
			<tr>
				<?php $strFieldsAndValues = explode("~~~",$item["DB_QUERY"]) ?> 
				<?php $strFields = explode("~!~",$strFieldsAndValues[0]) ?> 
				<?php $strValues = explode("~!~",$strFieldsAndValues[1]) ?>
				<?php $arr_length = count($strFields); ?>
				<?php for($i=0;$i<$arr_length;$i++) { ?>


					<?php //echo $strFields[$i] . " " . $strValues[$i] ."<br>"?> 



					<?php $strValue = $strValues[$i] ?> 
					<?php if (trim($strFields[$i]) == "NAME"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Name</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("NAME",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
						<?php endif; ?>

					<?php elseif (trim($strFields[$i]) == "LOCATION"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Location</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("LOCATION",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
						<?php endif; ?>

					<?php elseif (trim($strFields[$i]) == "REPOSITORY_ID"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Repository</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("REPOSITORY_ID",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . QubitRepository::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitRepository::getById($strValues[$i]) . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . QubitRepository::getById($strOlder) . "</td><td>" . QubitRepository::getById($strValues[$i]) . "</td><tr>" ?> 
						<?php endif; ?>




					<?php elseif (trim($strFields[$i]) == "UNIQUEIDENTIFIER"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Unique Identifier</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("UNIQUEIDENTIFIER",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
						<?php endif; ?>

					<?php elseif (trim($strFields[$i]) == "DESCRIPTIONTITLE"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Description Title</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("DESCRIPTIONTITLE",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
						<?php endif; ?>

					<?php elseif (trim($strFields[$i]) == "PERIODCOVERED"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Period Covered</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("PERIODCOVERED",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
						<?php endif; ?>

					<?php elseif (trim($strFields[$i]) == "EXTENT"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Extend</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("EXTENT",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
						<?php endif; ?>

					<?php elseif (trim($strFields[$i]) == "ACCRUALSPACE"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Accrual Space</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("ACCRUALSPACE",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
						<?php endif; ?>

					<?php elseif (trim($strFields[$i]) == "FORMS"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Forms</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("FORMS",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
						<?php endif; ?>

					<?php elseif (trim($strFields[$i]) == "CULTURE"): ?>
						<?php echo "<td><i>Field</i></td><td colspan=2>Culture</td><tr>" ?> 
						<?php $strOlder = doGetFieldValue("CULTURE",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
						<?php if ($strOlder != $strValues[$i]): ?>
							<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
						<?php else: ?>
								<?php if ($strFields[$i] != 'ID'): ?>
									<?php $strOlder = doGetFieldValue($strFields[$i],$auditObjectsArr,$item["ID"], $item[7], $item[6]); ?>
									<?php echo "<td><i>". $strFields[$i] . "</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?>
								<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php } ?>
			  </tr>
			</table>
        </td>
      </tr>
		<?php //endif; ?>
    <?php endforeach; ?>
  </tbody>
</table>

<div id="result-count">
  <?php echo __('Showing %1% results', array('%1%' => $foundcount)) ?>
</div>

    <section class="actions">
      <ul>
		<li><input class="c-btn c-btn-submit" type="button" onclick="history.back();" value="Back"></li>
      </ul>
    </section>

<?php 
function doGetFieldValue($keyValue, $auditObjectsArr2, $item_ID, $item, $item4)
{
	try 
	{
		$oValue = "";
		
		$arrSize = sizeof($auditObjectsArr2);
		//$arrSize = $arrSize - 1;
	 
		for ($n = 0; $n < $arrSize; $n++) 
		{
			if ($oValue != "")
			{
				break;
			}
			
			$strFieldsAndValuesOlder2 = explode("~~~",$auditObjectsArr2[$n][5]);
			$strFieldsOlder2 = explode("~!~",$strFieldsAndValuesOlder2[0]); 
			$strValuesOlder2 = explode("~!~",$strFieldsAndValuesOlder2[1]); 

			if ($item_ID > $auditObjectsArr2[$n][0] )   //Check for ID to be older than current ID
			{
 				if ($item4 == $auditObjectsArr2[$n][4] )   //same tables
 				{
	 				for ($j=0; $j < count($strFieldsOlder2); $j++) 
					{
 						if ($keyValue == $strFieldsOlder2[$j])
 						{
 							$oValue = $strValuesOlder2[$j];
 							break;
 						}
					}
	 			}
 			}
 		}

		return $oValue;
	} catch (Exception $e) {
		Propel::log($e->getMessage(), Propel::LOG_ERR);
		throw new PropelException("Unable to perform get filed value.", $e);
	}
}

function doGetTableValue($auditObjectsArr2, $item_ID,  $item4)
{
	try 
	{
		$oValue  = "";
		$oAction = "";
		$oTable  = ""; 
		$oUser   = "";
		$oDdate  = ""; 
		
		$arrSize = sizeof($auditObjectsArr2);
		$arrSize = $arrSize - 1;
		for ($n = 0; $n < $arrSize; $n++) 
		{
			$strFieldsAndValuesOlder2 = explode("~~~",$auditObjectsArr2[$n][5]);
			$strFieldsOlder2 = explode("~!~",$strFieldsAndValuesOlder2[0]); 
			$strValuesOlder2 = explode("~!~",$strFieldsAndValuesOlder2[1]); 

			if ($item_ID > $auditObjectsArr2[$n][0] )   //Check for ID to be older than current ID
			{
 				if ($item4 == $auditObjectsArr2[$n][4] )   //same tables
 				{
 					$oAction = $auditObjectsArr2[$n][3]; 
 					$oTable = $auditObjectsArr2[$n][4]; 
 					$oUser = $auditObjectsArr2[$n][6]; 
 					$oDdate = $auditObjectsArr2[$n][7]; 

					break;		 					
	 			}
 			}
 		}

		if ($oAction == "insert")
		{
			$dActionOlder = "Inserted into ";
		}
		elseif ($oAction == "update")
		{
			$dActionOlder = "Updated ";
		}
		elseif ($oAction == "delete")
		{
			$dActionOlder = "Deleted from";
		}
		else
		{
			$dActionOlder = $oAction;
		}
			 
		if ($oTable == "physical_object") 
		{
			$dTableOlder = "Physical Storage";
		}
		elseif ($oTable == "physical_object_i18n")
		{
			$dTableOlder = "Physical Storage Extend"; 
		}
		else
		{
			$dTableOlder = $oTable;
		}
 		
		$rOlder = $dTableOlder . "~!~". $dActionOlder . "~!~". $oUser . "~!~". $oDdate; 

		return $rOlder;
	} catch (Exception $e) {
		Propel::log($e->getMessage(), Propel::LOG_ERR);
		throw new PropelException("Unable to perform get filed value.", $e);
	}
}
?>

