<?php decorate_with('layout_1col.php') ?>

<h1><?php echo __('Edit Access%1%', array('%1%' => sfConfig::get('app_ui_label_accessobject'))) ?></h1>
<h1 class="label"><?php echo render_title($resource) ?></h1>
<?php echo $form->renderGlobalErrors() ?>

<?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
  <?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'accessobject', 'action' => 'edit'))) ?>
<?php else: ?>

  <?php echo $form->renderFormTag(url_for(array('module' => 'accessobject', 'action' => 'add'))) ?>
<?php endif; ?>

  <?php echo $form->renderHiddenFields() ?>
  <div id="content">
  <fieldset class="collapsible">

  <?php echo render_field($form->name, $resource) ?>

  <?php echo $form->condition->renderRow() ?>

  <?php echo $form->refusal->renderRow() ?>

  <?php echo $form->sensitivity->renderRow() ?>
  
  <?php echo $form->publish->renderRow() ?>
  
  <?php echo $form->classification->renderRow() ?>
  
  <?php echo $form->restriction->renderRow() ?>

  </fieldset>

    </div>
  
    <section class="actions">
      <ul>
        <?php if (null !== $next = $form->getValue('next')): ?>
          <li><?php echo link_to(__('Cancel'), $next, array('class' => 'c-btn')) ?></li>
        <?php else: ?>
          <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'accessobject'), array('class' => 'c-btn')) ?></li>
        <?php endif; ?>
        <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
      </ul>
    </section>
</form>
<?php end_slot() ?>
