<?php //decorate_with('layout_1col.php') ?>
<h1><?php echo __('Edit Preservation%1%', array('%1%' => sfConfig::get('app_ui_label_presevationobject'))) ?></h1>
<h1 class="multiline"><?php echo render_title($resource) ?></h1>
<?php echo $form->renderGlobalErrors() ?>

	<?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
	  <?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'presevationobject', 'action' => 'editPreservation'))) ?>
	<?php else: ?>
	  <?php echo $form->renderFormTag(url_for(array('module' => 'presevationobject', 'action' => 'add'))) ?>
	<?php endif; ?>

  <?php echo $form->renderHiddenFields() ?>
<table width="80%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
  <fieldset class="collapsible">
	  <tr>
		<td width="572" colspan="3"><?php echo $form->name->label(__('Name'))->renderRow(array('readonly'=>'true')) ?></td>
	  </tr>
	  <tr>
		<td><?php echo  $form->availability->label(__('Available'))->renderRow() ?></td>
		<td>&nbsp;</td>
		<td><?php echo $form->restoration->label(__('Restoration'))->renderRow() ?></td>
	  </tr>
	  <tr>
		<td><?php echo $form->conservation->label(__('Conservation'))->renderRow(array('size' => 0)) ?></td>
		<td>&nbsp;</td>
		<td><?php echo $form->type->label(__('Method of Storage'))->renderRow() ?></td>
	  </tr>
	  <tr>
		<td><?php echo $form->measure->label(__('Measure'))->renderRow() ?></td>
		<td>&nbsp;</td>
		<td><?php echo $form->usability->label(__('Usability'))->renderRow() ?></td>
	  </tr>
	  <tr>
		<td><?php echo $form->condition->label(__('Condition'))->renderRow() ?></td>
		<td>&nbsp;</td>
		<td></td>
	  </tr>
	  
  </fieldset>
   <tr>
   		<td colspan=3>
			<div class="actions section">

			<h2 class="element-invisible"><?php echo __('Actions') ?></h2>

			<section class="actions">
			  <ul>
				<li><?php echo link_to(__('Cancel'), array($informationObj, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
        		<li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
			  </ul>
			</section>
		</td>
	</tr>
 </div>
</table>
</body>
