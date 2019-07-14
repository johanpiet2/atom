<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo render_title($resource) ?>
    <span class="sub"><?php echo __('Edit %1%', array('%1%' => sfConfig::get('app_ui_label_physicalobject'))) ?></span>
  </h1>
<?php end_slot() ?>

<?php slot('content') ?>

  <?php echo $form->renderGlobalErrors() ?>

  <?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
    <?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'physicalobject', 'action' => 'edit'))) ?>
  <?php else: ?>
    <?php echo $form->renderFormTag(url_for(array('module' => 'physicalobject', 'action' => 'add'))) ?>
  <?php endif; ?>

    <?php echo $form->renderHiddenFields() ?>

    <div id="content">

      <fieldset class="collapsible">

        <legend><?php echo __('Edit %1%', array('%1%' => sfConfig::get('app_ui_label_physicalobject'))) ?></legend>

		<?php echo $form->repositoryId->renderRow() ?>
		<?php echo render_field($form->name, $resource) ?>
		<?php echo render_field($form->location, $resource) ?>
		<?php echo render_field($form->uniqueIdentifier, $resource) ?>
		<?php echo render_field($form->descriptionTitle, $resource) ?>
		<?php echo render_field($form->periodCovered, $resource) ?>
		<?php echo render_field($form->extent, $resource) ?>
		<?php echo render_field($form->accrualSpace, $resource) ?>
		<?php echo render_field($form->forms, $resource) ?>
		<?php echo $form->type->renderRow() ?>

      </fieldset>

    </div>

	<section class="actions">
	  <ul>
		<?php if (isset($resource->id)): ?>
		  <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'physicalobject'), array('class' => 'c-btn')) ?></li>
		  <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
		<?php else: ?>
		  <?php if (isset($sf_request->parent)): ?>
		    <li><?php echo link_to(__('Cancel'), array($resource->parent, 'module' => 'physicalobject', 'action' => 'browse'), array('class' => 'c-btn')) ?></li>
		  <?php endif; ?>
		  <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Create') ?>"/></li>
		<?php endif; ?>
	  </ul>
	</section>

  </form>

<?php end_slot() ?>
