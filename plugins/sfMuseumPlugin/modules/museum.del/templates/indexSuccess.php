<?php decorate_with('layout_2col'); ?>
DEBUG - Museum template loaded!<br>
<?php slot('sidebar'); ?>
  <?php if (isset($resource)) { ?>
    <?php include_component('informationobject', 'contextMenu'); ?>
  <?php } ?>
<?php end_slot(); ?>

<?php slot('title'); ?>
  <h1><?php echo isset($resource) ? render_title($resource) : __('Museum Object'); ?></h1>
<?php end_slot(); ?>

<?php slot('content'); ?>
  <?php if (!isset($resource)) { ?>
    <div class="error">
      <p><?php echo __('Error: Resource not found'); ?></p>
    </div>
  <?php } else { ?>
    <section id="museumContent">
      <div class="field">
        <h3><?php echo __('Identity area'); ?></h3>
        
        <?php if ($resource->referenceCode) { ?>
          <div class="field">
            <h4><?php echo __('Reference code'); ?></h4>
            <div><?php echo $resource->referenceCode; ?></div>
          </div>
        <?php } ?>
        
        <?php if ($resource->getTitle()) { ?>
          <div class="field">
            <h4><?php echo __('Title'); ?></h4>
            <div><?php echo render_title($resource); ?></div>
          </div>
        <?php } ?>
        
        <?php if ($resource->levelOfDescription) { ?>
          <div class="field">
            <h4><?php echo __('Level of description'); ?></h4>
            <div><?php echo render_value($resource->levelOfDescription); ?></div>
          </div>
        <?php } ?>
        
        <?php if ($resource->getExtentAndMedium()) { ?>
          <div class="field">
            <h4><?php echo __('Extent and medium'); ?></h4>
            <div><?php echo render_value($resource->getExtentAndMedium(['cultureFallback' => true])); ?></div>
          </div>
        <?php } ?>
      </div>
      
      <!-- Museum-specific fields will go here -->
      <div class="field">
        <h3><?php echo __('Museum metadata'); ?></h3>
        <p><?php echo __('Museum-specific fields will be displayed here.'); ?></p>
      </div>
    </section>
  <?php } ?>
<?php end_slot(); ?>

<?php if (isset($resource)) { ?>
  <?php slot('after-content'); ?>
    <section class="actions">
      <ul>
        <?php if (QubitAcl::check($resource, 'update')) { ?>
          <li><?php echo link_to(__('Edit'), [$resource, 'module' => 'museum', 'action' => 'edit'], ['class' => 'c-btn']); ?></li>
        <?php } ?>
      </ul>
    </section>
  <?php end_slot(); ?>
<?php } ?>
