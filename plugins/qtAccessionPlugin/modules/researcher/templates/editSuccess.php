<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo __('Add Researcher') ?>
    <span class="sub"><?php echo render_title($resource) ?></span>
  </h1>
<?php end_slot() ?>

<?php slot('content') ?>

  <?php echo $form->renderGlobalErrors() ?>

  <?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
    <?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'researcher', 'action' => 'edit')), array('id' => 'editForm')) ?>
  <?php else: ?>
    <?php echo $form->renderFormTag(url_for(array('module' => 'researcher', 'action' => 'add')), array('id' => 'editForm')) ?>
  <?php endif; ?>

    <?php echo $form->renderHiddenFields() ?>

    <section id="content">

      <fieldset class="collapsible" id="identityArea">

        <legend><?php echo __('Identity area') ?></legend>

        <?php echo render_field($form->corporateBodyIdentifiers->label(__('Identifier')), $resource) ?>
        <?php echo render_field($form->authorizedFormOfName->label(__('Authorized form of name').' <span class="form-required" title="'.__('This is a mandatory element.').'"></span>'), $resource) ?>
        <?php echo render_field($form->repositoryId->label(__('Repository').' <span class="form-required" title="'.__('This is a mandatory element.').'"></span>'), $resource) ?>

      </fieldset>

      <fieldset class="collapsible" id="contactArea">

        <legend><?php echo __('Contact area') ?></legend>

        <?php echo get_partial('contactinformation/edit', $contactInformationEditComponent->getVarHolder()->getAll()) ?>

      </fieldset>

    </section>

    <section class="actions">
      <ul class="clearfix links">
        <?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
          <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'researcher'), array('title' => __('Cancel'), 'class' => 'c-btn')) ?></li>
          <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
        <?php else: ?>
          <li><?php echo link_to(__('Cancel'), array('module' => 'researcher', 'action' => 'list'), array('title' => __('Cancel'), 'class' => 'c-btn')) ?></li>
          <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Create') ?>"/></li>
        <?php endif; ?>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
