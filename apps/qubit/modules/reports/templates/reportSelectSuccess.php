<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <?php if (isset($resource)): ?>
    <h1 class="multiline">
      <?php echo $title ?>
      <span class="sub"><?php echo render_title($resource) ?></span>
    </h1>
  <?php else: ?>
    <h1><?php echo "Select Report Type" ?></h1>
  <?php endif; ?>
<?php end_slot() ?>

<?php slot('content') ?>

  <?php if (isset($resource)): ?>
    <?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'reports', 'action' => 'reportSelect')), array('enctype' => 'multipart/form-data')) ?>
  <?php else: ?>
    <?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportSelect')), array('enctype' => 'multipart/form-data')) ?>
  <?php endif; ?>

    <?php echo $form->renderHiddenFields() ?>

    <section id="content">

      <fieldset class="collapsible">

        <input type="hidden" name="importType" value="<?php echo esc_entities($type) ?>"/>

          <div class="form-item">
            <label><?php echo __('Type') ?></label>
            <select name="objectType">
              <option value="access"><?php echo sfConfig::get('app_ui_label_accession', __('Access (Archival Description)')) ?></option>
              <!--option value="accession"><?php echo sfConfig::get('app_ui_label_accession', __('Accession')) ?></option -->
              <option value="informationObject"><?php echo sfConfig::get('app_ui_label_informationobject', __('Archival Description')) ?></option>
              <option value="audit_trail"><?php echo __('Audit Trail') ?></option>
              <option value="authorityRecord"><?php echo sfConfig::get('app_ui_label_actor', __('Authority Record/Actor')) ?></option>
              <option value="booked_in"><?php echo sfConfig::get('app_ui_label_user', __('Booked In')) ?></option>
              <option value="booked_out"><?php echo sfConfig::get('app_ui_label_user', __('Booked Out')) ?></option>
              <option value="donor"><?php echo sfConfig::get('app_ui_label_donor', __('Donor')) ?></option>
              <!--option value="function"><?php echo sfConfig::get('app_ui_label_donor', __('Function')) ?></option -->
              <option value="physical_storage"><?php echo __('Physical Storage') ?></option>
              <option value="preservation"><?php echo __('Preservation') ?></option>
              <option value="registry"><?php echo __('Registry') ?></option>
              <option value="repository"><?php echo sfConfig::get('app_ui_label_donor', __('Repository/Archival Institution')) ?></option>
              <option value="researcher"><?php echo sfConfig::get('app_ui_label_researcher', __('Researcher')) ?></option>
              <option value="service_provider"><?php echo __('Service Provider') ?></option>
              <option value="user"><?php echo sfConfig::get('app_ui_label_researcher', __('User Action')) ?></option>
              <!--option value="term"><?php echo __('Terms') ?></option>
              <option value="user"><?php echo sfConfig::get('app_ui_label_user', __('User')) ?></option>
              <option value="dc"><?php echo __('To Add') ?></option -->
            </select>

          <div class="form-item">
    </section>

    <section class="actions">
      <ul>
        <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Select') ?>"/></li>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
