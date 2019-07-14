<h1><?php echo __('Audit Repository/Archival Institution') ?></h1>

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
		<?php $auditObjectsArr[] = array($item[0],$item[1],$item[2],$item[3],$item[4],$item[5],$item[6],$item[7],$item[8]); ?>
    <?php endforeach;  ?>

  	<?php foreach ($auditObjects as $item): ?>
       <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
    		<?php echo "<hr>" ?>
			<table border=1>
			<tr>
			<td colspan=3>Repository/Archival Institution Name
			</td>
			</tr>
			<tr>
				<td colspan=3>
					<b>
					<?php echo $item[1] ?>
					</b>
				</td>
			</tr>

			<?php if ($item[5] == "insert"): ?> 
				<?php $dAction = "Inserted into " ?> 
			<?php elseif ($item[5] == "update"): ?>
				<?php $dAction = "Updated " ?> 
			<?php elseif ($item[5] == "delete"): ?>
				<?php $dAction = "Deleted from" ?> 
			<?php else: ?>
				<?php $dAction = $item[5] ?> 
			<?php endif; ?>
 
			<?php if ($item[6] == "information_object"): ?>
				<?php $dTable = "'Archival Description Store'" ?> 
			<?php elseif ($item[6] == "repository"): ?>
				<?php $dTable = "Repository/Archival Institution" ?> 
			<?php elseif ($item[6] == "repository_i18n"): ?>
				<?php $dTable = "Repository/Archival Institution Extend" ?> 
			<?php elseif ($item[6] == "note"): ?>
				<?php $dTable = "Note" ?> 
			<?php elseif ($item[6] == "other_name"): ?>
				<?php $dTable = "Other Name" ?> 
			<?php elseif ($item[6] == "actor"): ?>
				<?php $dTable = "Actor" ?> 
			<?php elseif ($item[6] == "actor_i18n"): ?>
				<?php $dTable = "Actor extend" ?> 
			<?php else: ?>
				<?php $dTable = $item[5] ?> 
			<?php endif; ?>
 
 			<?php $user = ""; ?>
 			<?php $date = ""; ?>


			<?php $rOlder = doGetTableValue($auditObjectsArr, $item["ID"], $item[7]); ?>
			<?php $rOlderValues = explode("~!~",$rOlder); ?>
			<?php $dTableOlder = $rOlderValues[0] ?> 
			<?php $dActionOlder = $rOlderValues[1] ?> 
			<?php $user = $rOlderValues[2]; ?>
			<?php $date = $rOlderValues[3]; ?>
			
			<tr>
				<td><b>Field</b></td> <td><b>Old Value</b</td> <td><b>New Value</b</td> 
			</tr>
			<tr>
				<td>ID</td> <td><?php echo $item[0] ?></td> <td><?php echo $item[0] ?></td> 
			</tr>
			<tr>
				<td>User</td> <td><?php echo $user ?></td> <td><?php echo $item[8] ?></td> 
			</tr>
			<tr>
				<td>Date & Time</td> <td><?php echo $date ?></td> <td><?php echo $item[9] ?></td> 
			</tr>
			<tr>
				<td>Action</td> <td><?php echo $dActionOlder . $dTableOlder ?></td> <td><?php echo $dAction . $dTable ?></td> 
			</tr>
			
			<tr>
				<td colspan=3>
			<?php echo '<b>DB QUERY: </b>' . "<br>" ?>  
			</tr>
			<tr>
				<?php $strFieldsAndValues = explode("~~~",$item[7]) ?> 
				<?php $strFields = explode("~!~",$strFieldsAndValues[0]) ?> 
				<?php $strValues = explode("~!~",$strFieldsAndValues[1]) ?>
				<?php $arr_length = count($strFields); ?>
				<?php for($i=0;$i<$arr_length;$i++) { ?>
					<?php if ($strFields[$i] != "LFT" && $strFields[$i] != "RGT"): ?> 
						<?php $strValue = $strValues[$i] ?> 

						<?php if (trim($strFields[$i]) == "AUTHORIZED_FORM_OF_NAME"): ?>
					
							<?php $strOlder = doGetFieldValue("AUTHORIZED_FORM_OF_NAME",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "CORPORATE_BODY_IDENTIFIERS"): ?>
							<?php $strOlder = doGetFieldValue("CORPORATE_BODY_IDENTIFIERS",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Value</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "GEOCULTURAL_CONTEXT"): ?>
							<?php $strOlder = doGetFieldValue("GEOCULTURAL_CONTEXT",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Geocultural Context</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Geocultural Context</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "COLLECTING_POLICIES"): ?>
							<?php $strOlder = doGetFieldValue("COLLECTING_POLICIES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Collection Policies</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Collection Policies</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>


						<?php elseif (trim($strFields[$i]) == "BUILDINGS"): ?>
							<?php $strOlder = doGetFieldValue("BUILDINGS",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Buildings</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Buildings</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "HOLDINGS"): ?>
							<?php $strOlder = doGetFieldValue("HOLDINGS",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Holdings</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Holdings</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "OPENING_TIMES"): ?>
							<?php $strOlder = doGetFieldValue("OPENING_TIMES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Opening Times</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Opening Times</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "UPLOAD_LIMIT"): ?>
							<?php $strOlder = doGetFieldValue("UPLOAD_LIMIT",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Upload Limit</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Holdings</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DISABLED_ACCESS"): ?>
							<?php $strOlder = doGetFieldValue("DISABLED_ACCESS",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Disabled Access</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Disabled Access</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "RESEARCH_SERVICES"): ?>
							<?php $strOlder = doGetFieldValue("RESEARCH_SERVICES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Research Services</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Research Services</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "REPRODUCTION_SERVICES"): ?>
							<?php $strOlder = doGetFieldValue("REPRODUCTION_SERVICES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Reproduction Services</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Reproduction Services</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "PUBLIC_FACILITIES"): ?>
							<?php $strOlder = doGetFieldValue("PUBLIC_FACILITIES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Public Areas</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Public Areas</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESC_INSTITUTION_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("DESC_INSTITUTION_IDENTIFIER",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>	Institution Identifier</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>	Institution Identifier</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESC_RULES"): ?>
							<?php $strOlder = doGetFieldValue("DESC_RULES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Rules and/or Conventions Used</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Rules and/or Conventions Used</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESC_SOURCES"): ?>
							<?php $strOlder = doGetFieldValue("DESC_SOURCES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Sources</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Sources</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESC_REVISION_HISTORY"): ?>
							<?php $strOlder = doGetFieldValue("DESC_REVISION_HISTORY",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Dates of creation<br> revision and deletion</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Dates of creation<br> revision and deletion</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "HISTORY"): ?>
							<?php $strOlder = doGetFieldValue("HISTORY",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>History</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>History</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "MANDATES"): ?>
							<?php $strOlder = doGetFieldValue("MANDATES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Mandates/Sources of authority</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Mandates/Sources of authority</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "INTERNAL_STRUCTURES"): ?>
							<?php $strOlder = doGetFieldValue("INTERNAL_STRUCTURES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Administrative structure</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Administrative structure</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESC_STATUS_ID"): ?>
							<?php $strOlder = doGetFieldValue("DESC_STATUS_ID",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Record ID</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Record ID</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "OBJECT_ID"): ?>
							<?php $strOlder = doGetFieldValue("OBJECT_ID",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Object ID</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Object ID</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESC_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("DESC_IDENTIFIER",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Description Identifier</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Description Identifier</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESC_DETAIL_ID"): ?>
							<?php $strOlder = doGetFieldValue("DESC_DETAIL_ID",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Object ID</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Object ID</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "TYPE_ID" && $item[5] == "relation"): ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Physical Storage</i></td><td>" . QubitPhysicalObject::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitPhysicalObject::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Physical Storage</i></td><td>" . QubitPhysicalObject::getById($strOlder) . "</td><td>" . QubitPhysicalObject::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "TYPE_ID"): ?>
							<?php $strOlder = doGetFieldValue("TYPE_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Type ID</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Type ID</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "STATUS_ID"): ?>
							<?php $strOlder = doGetFieldValue("STATUS_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Status</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Status</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "REPOSITORY_ID"): ?>
							<?php $strOlder = doGetFieldValue("REPOSITORY_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Repository</i></td><td>". QubitRepository::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitRepository::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Repository</i></td><td>" . QubitRepository::getById($strOlder) . "</td><td>" . QubitRepository::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "RESTRICTION_CONDITION"): ?>
							<?php $strOlder = doGetFieldValue("RESTRICTION_CONDITION",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Restriction Condition</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Restriction Condition</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "REFUSAL_ID"): ?>
							<?php $strOlder = doGetFieldValue("REFUSAL_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Refusal</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Refusal</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "SENSITIVITY_ID"): ?>
							<?php $strOlder = doGetFieldValue("SENSITIVITY_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Sensitive</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Sensitive</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "PUBLISH_ID"): ?>
							<?php $strOlder = doGetFieldValue("PUBLISH_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Publish</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Publish</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "CLASSIFICATION_ID"): ?>
							<?php $strOlder = doGetFieldValue("CLASSIFICATION_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Classification</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Classification</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "RESTRICTION_ID"): ?>
							<?php $strOlder = doGetFieldValue("RESTRICTION_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Restriction</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Restriction</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("IDENTIFIER",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Identifier</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Identifier</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "FORMAT_ID"): ?>
							<?php $strOlder = doGetFieldValue("FORMAT_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Type and form of Archive</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Type and form of Archive</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "REGISTRY_ID"): ?>
							<?php $strOlder = doGetFieldValue("REGISTRY_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Registry</i></td><td>" . QubitRegistry::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitRegistry::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Registry</i></td><td>" . QubitRegistry::getById($strOlder) . "</td><td>" . QubitRegistry::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "SIZE_ID"): ?>

						<?php elseif (trim($strFields[$i]) == "TYP_ID"): ?>
							<?php $strOlder = doGetFieldValue("TYP_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Type</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Type</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "EQUIPMENT_ID"): ?>
							<?php $strOlder = doGetFieldValue("EQUIPMENT_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Equipment Available</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Equipment Available</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "DISPLAY_STANDARD_ID"): ?>
							<?php $strOlder = doGetFieldValue("DISPLAY_STANDARD_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Display Standard</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Display Standard</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "SOURCE_STANDARD"): ?>
							<?php $strOlder = doGetFieldValue("SOURCE_STANDARD",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Standard</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Standard</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "DESCRIPTION_DETAIL_ID"): ?>
							<?php $strOlder = doGetFieldValue("DESCRIPTION_DETAIL_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Description Detail</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Description Detail</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "DESCRIPTION_STATUS_ID"): ?>
							<?php $strOlder = doGetFieldValue("DESCRIPTION_STATUS_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Description Status</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Description Status</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "PARTNO"): ?>
							<?php $strOlder = doGetFieldValue("PARTNO",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Part Number</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Part Number</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "LEVEL_OF_DESCRIPTION_ID"): ?>
							<?php $strOlder = doGetFieldValue("LEVEL_OF_DESCRIPTION_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Level of Description</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Level of Description</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "EXTENT_AND_MEDIUM"): ?>
							<?php $strOlder = doGetFieldValue("EXTENT_AND_MEDIUM",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Extent and medium</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?>
							<?php else: ?>
								<?php echo "<td><i>Extent and medium</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?>
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "ARCHIVAL_HISTORY"): ?>
							<?php $strOlder = doGetFieldValue("ARCHIVAL_HISTORY",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php //echo "<td><i>Archival history</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Archival history</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Archival history</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "ACQUISITION"): ?>
							<?php $strOlder = doGetFieldValue("ACQUISITION",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Immediate source of acquisition or transfer</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Immediate source of acquisition or transfer</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "SCOPE_AND_CONTENT"): ?>
							<?php $strOlder = doGetFieldValue("SCOPE_AND_CONTENT",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Scope and content</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Scope and content</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "APPRAISAL"): ?>
							<?php $strOlder = doGetFieldValue("APPRAISAL",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php echo "<td><i>Appraisal, destruction and scheduling</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?>
						<?php elseif (trim($strFields[$i]) == "ACCRUALS"): ?>
							<?php $strOlder = doGetFieldValue("ACCRUALS",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Accruals</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Accruals</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "ARRANGEMENT"): ?>
							<?php $strOlder = doGetFieldValue("ARRANGEMENT",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>System of arrangement</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>System of arrangement</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "ACCESS_CONDITIONS"): ?>
							<?php $strOlder = doGetFieldValue("ACCESS_CONDITIONS",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Conditions governing access</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Conditions governing access</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "REPRODUCTION_CONDITIONS"): ?>
							<?php $strOlder = doGetFieldValue("REPRODUCTION_CONDITIONS",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Conditions governing reproduction</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Conditions governing reproduction</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "PHYSICAL_CHARACTERISTICS"): ?>
							<?php $strOlder = doGetFieldValue("PHYSICAL_CHARACTERISTICS",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Physical characteristics and technical requirements</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Physical characteristics and technical requirements</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "FINDING_AIDS"): ?>
							<?php $strOlder = doGetFieldValue("FINDING_AIDS",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Finding aids</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Finding aids</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "LOCATION_OF_ORIGINALS"): ?>
							<?php $strOlder = doGetFieldValue("LOCATION_OF_ORIGINALS",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Existence and location of originals</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Existence and location of originals</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "LOCATION_OF_COPIES"): ?>
							<?php $strOlder = doGetFieldValue("LOCATION_OF_COPIES",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Existence and location of copies</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Existence and location of copies</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "RELATED_UNITS_OF_DESCRIPTION"): ?>
							<?php $strOlder = doGetFieldValue("RELATED_UNITS_OF_DESCRIPTION",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Related units of description</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Related units of description</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "INSTITUTION_RESPONSIBLE_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("INSTITUTION_RESPONSIBLE_IDENTIFIER",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Institution identifier</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Institution identifier</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "RULES"): ?>
							<?php $strOlder = doGetFieldValue("RULES",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Rules or conventions</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Rules or conventions</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "SOURCES"): ?>
							<?php $strOlder = doGetFieldValue("SOURCES",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Sources</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Sources</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "REVISION_HISTORY"): ?>
							<?php $strOlder = doGetFieldValue("REVISION_HISTORY",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Dates of creation, revision and deletion</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Dates of creation, revision and deletion</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "VOLUME_NUMBER_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("VOLUME_NUMBER_IDENTIFIER",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Volume</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Volume</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "FILE_NUMBER_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("FILE_NUMBER_IDENTIFIER",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>File</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>File</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "PART_NUMBER_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("PART_NUMBER_IDENTIFIER",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Part</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Part</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "CULTURE"): ?>
							<?php $strOlder = doGetFieldValue("CULTURE",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Culture/Language</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Culture/Language</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "ITEM_NUMBER_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("ITEM_NUMBER_IDENTIFIER",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Item</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Item</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "TITLE"): ?>
							<?php $strOlder = doGetFieldValue("TITLE",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Title</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Title</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "NAME"): ?>
							<?php $strOlder = doGetFieldValue("NAME",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Name</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Name</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "DESCRIPTION_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("DESCRIPTION_IDENTIFIER",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Description Identifier</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Description Identifier</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "SOURCE_STANDARD"): ?>
							<?php $strOlder = doGetFieldValue("SOURCE_STANDARD",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Source Standard</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Source Standard</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "note"): ?>
							<?php $strOlder = doGetFieldValue("note",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Note</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Note</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "PARENT_ID"): ?>
							<?php $strOlder = doGetFieldValue("PARENT_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Parent ID</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Parent ID</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "SOURCE_CULTURE"): ?>
							<?php $strOlder = doGetFieldValue("SOURCE_CULTURE",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Source Culture</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Source Culture</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "USABILITY_ID"): ?>
							<?php $strOlder = doGetFieldValue("USABILITY_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Usibility</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Usibility</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "MEASURE_ID"): ?>
							<?php $strOlder = doGetFieldValue("MEASURE_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Measure</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Measure</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "MEDIUM_ID"): ?>
							<?php $strOlder = doGetFieldValue("MEDIUM_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Medium</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Medium</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "AVAILABILITY_ID"): ?>
							<?php $strOlder = doGetFieldValue("AVAILABILITY_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Available</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Available</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "RESTORATION_ID"): ?>
							<?php $strOlder = doGetFieldValue("RESTORATION_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Restoration</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Restoration</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "CONSERVATION_ID"): ?>
							<?php $strOlder = doGetFieldValue("CONSERVATION_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Conservation</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Conservation</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "Type_ID"): ?>
							<?php $strOlder = doGetFieldValue("Type_ID",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Type</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Type</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "RECORD_CONDITION"): ?>
							<?php $strOlder = doGetFieldValue("RECORD_CONDITION",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Condition</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Condition</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "AVAILABILITY"): ?>
							<?php $strOlder = doGetFieldValue("AVAILABILITY",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Available</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Available</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "LOCATION"): ?>
							<?php $strOlder = doGetFieldValue("LOCATION",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Location</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Location</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "SHELF"): ?>
							<?php $strOlder = doGetFieldValue("SHELF",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Shelf</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Shelf</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "ROW"): ?>
							<?php $strOlder = doGetFieldValue("ROW",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Row</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Row</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "STRONG_ROOM"): ?>
							<?php $strOlder = doGetFieldValue("STRONG_ROOM",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Strong room</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Strong room</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "REMARKS"): ?>
							<?php $strOlder = doGetFieldValue("REMARKS",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Remarks</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Remarks</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "UNIQUE_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("UNIQUE_IDENTIFIER",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Unique identifier</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Unique identifier</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php elseif (trim($strFields[$i]) == "TIME_PERIOD"): ?>
							<?php $strOlder = doGetFieldValue("TIME_PERIOD",$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Date/Time</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Date/Time</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>
						<?php else: ?>
								<?php if ($strFields[$i] != 'ID'): ?>
									<?php $strOlder = doGetFieldValue($strFields[$i],$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
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
function doGetFieldValue($keyValue, $auditObjectsArr2, $item_ID, $item, $item6)
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
			$strFieldsAndValuesOlder2 = explode("~~~",$auditObjectsArr2[$n][7]);
			$strFieldsOlder2 = explode("~!~",$strFieldsAndValuesOlder2[0]); 
			$strValuesOlder2 = explode("~!~",$strFieldsAndValuesOlder2[1]); 

			if ($item_ID > $auditObjectsArr2[$n][2] )   //Check for ID to be older than current ID
			{
 				if ($item6 == $auditObjectsArr2[$n][5] )   //same tables
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
	//echo "oValue=".$oValue."<br>";
		return $oValue;
	} catch (Exception $e) {
		Propel::log($e->getMessage(), Propel::LOG_ERR);
		throw new PropelException("Unable to perform get filed value.", $e);
	}
}

function doGetTableValue($auditObjectsArr2, $item_ID,  $item6)
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
			$strFieldsAndValuesOlder2 = explode("~~~",$auditObjectsArr2[$n][7]);
			$strFieldsOlder2 = explode("~!~",$strFieldsAndValuesOlder2[0]); 
			$strValuesOlder2 = explode("~!~",$strFieldsAndValuesOlder2[1]); 

			if ($item_ID > $auditObjectsArr2[$n][2] )   //Check for ID to be older than current ID
			{
 				if ($item6 == $auditObjectsArr2[$n][5] )   //same tables
 				{
 					$oAction = $auditObjectsArr2[$n][4]; 
 					$oTable = $auditObjectsArr2[$n][5]; 
 					$oUser = $auditObjectsArr2[$n][7]; 
 					$oDdate = $auditObjectsArr2[$n][8]; 

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
			 
		if ($oTable == "repository")
		{
			$dTableOlder = "Repository";
		}
		elseif ($oTable == "repository_i18n")
		{
			$dTableOlder = "Repository Extend";
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

