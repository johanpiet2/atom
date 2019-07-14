<?php use_helper('Javascript') ?>

<h1 class="multiline">
  <?php echo __('Edit %1% permissions', array('%1%' => lcfirst(sfConfig::get('app_ui_label_service_provider')))) ?>
  <span class="sub"><?php echo render_title($resource) ?></span>
</h1>

<?php echo get_partial('aclGroup/addServiceProviderDialog', array('basicActions' => $basicActions)) ?>

<?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'user', 'action' => 'editServiceProviderAcl')), array('id' => 'editForm')) ?>

  <div id="content">

    <fieldset class="collapsible">

      <legend><?php echo __('Edit permissions') ?></legend>

      <?php foreach ($serviceProviders as $key => $item): ?>
        <div class="form-item">
        <?php if (count($serviceProviders) == 1): ?>
          <?php echo get_component('aclGroup', 'aclTable', array('object' => QubitServiceProvider::getById($key), 'permissions' => $item, 'actions' => $basicActions)) ?>
        	<?php endif; ?>
        	<?php if ($key == 6): ?>
          <?php echo get_component('aclGroup', 'aclTable', array('object' => QubitServiceProvider::getById($key), 'permissions' => $item, 'actions' => $basicActions)) ?>
        	<?php endif; ?>
        </div>
      <?php endforeach; ?>

    </fieldset>

  </div>

  <section class="actions">
    <ul>
      <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'user', 'action' => 'indexServiceProviderAcl'), array('class' => 'c-btn')) ?></li>
      <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
    </ul>
  </section>

</form>
