<h1><?php echo __('Audit Registry') ?></h1>

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
 
			<?php if ($item["DB_TABLE"] == "actor"): ?> 
				<?php $dTable = "Actor" ?> 
			<?php elseif ($item["DB_TABLE"] == "actor_i18n"): ?> 
				<?php $dTable = "Actor Extend" ?> 
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



						<?php elseif (trim($strFields[$i]) == "DATES_OF_EXISTENCE"): ?>
							<?php $strOlder = doGetFieldValue("DATES_OF_EXISTENCE",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Dates of Existence</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Dates of Existence</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "PLACES"): ?>
							<?php $strOlder = doGetFieldValue("PLACES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Places</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Places</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "LEGAL_STATUS"): ?>
							<?php $strOlder = doGetFieldValue("LEGAL_STATUS",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Legal Status</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Legal Status</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "FUNCTIONS"): ?>
							<?php $strOlder = doGetFieldValue("FUNCTIONS",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Functions</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Functions</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "GENERAL_CONTEXT"): ?>
							<?php $strOlder = doGetFieldValue("GENERAL_CONTEXT",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>General Context</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>General Context</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "INSTITUTION_RESPONSIBLE_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("INSTITUTION_RESPONSIBLE_IDENTIFIER",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Institution Identifier</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Institution Identifier</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "RULES"): ?>
							<?php $strOlder = doGetFieldValue("RULES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Rules and/or Conventions Used</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Rules and/or Conventions Used</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "SOURCES"): ?>
							<?php $strOlder = doGetFieldValue("SOURCES",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Sources</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Sources</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "REVISION_HISTORY"): ?>
							<?php $strOlder = doGetFieldValue("REVISION_HISTORY",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Rivision History</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Rivision History</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "CULTURE"): ?>
							<?php $strOlder = doGetFieldValue("CULTURE",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Culture/Language</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Culture/Language</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "ENTITY_TYPE_ID"): ?>
							<?php $strOlder = doGetFieldValue("ENTITY_TYPE_ID",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Entity Type</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Entity Type</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESCRIPTION_STATUS_ID"): ?>
							<?php $strOlder = doGetFieldValue("DESCRIPTION_STATUS_ID",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Status</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Status</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESCRIPTION_DETAIL_ID"): ?>
							<?php $strOlder = doGetFieldValue("DESCRIPTION_DETAIL_ID",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Detail</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Detail</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "DESCRIPTION_IDENTIFIER"): ?>
							<?php $strOlder = doGetFieldValue("DESCRIPTION_IDENTIFIER",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Authority Record Identifier</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Authority Record Identifier</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "TYPE_ID"): ?>
							<?php $strOlder = doGetFieldValue("TYPE_ID",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Other Name</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td bgcolor='#CCFF66'>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Other Name</i></td><td>" . QubitTerm::getById($strOlder) . "</td><td>" . QubitTerm::getById($strValues[$i]) . "</td><tr>" ?> 
							<?php endif; ?>

						<?php elseif (trim($strFields[$i]) == "SOURCE_CULTURE"): ?>
							<?php $strOlder = doGetFieldValue("SOURCE_CULTURE",$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>Source Culture</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php echo "<td><i>Source Culture</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?> 
							<?php endif; ?>






						<?php else: ?>
							<?php $strOlder = doGetFieldValue($strFields[$i],$auditObjectsArr, $item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
							<?php if ($strOlder != $strValues[$i]): ?>
								<?php echo "<td><i>" . $strFields[$i] . "</i></td><td>" . $strOlder . "</td><td bgcolor='#CCFF66'>" . $strValues[$i] . "</td><tr>" ?> 
							<?php else: ?>
								<?php $strOlder = doGetFieldValue($strFields[$i],$auditObjectsArr,$item["ID"], $item["ACTION_DATE_TIME"], $item["DB_TABLE"]); ?>
								<?php echo "<td><i>". $strFields[$i] . "</i></td><td>" . $strOlder . "</td><td>" . $strValues[$i] . "</td><tr>" ?>
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
			if ($keyValue == 'AUTHORIZED_FORM_OF_NAME') {
	}
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
			 
		if ($oTable == "actor") 
		{
			$dTableOlder = "Actor";
		}
		elseif ($oTable == "actor_i18n")
		{
			$dTableOlder = "Actor Extend"; 
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

