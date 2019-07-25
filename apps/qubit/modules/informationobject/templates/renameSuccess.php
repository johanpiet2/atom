<?php decorate_with('layout_2col.php') ?>

<?php slot('sidebar') ?>

  <?php include_component('repository', 'contextMenu') ?>

<?php end_slot() ?>

<?php slot('title') ?>

  <h1><?php echo render_title($resource) ?></h1>

<?php end_slot() ?>

<?php slot('content') ?>

  <div class="modal hide" id="rename-slug-warning">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">×</a>
      <h3><?php echo __('Slug in use') ?></h3>
    </div>

    <div class="modal-body">
      <?php echo __('A slug based on this title already exists so a number has been added to pad the slug.') ?>
    </div>

    <div class="modal-footer">
      <section class="actions">
        <ul>
          <li><a href="#" id="renameModalCancel" class="c-btn c-btn-submit" data-dismiss="modal"><?php echo __('Close') ?></a></li>
        </ul>
      </section>
    </div>
  </div>

  <?php echo $form->renderFormTag(url_for(array('module' => 'informationobject', 'action' => 'rename', 'slug' => $resource->slug)), array('id' => 'rename-form')) ?>

    <?php echo $form->renderHiddenFields() ?>

    <div id="content">

      <fieldset class="collapsible">

        <legend><?php echo __('Rename') ?></legend>

        <p><?php echo __('Use this interface to update the description title, slug (permalink), and/or %1% filename.', array('%1%' => mb_strtolower(sfConfig::get('app_ui_label_digitalobject')))) ?></p>

        <div class="rename-form-field-toggle"><input id="rename_enable_title" type="checkbox" checked="checked" /> <?php echo __('Update title') ?></div>
        <br />

        <?php echo render_field($form->title
          ->label(__('Title'))
          ->help(__('Editing the description title will automatically update the slug field if the "Update slug" checkbox is selected - you can still edit it after.'))
          , $resource) ?>

        <p><?php echo __('Original title') ?>: <em><?php echo $resource->title ?></em></p>

        <div class="rename-form-field-toggle"><input id="rename_enable_slug" type="checkbox" checked="checked" /> <?php echo __('Update slug') ?></div>
        <br />

        <?php echo render_field($form->slug
          ->label(__('Slug'))
          ->help(__('Do not use any special characters or spaces in the slug - only lower case alphanumeric characters (a-z, 0-9) and dashes (-) will be saved. Other characters will be stripped out or replaced. Editing the slug will not automatically update the other fields.'))
          , $resource) ?>

        <p><?php echo __('Original slug') ?>: <em><?php echo $resource->slug ?></em></p>

        <?php if (count($resource->digitalObjectsRelatedByobjectId) > 0): ?>
			<?php for ($n = 0; $n < count($resource->digitalObjectsRelatedByobjectId); $n++): ?>
				<?php if ($n == 0): ?>
					<div class="rename-form-field-toggle"><input id="rename_enable_filename" type="checkbox" /> <?php echo __('Update filename') ?></div>
				  <br />

				  <?php echo render_field($form->filename0
				    ->label(__('Filename'))
				    ->help(__('Do not use any special characters or spaces in the filename - only lower case alphanumeric characters (a-z, 0-9) and dashes (-) will be saved. Other characters will be stripped out or replaced. Editing the filename will not automatically update the other fields.')), $resource) ?>

				  <p><?php echo __('Original filename') ?>: <em><?php echo $resource->digitalObjectsRelatedByobjectId[0]->name ?></em></p>

				<?php elseif($n == 1) : ?>
					<div class="rename-form-field-toggle"><input id="rename_enable_filename1" type="checkbox" /> <?php echo __('Update filename') ?></div>
				  <br />

				  <?php echo render_field($form->filename1
				    ->label(__('Filename'))
				    ->help(__('Do not use any special characters or spaces in the filename - only lower case alphanumeric characters (a-z, 0-9) and dashes (-) will be saved. Other characters will be stripped out or replaced. Editing the filename will not automatically update the other fields.')), $resource) ?>

				  <p><?php echo __('Original filename') ?>: <em><?php echo $resource->digitalObjectsRelatedByobjectId[1]->name ?></em></p>

				<?php elseif($n == 2) : ?>
					<div class="rename-form-field-toggle"><input id="rename_enable_filename2" type="checkbox" /> <?php echo __('Update filename') ?></div>
				  <br />

				  <?php echo render_field($form->filename2
				    ->label(__('Filename'))
				    ->help(__('Do not use any special characters or spaces in the filename - only lower case alphanumeric characters (a-z, 0-9) and dashes (-) will be saved. Other characters will be stripped out or replaced. Editing the filename will not automatically update the other fields.'))
				    , $resource) ?>

				  <p><?php echo __('Original filename') ?>: <em><?php echo $resource->digitalObjectsRelatedByobjectId[2]->name ?></em></p>

				<?php elseif($n == 3) : ?>
					<div class="rename-form-field-toggle"><input id="rename_enable_filename3" type="checkbox" /> <?php echo __('Update filename') ?></div>
				  <br />

				  <?php echo render_field($form->filename3
				    ->label(__('Filename'))
				    ->help(__('Do not use any special characters or spaces in the filename - only lower case alphanumeric characters (a-z, 0-9) and dashes (-) will be saved. Other characters will be stripped out or replaced. Editing the filename will not automatically update the other fields.'))
				    , $resource) ?>

				  <p><?php echo __('Original filename') ?>: <em><?php echo $resource->digitalObjectsRelatedByobjectId[3]->name ?></em></p>

				<?php elseif($n == 4) : ?>
					<div class="rename-form-field-toggle"><input id="rename_enable_filename4" type="checkbox" /> <?php echo __('Update filename') ?></div>
				  <br />

				  <?php echo render_field($form->filename4
				    ->label(__('Filename'))
				    ->help(__('Do not use any special characters or spaces in the filename - only lower case alphanumeric characters (a-z, 0-9) and dashes (-) will be saved. Other characters will be stripped out or replaced. Editing the filename will not automatically update the other fields.'))
				    , $resource) ?>

				  <p><?php echo __('Original filename') ?>: <em><?php echo $resource->digitalObjectsRelatedByobjectId[4]->name ?></em></p>

				<?php endif; ?>

	        <?php endfor; ?>
        <?php endif; ?>

      </fieldset>
    </div>

    <section class="actions">
      <ul>
        <li><input class="c-btn c-btn-submit" id="rename-form-submit" type="submit" value="<?php echo __('Update') ?>"/></li>
        <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
