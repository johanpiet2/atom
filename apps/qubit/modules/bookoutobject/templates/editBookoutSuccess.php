<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
	<h1 class="multiline"> 
		<?php echo render_title('Strong Room Re-Book Out') ?>
		<br>
		<br>
		<?php echo render_title($resource) ?>
	</h1>
<?php end_slot() ?>

<?php slot('content') ?>
	<body onload="javascript:NewCal('time_period','ddmmyyyy',true,false,24,true);renderCalendar('time_period','div0');">

		<?php echo $form->renderGlobalErrors() ?>
		<?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
			<?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'bookoutobject', 'action' => 'editBookout'))) ?>
		<?php else: ?>
			<?php echo $form->renderFormTag(url_for(array('module' => 'bookoutobject', 'action' => 'add'))) ?>
		<?php endif; ?>
		<?php echo $form->renderHiddenFields() ?>

		<section id="content">
			<fieldset class="collapsible">
				<legend><?php echo __('Location area') ?></legend>
				<table width="100%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
					<tr>
						<td colspan=3>
							<?php echo $form->name->renderRow(array('size' => 50, 'readonly'=>'true'), 'Name of Collection/Item')  ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $form->unique_identifier->renderRow(array('readonly'=>'true'), 'Unique Identifier ') ?>
						</td>
						<td>
							<?php echo $form->availability->renderRow(array('readonly'=>'true'), 'Equipment Availability'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $form->location->renderRow(array('readonly'=>'true'), 'Physical Location')?>
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
				</table>
			</fieldset>
			<fieldset class="collapsible">
				<legend><?php echo __('Date area') ?></legend>
				<table width="100%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
					<tr>
						<td>
							<table>
								<td>
									<?php $currentDate = date('d/m/Y H:i:s');
									echo $form->time_period->renderRow(array('value' => $currentDate,'readonly'=>'true','onchange' => ''),'Time Period') ?>
									<div id="div0">Auto fill datepicker - Time Period - This will be deleted automatically</div>
								</td>
							</table>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="collapsible">
				<legend><?php echo __('Book In area') ?></legend>
				<table width="100%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
					<tr>
						<td>
							<?php echo $form->remarks->renderRow() ?> 
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $form->dispatcher->renderRow(array('readonly'=>'true')) ?>
						</td>
					</tr>
				</table>
				<div id='divReceipt'> 
					<tr>
				        <td><p style="color:#424242"><input type="checkbox" name="cbReceipt" value="1" checked="checked" />Print passout</td>
					</tr>
				</div>
			</fieldset>
		</section>
		<section class="actions">
			<table width="100%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
			  <ul class="clearfix links">
				<li><?php echo link_to(__('Cancel'), array($informationObj, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
				<li><input class="c-btn c-btn-submit" type="submit" id="bookin"  value="<?php echo __('Re-Book Out') ?>"/></li>
			  </ul>
			</table>
		</section>
	</body>
<?php end_slot() ?>
