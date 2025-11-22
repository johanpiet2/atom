<h1><?php echo __('Audit Registry'); ?></h1>

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
  	<?php foreach ($auditObjectsOlder as $item) {  ?>
		<?php $auditObjectsArr[] = [$item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], $item[8], $item[9]]; ?>
    <?php }  ?>

  	<?php foreach ($pager->getResults() as $item) { ?>
  	
       <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd'; ?>">
        <td>
    		<?php echo '<hr>'; ?>
			<table border=1>
			<tr>
				<td colspan=3>
					<b>Record ID: 
					<?php echo $item[1]; ?>
					</b>
				</td>
			</tr>
			<tr>
				<td>Name</td><td colspan=2><?php echo $item[8].' - '.$item[9]; ?></td> 
			</tr>
			
			<?php if ('insert' == $item['ACTION']) { ?> 
				<?php $dAction = 'Inserted into '; ?> 
			<?php } elseif ('update' == $item['ACTION']) { ?>
				<?php $dAction = 'Updated '; ?> 
			<?php } elseif ('delete' == $item['ACTION']) { ?>
				<?php $dAction = 'Deleted from'; ?> 
			<?php } else { ?>
				<?php $dAction = $item['ACTION']; ?> 
			<?php } ?>
 
			<?php if ('actor' == $item['DB_TABLE']) { ?> 
				<?php $dTable = 'Registry (Actor)'; ?> 
			<?php } elseif ('actor_i18n' == $item['DB_TABLE']) { ?> 
				<?php $dTable = 'Registry (Actor) Extend'; ?> 
			<?php } elseif ('registry' == $item['DB_TABLE']) { ?> 
				<?php $dTable = 'Registry'; ?> 
			<?php } elseif ('contact_information_i18n' == $item['DB_TABLE']) { ?> 
				<?php $dTable = 'Contact Information'; ?> 
			<?php } else { ?>
				<?php $dTable = $item['DB_TABLE']; ?> 
			<?php } ?>
 
 			<?php $user = ''; ?>
 			<?php $date = ''; ?>


			<?php $rOlder = doGetTableValue($auditObjectsArr, $item['ID'], $item['DB_TABLE']); ?>
			<?php $rOlderValues = explode('~!~', $rOlder); ?>
			<?php $dTableOlder = $rOlderValues[0]; ?> 
			<?php $dActionOlder = $rOlderValues[1]; ?> 
			<?php $user = $rOlderValues[2]; ?>
			<?php $date = $rOlderValues[3]; ?>
			
			<tr>
				<td><b>Field</b></td> <td><b>Old Value</b</td> <td><b>New Value</b</td> 
			</tr>
			<tr>
				<td>ID</td> <td><?php // echo $item[0]?></td> <td><?php echo $item[0]; ?></td> 
			</tr>
			<tr>
				<td>User</td> <td><?php echo $user; ?></td> <td><?php echo $item[6]; ?></td> 
			</tr>
			<tr>
				<td>Date & Time</td> <td><?php echo $date; ?></td> <td><?php echo $item['ACTION_DATE_TIME']; ?></td> 
			</tr>
			<tr>
				<td>Action</td> <td><?php echo $dActionOlder.$dTableOlder; ?></td> <td><?php echo $dAction.$dTable; ?></td> 
			</tr>
			
			<tr>
				<td colspan=3>
			<?php echo '<b>DB QUERY: </b><br>'; ?>  
			</tr>
			<tr>
				<?php $strFieldsAndValues = explode('~~~', $item['DB_QUERY']); ?> 
				<?php $strFields = explode('~!~', $strFieldsAndValues[0]); ?> 
				<?php $strValues = explode('~!~', $strFieldsAndValues[1]); ?>
				<?php $arr_length = count($strFields); ?>
				<?php for ($i = 0; $i < $arr_length; ++$i) { ?>
					<?php $strValue = $strValues[$i]; ?> 
					<?php if ('AUTHORIZED_FORM_OF_NAME' == trim($strFields[$i])) { ?>
						<?php echo '<td><i>Field</i></td><td colspan=2>Authorised form of name</td><tr>'; ?> 
						<?php $strOlder = doGetFieldValue('AUTHORIZED_FORM_OF_NAME', $auditObjectsArr, $item['ID'], $item['ACTION_DATE_TIME'], $item['DB_TABLE']); ?>
						<?php if ($strOlder != $strValues[$i]) { ?>
							<?php echo '<td><i>Value</i></td><td>'.$strOlder."</td><td bgcolor='#CCFF66'>".$strValues[$i].'</td><tr>'; ?> 
						<?php } else { ?>
							<?php echo '<td><i>Value</i></td><td>'.$strOlder.'</td><td>'.$strValues[$i].'</td><tr>'; ?> 
						<?php } ?>

					<?php } elseif ('CORPORATE_BODY_IDENTIFIERS' == trim($strFields[$i])) { ?>
						<?php echo '<td><i>Field</i></td><td colspan=2>Identifier</td><tr>'; ?> 
						<?php $strOlder = doGetFieldValue('CORPORATE_BODY_IDENTIFIERS', $auditObjectsArr, $item['ID'], $item['ACTION_DATE_TIME'], $item['DB_TABLE']); ?>
						<?php if ($strOlder != $strValues[$i]) { ?>
							<?php echo '<td><i>Value</i></td><td>'.$strOlder."</td><td bgcolor='#CCFF66'>".$strValues[$i].'</td><tr>'; ?> 
						<?php } else { ?>
							<?php echo '<td><i>Value</i></td><td>'.$strOlder.'</td><td>'.$strValues[$i].'</td><tr>'; ?> 
						<?php } ?>

					<?php } elseif ('REPOSITORY_ID' == trim($strFields[$i])) { ?>
						<?php echo '<td><i>Field</i></td><td colspan=2>Repository</td><tr>'; ?> 
						<?php $strOlder = doGetFieldValue('REPOSITORY_ID', $auditObjectsArr, $item['ID'], $item['ACTION_DATE_TIME'], $item['DB_TABLE']); ?>
						<?php if ($strOlder != $strValues[$i]) { ?>
							<?php echo '<td><i>Value</i></td><td>'.QubitRepository::getById($strOlder)."</td><td bgcolor='#CCFF66'>".QubitRepository::getById($strValues[$i]).'</td><tr>'; ?> 
						<?php } else { ?>
							<?php echo '<td><i>Value</i></td><td>'.QubitRepository::getById($strOlder).'</td><td>'.QubitRepository::getById($strValues[$i]).'</td><tr>'; ?> 
						<?php } ?>

					<?php } else { ?>
							<?php if ('ID' != $strFields[$i]) { ?>
								<?php $strOlder = doGetFieldValue($strFields[$i], $auditObjectsArr, $item['ID'], $item['ACTION_DATE_TIME'], $item['DB_TABLE']); ?>
								<?php echo '<td><i>'.$strFields[$i].'</i></td><td>'.$strOlder.'</td><td>'.$strValues[$i].'</td><tr>'; ?>
							<?php } ?>

					<?php } ?>
				<?php } ?>
      </tr>
 
 			</table>

        </td>
      </tr>
		<?php // endif;?>
    <?php } ?>
  </tbody>
