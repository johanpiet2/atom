<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
	<h1 class="multiline"> <!--changed from 'label'-->
		<?php echo render_title('Strong Room Book Out') ?>
		<br>
		<br>
		<?php echo render_title($resource) ?>
	</h1>
<?php end_slot() ?>

<?php slot('content') ?>
	<body onload="javascript:NewCal('time_period','ddmmyyyy',true,false,24,true);renderCalendar('time_period','div0');show24();toggleOff();">
	<?php echo $form->renderGlobalErrors() ?>
	<?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'informationobject', 'action' => 'editBookoutObjects'))) ?>
	<?php echo $form->renderHiddenFields() ?>
    <section id="content">
		<fieldset class="collapsible">
		<legend><?php echo __('Location area') ?></legend>
			<table width="100%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
				<div class="content">
					<tr>
						<td colspan=1>
							<?php echo $form->name->renderRow(array('size' => 50, 'readonly'=>'true'), 'Name of Collection/Item')  ?>
						</td>
						<td colspan=2>
							<?php echo $form->identifier->renderRow(array('size' => 50, 'readonly'=>'true'), 'Identifier')  ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $form->availability->renderRow(array('readonly'=>'true'), 'Equipment Availability'); ?>
						</td>
						<td>
					 		<?php echo $form->location->renderRow(array('readonly'=>'true'), 'Physical Location')?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $form->unique_identifier->renderRow(array('readonly'=>'true'), 'Unique Identifier') ?>
						</td>
						<td>
								<?php echo $form->strong_room->renderRow(array('readonly'=>'true'), 'Strongroom Name') ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $form->shelf->renderRow(array('readonly'=>'true'), 'Shelf')?>
						</td>
						<td>
							<?php echo $form->row->renderRow(array('readonly'=>'true'), 'Row') ?> 
						</td>
						<td>
							<?php echo $form->bin->renderRow(array('readonly'=>'true'), 'Bin/Box') ?> 
						</td>
					</tr>
				</div>
			</table>
		</fieldset>
		<fieldset class="collapsible">
		<legend><?php echo __('Duration area') ?></legend>
			<table width="100%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
				<div class="content">
					<tr>
						<td>
							<?php $currentDate = date('d/m/Y H:i:s');
							echo $form->time_period->renderRow(array('value' => $currentDate,'readonly'=>'true','onchange' => 'show24();'),'Time Period') ?>
							<div id="div0">Auto fill datepicker - Time Period - This will be deleted automatically</div>
						</td>
						<td>
						</td>
						<td>Return date/Time<h1 style="background-color:red"><b>
							<input type="hidden" name="dateDiff" id="dateDiff" value="" onchange="show24();"/>
							<p label id="lblAdd24Hrs" style="font-family:verdana">Return Date/Time</p>

						</td>
					</tr>
				</div>
			</table>
		</fieldset>
		<fieldset class="collapsible">
		<legend><?php echo __('Booking area') ?></legend>
			<tr>
				<td colspan=3>
					<?php echo $form->remarks->renderRow() ?> 
				</td>
			</tr>
			<tr>
				<td colspan=3>
					<?php echo $form->record_condition->renderRow(array('value' => ' '), 'Record Condition') ?> 
				</td>
			</tr>
			<tr>
				<td colspan=3>
					Requested By
					<tr>
						<td>
							<p style="color:#424242"><input type="radio" id="requestedObjectResearcher"  name="requestedObject" value = "researcherObject" onclick="toggleOff();">Researcher</p> 
						</td>
					</tr>
					<tr>
						<td>
							<p style="color:#424242"><input type="radio" id="requestedObjectAuthority" name="requestedObject" value = "authorityRecordObject" onclick="toggleOff();">Client Office</p> 
						</td>
					</tr>
					<tr>
						<td>
							<p style="color:#424242"><input type="radio" id="requestedObjectServiceProvider"  name="requestedObject" value = "serviceProviderObject" onclick="toggleOff();">Service Provider</p> 
						</td>
					</tr>
				</td>
				<td width="73%">
			</tr>
			
			<tr>
				<td colspan=3>
					<div id="div4">
						<?php echo $form->researcher->label(__('Researcher'))->renderRow(array('onchange'=>'toggleOff();')) ?>
					</div>
					<div id="div5">
						<?php echo $form->service_provider->label(__('Service Provider'))->renderRow(array('onchange'=>'toggleOff();')) ?>
					</div>
					<div id="div6">
						<?php echo $form->authority_record->label(__('Authority Record'))->renderRow(array('onchange'=>'toggleOff();')) ?>
					</div>
					<input type="hidden" name="researcherFlag" id="researcherFlag" value=""/>
					<input type="hidden" name="authorityRecordFlag" id="authorityRecordFlag" value=""/>
					<input type="hidden" name="serviceProviderFlag" id="serviceProviderFlag" value=""/>
				</td>
			</tr>
			<tr>
				<td colspan=3><div id="div3">
					<?php echo $form->requestor->renderRow(array('readonly'=>'true')) ?></div>
				</td>
			</tr>
			<tr>
				<td colspan=3>
					<?php echo $form->dispatcher->renderRow() ?>
				</td>
			</tr>
			<div id='divReceipt'> 
				<tr>
		            <td><p style="color:#424242"><input type="checkbox" name="cbReceipt" value="1" checked="checked" />Print removal permit</td>
				</tr>
			</div>
		</fieldset>
    </section>
	<section class="actions">
		<table width="100%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
		  <ul class="clearfix links">
			<li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
			<li><input class="c-btn c-btn-submit" type="submit" id="bookout"  value="<?php echo __('Book Out') ?>"/></li>
		  </ul>
		</table>
	</section>
