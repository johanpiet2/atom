<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
	<h1 class="multiline">
		<?php echo render_title($resource) ?>
		<span class="sub"><?php echo __('Edit Book Items %1%', array('%1%' => sfConfig::get('app_ui_label_bookinobject'))) ?></span>
	</h1>
<?php end_slot() ?>

<h1 class="label"><?php echo render_title($resource) ?>

<?php echo $form->renderGlobalErrors() ?>

<?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
	<?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'bookinobject', 'action' => 'editBookin'))) ?>
<?php else: ?>
	<?php echo $form->renderFormTag(url_for(array('module' => 'bookinobject', 'action' => 'add'))) ?>
<?php endif; ?>

<?php echo $form->renderHiddenFields() ?>

<div id="content">
	<fieldset class="collapsible">

	<legend><?php echo __('Edit Bookin %1%', array('%1%' => sfConfig::get('app_ui_label_bookinobject'))) ?></legend>

	<?php echo render_field($form->name, $resource) ?>
	<?php echo render_field($form->time_period, $resource) ?>
	<?php echo render_field($form->remarks, $resource) ?>
	<?php echo render_field($form->location, $resource) ?>
	<?php echo $form->requestor->renderRow() ?>
	<?php echo $form->dispatcher->renderRow() ?>
	</fieldset>
</div>

<section class="actions">
	<ul>

	<li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'informationobject', 'action' => 'browse'), array('class' => 'c-btn')) ?></li>

	<li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
	</ul>
</section>

<?php end_slot() ?>
