<h1><?php echo __('Audit'); ?></h1>

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
	<?php $auditObjectsArr = []; ?>
 
 <?php foreach ($pager->getResults() as $item) { ?>
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
		
	<?php } elseif ('QubitDeaccession' == $item['DB_TABLE']) { ?>
		<td><?php echo 'Deaccession'; ?></td> 
		
	<?php } else { ?>
		<td><?php echo 'Unknown'; ?></td> 
		
	<?php } ?>
	
	- Archival Description (Deleted)
    <?php } ?>
 <?php foreach ($pager->getResults() as $item) { ?>
       <tr>
        <td>
			<table border=1>
		
			<tr>
				<td>ID</td> <td><?php echo $item['ID']; ?></td>
			</tr>
			<tr>
				<td>User</td> <td><?php echo $item['USER']; ?></td>
			</tr>
			<tr>
				<td>Date & Time</td> <td><?php echo $item['ACTION_DATE_TIME']; ?></td>
			</tr>
			
			<tr>
				<td colspan=3>
			<?php echo '<b>DB QUERY: </b><br>'; ?>  
			</tr>
			<tr>
				<?php $strFieldsAndValues = explode('||', $item['DB_QUERY']); ?> 
				<?php $strFields = explode('~', $strFieldsAndValues[0]); ?> 
				<?php $strValues = explode('~', $strFieldsAndValues[1]); ?>
				<?php $arr_length = count($strFields); ?>
				<?php for ($i = 0; $i < $arr_length; ++$i) { ?>
					<?php $strOlder = doGetFieldValue($strFields[$i], $auditObjectsArr, $item['ID'], $item['DB_TABLE']); ?>
					<?php echo '<td><i>'.$strFields[$i].'</i></td><td>'.$strValues[$i].'</td><tr>'; ?>
				<?php } ?>
      </tr>
 
 			</table>

        </td>
      </tr>
		<?php // endif;?>
    <?php } ?>
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
    try {
        $oValue = '';

        $arrSize = sizeof($auditObjectsArr2);
        // $arrSize = $arrSize - 1;

        for ($n = 0; $n < $arrSize; ++$n) {
            if ('' != $oValue) {
                break;
            }
            $strFieldsAndValuesOlder2 = explode('~~~', $auditObjectsArr2[$n][9]);
            $strFieldsOlder2 = explode('~!~', $strFieldsAndValuesOlder2[0]);
            $strValuesOlder2 = explode('~!~', $strFieldsAndValuesOlder2[1]);

            if ($item_ID > $auditObjectsArr2[$n][2]) {   // Check for ID to be older than current ID
            if ($itemTable == $auditObjectsArr2[$n][8]) {   // same tables
                for ($j = 0; $j < count($strFieldsOlder2); ++$j) {
                        if ($keyValue == $strFieldsOlder2[$j]) {
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

        throw new PropelException('Unable to perform get filed value.', $e);
    }
}
?>