</body>

<script type="text/javascript">

function show24()
{
	var vDay = document.getElementById("time_period").value.substr(0,document.getElementById("time_period").value.indexOf("/"));
	var vRes = document.getElementById("time_period").value.substr(document.getElementById("time_period").value.indexOf("/")+1);
	vMonth = vRes.substr(0,vRes.indexOf("/"));
	vRes = vRes.substr(vRes.indexOf("/")+1);
	vYear = vRes.substr(0,vRes.indexOf(" "));
	vRes = vRes.substr(vRes.indexOf(" ")+1);
	vMonth = vGetMonth(parseInt(vMonth)-1);
	var startDate = new Date(vDay+" "+vMonth+" "+vYear+" "+vRes);
	var date = new Date(startDate);
	date.setDate(date.getDate() + 1); // plus 24 hours/1 day
	document.getElementById('lblAdd24Hrs').innerHTML = date;
}

function toggleOff()
{
	if (document.getElementById("requestedObjectServiceProvider").checked)
	{
		var serviceProviderDiv = document.getElementById('div5');
		serviceProviderDiv.setAttribute('class', 'visible');
		var researcherDiv = document.getElementById('div4');
		researcherDiv.setAttribute('class', 'hidden');
		var authorityDiv = document.getElementById('div6');
		authorityDiv.setAttribute('class', 'hidden');
    	document.getElementById("bookout").disabled = false;
	    document.getElementById("serviceProviderFlag").value = "true";
	    document.getElementById("researcherFlag").value = "false";
	    document.getElementById("authorityRecordFlag").value = "false";
    }
	else if (document.getElementById("requestedObjectResearcher").checked)
	{
		var serviceProviderDiv = document.getElementById('div5');
		serviceProviderDiv.setAttribute('class', 'hidden');
		var researcherDiv = document.getElementById('div4');
		researcherDiv.setAttribute('class', 'visible');
		var authorityDiv = document.getElementById('div6');
		authorityDiv.setAttribute('class', 'hidden');
    	document.getElementById("bookout").disabled = false;
	    document.getElementById("serviceProviderFlag").value = "false";
	    document.getElementById("researcherFlag").value = "true";
	    document.getElementById("authorityRecordFlag").value = "false";
	}
	else if (document.getElementById("requestedObjectAuthority").checked)
	{
		var serviceProviderDiv = document.getElementById('div5');
		serviceProviderDiv.setAttribute('class', 'hidden');
		var researcherDiv = document.getElementById('div4');
		researcherDiv.setAttribute('class', 'hidden');
		var authorityDiv = document.getElementById('div6');
		authorityDiv.setAttribute('class', 'visible');
    	document.getElementById("bookout").disabled = false;
	    document.getElementById("serviceProviderFlag").value = "false";
	    document.getElementById("researcherFlag").value = "false";
	    document.getElementById("authorityRecordFlag").value = "true";
	}
	else
	{
		var serviceProviderDiv = document.getElementById('div5');
		serviceProviderDiv.setAttribute('class', 'hidden');
		var researcherDiv = document.getElementById('div4');
		researcherDiv.setAttribute('class', 'hidden');
		var authorityDiv = document.getElementById('div6');
		authorityDiv.setAttribute('class', 'hidden');
    	document.getElementById("bookout").disabled = true;
	    document.getElementById("serviceProviderFlag").value = "false";
	    document.getElementById("researcherFlag").value = "false";
	    document.getElementById("authorityRecordFlag").value = "false";
	} 
}

</script>
<?php end_slot() ?>
