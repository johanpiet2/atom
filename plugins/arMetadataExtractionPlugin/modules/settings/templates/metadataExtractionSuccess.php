<?php decorate_with('layout_2col.php'); ?>

<?php slot('sidebar'); ?>
  <?php echo get_component('settings', 'menu'); ?>
<?php end_slot(); ?>

<?php slot('title'); ?>
  <h1><?php echo __('Metadata Extraction Settings'); ?></h1>
<?php end_slot(); ?>

<?php slot('content'); ?>

<?php echo $form->renderGlobalErrors(); ?>

<form action="<?php echo url_for('settings/metadataExtraction'); ?>" method="post">
  <?php echo $form->renderHiddenFields(); ?>

  <div id="content">

    <table class="table sticky-enabled">
      <thead>
        <tr>
          <th><?php echo __('Name'); ?></th>
          <th><?php echo __('Value'); ?></th>
        </tr>
      </thead>

      <tbody>

        <?php
        // Map labels to fields
        $fields = [
          'metadata_extraction_enabled'   => __('Enable metadata extraction'),
          'extract_exif'                  => __('Extract EXIF metadata'),
          'extract_iptc'                  => __('Extract IPTC metadata'),
          'extract_xmp'                   => __('Extract XMP metadata'),
          'overwrite_title'               => __('Overwrite title from metadata'),
          'overwrite_description'         => __('Overwrite description from metadata'),
          'auto_generate_keywords'        => __('Auto-generate keywords'),
          'extract_gps_coordinates'       => __('Extract GPS coordinates'),
          'add_technical_metadata'        => __('Store technical metadata'),
        ];
        ?>

        <?php foreach ($fields as $key => $label): ?>
          <tr>
            <td>
              <?php echo $form[$key]->renderLabel($label); ?>
            </td>
            <td>
              <?php if (strlen($err = $form[$key]->renderError())): ?>
                <?php echo $err; ?>
              <?php endif; ?>

              <?php echo $form[$key]->render(); ?>
            </td>
          </tr>
        <?php endforeach; ?>

        <!-- Dropdown field -->
        <tr>
          <td>
            <?php echo $form['technical_metadata_target_field']->renderLabel(
              __('Technical metadata target field')
            ); ?>
          </td>
          <td>
            <?php if (strlen($err = $form['technical_metadata_target_field']->renderError())): ?>
              <?php echo $err; ?>
            <?php endif; ?>

            <?php echo $form['technical_metadata_target_field']->render(); ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>

  <section class="actions">
    <ul>
      <li>
        <input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save'); ?>"/>
      </li>
    </ul>
  </section>

</form>

<?php end_slot(); ?>
