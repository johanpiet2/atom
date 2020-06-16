<h1><?php echo __('Audit') ?></h1>

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
 
 <?php foreach ($pager->getResults() as $item): ?>
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
		
	<?php } else if ($item["DB_TABLE"] == 'QubitDeaccession') { ?>
		<td><?php echo "Deaccession" ?></td> 
		
	<?php } else { ?>
		<td><?php echo "Unknown" ?></td> 
		
	<?php } ?>
	
	- Archival Description (Deleted)
    <?php endforeach; ?>
 <?php foreach ($pager->getResults() as $item): ?>
       <tr>
        <td>
			<table border=1>
		
			<tr>
				<td>ID</td> <td><?php echo $item["ID"] ?></td>
			</tr>
			<tr>
				<td>User</td> <td><?php echo $item["USER"] ?></td>
			</tr>
			<tr>
				<td>Date & Time</td> <td><?php echo $item["ACTION_DATE_TIME"] ?></td>
			</tr>
			
			<tr>
				<td colspan=3>
			<?php echo '<b>DB QUERY: </b>' . "<br>" ?>  
			</tr>
			<tr>
				<?php $strFieldsAndValues = explode("||",$item["DB_QUERY"]) ?> 
				<?php $strFields = explode("~",$strFieldsAndValues[0]) ?> 
				<?php $strValues = explode("~",$strFieldsAndValues[1]) ?>
				<?php $arr_length = count($strFields); ?>
				<?php for($i=0;$i<$arr_length;$i++) { ?>
					<?php $strOlder = doGetFieldValue($strFields[$i],$auditObjectsArr,$item["ID"], $item["DB_TABLE"]); ?>
					<?php echo "<td><i>". $strFields[$i] . "</i></td><td>" . $strValues[$i] . "</td><tr>" ?>
				<?php } ?>
      </tr>
 
 			</table>

        </td>
      </tr>
		<?php //endif; ?>
    <?php endforeach; ?>
  </tbody>
</table>

    <section class="actions">
      <ul>
		<li><input class="c-btn c-btn-submit" type="button" onclick="history.back();" value="Back"></li>
      </ul>
    </section>

<?php 
function doGetFieldValue($keyValue, $auditObjectsArr2, $item_ID, $itemTable)
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
			$strFieldsAndValuesOlder2 = explode("~~~",$auditObjectsArr2[$n][9]);
			$strFieldsOlder2 = explode("~!~",$strFieldsAndValuesOlder2[0]); 
			$strValuesOlder2 = explode("~!~",$strFieldsAndValuesOlder2[1]); 

			if ($item_ID > $auditObjectsArr2[$n][2] )   //Check for ID to be older than current ID
			{
 				if ($itemTable == $auditObjectsArr2[$n][8] )   //same tables
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
?>