</table>

<div id="result-count">
  <?php echo __('Showing %1% results', ['%1%' => $foundcount]); ?>
</div>

    <section class="actions">
      <ul>
		<li><input class="c-btn c-btn-submit" type="button" onclick="history.back();" value="Back"></li>
      </ul>
    </section>

<?php
function doGetFieldValue($keyValue, $auditObjectsArr2, $item_ID, $item, $item4)
{
    try {
        $oValue = '';

        $arrSize = sizeof($auditObjectsArr2);
        // $arrSize = $arrSize - 1;

        for ($n = 0; $n < $arrSize; ++$n) {
            if ('' != $oValue) {
                break;
            }

            $strFieldsAndValuesOlder2 = explode('~~~', $auditObjectsArr2[$n][7]);
            $strFieldsOlder2 = explode('~!~', $strFieldsAndValuesOlder2[0]);
            $strValuesOlder2 = explode('~!~', $strFieldsAndValuesOlder2[1]);

            if ($item_ID > $auditObjectsArr2[$n][0]) {   // Check for ID to be older than current ID
            if ('AUTHORIZED_FORM_OF_NAME' == $keyValue) {
    }
                if ($item4 == $auditObjectsArr2[$n][6]) {   // same tables
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

function doGetTableValue($auditObjectsArr2, $item_ID, $item4)
{
    try {
        $oValue = '';
        $oAction = '';
        $oTable = '';
        $oUser = '';
        $oDdate = '';

        $arrSize = sizeof($auditObjectsArr2);
        $arrSize = $arrSize - 1;
        for ($n = 0; $n < $arrSize; ++$n) {
            $strFieldsAndValuesOlder2 = explode('~~~', $auditObjectsArr2[$n][7]);
            $strFieldsOlder2 = explode('~!~', $strFieldsAndValuesOlder2[0]);
            $strValuesOlder2 = explode('~!~', $strFieldsAndValuesOlder2[1]);

            if ($item_ID > $auditObjectsArr2[$n][0]) {   // Check for ID to be older than current ID
            if ($item4 == $auditObjectsArr2[$n][6]) {   // same tables
                $oAction = $auditObjectsArr2[$n][5];
                    $oTable = $auditObjectsArr2[$n][6];
                    $oUser = $auditObjectsArr2[$n][8];
                    $oDdate = $auditObjectsArr2[$n][9];

                    break;
                }
            }
        }

        if ('insert' == $oAction) {
            $dActionOlder = 'Inserted into ';
        } elseif ('update' == $oAction) {
            $dActionOlder = 'Updated ';
        } elseif ('delete' == $oAction) {
            $dActionOlder = 'Deleted from';
        } else {
            $dActionOlder = $oAction;
        }

        if ('actor' == $oTable) {
            $dTableOlder = 'Registry (Actor)';
        } elseif ('actor_i18n' == $oTable) {
            $dTableOlder = 'Registry (Actor) Extend';
        } elseif ('registry' == $oTable) {
            $dTableOlder = 'Registry';
        } elseif ('contact_information_i18n' == $oTable) {
            $dTableOlder = 'Contact Information';
        } else {
            $dTableOlder = $oTable;
        }

        return $dTableOlder.'~!~'.$dActionOlder.'~!~'.$oUser.'~!~'.$oDdate;
    } catch (Exception $e) {
        Propel::log($e->getMessage(), Propel::LOG_ERR);

        throw new PropelException('Unable to perform get filed value.', $e);
    }
}
?>

