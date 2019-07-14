<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <h1 class="multiline-header">
    <?php echo render_title($resource) ?>
    <?php echo link_to(image_tag('printer-icon.png', array('alt' => __('Print'))), array($resource, 'module' => 'physicalobject', 'action' => 'boxList'), array('id' => 'print-button', 'title' => __('Print'))) ?>
    <h1><?php echo render_title($resource) ?></h1>
    <span class="sub"><?php echo __('View %1%', array('%1%' => sfConfig::get('app_ui_label_physicalobject'))) ?></span>
  </div>
<?php end_slot() ?>

<?php slot('before-content') ?>
  <?php echo get_component('default', 'translationLinks', array('resource' => $resource)) ?>
<?php end_slot() ?>

<?php echo render_show(__('%1%', array('%1%' => sfConfig::get('app_ui_label_physicalobject'))), $resource) ?>
<?php echo render_show(__('Type'), $resource->type) ?>
<?php echo render_show(__('Location'), $resource->getLocation(array('cultureFallback' => true))) ?>  
<?php echo render_show(__('Unique Identifier'), $resource->getUniqueIdentifier(array('cultureFallback' => true))) ?>
<?php echo render_show(__('Description/Title'), $resource->getDescriptionTitle(array('cultureFallback' => true))) ?> 
<?php echo render_show(__('Period Covered'), $resource->getPeriodCovered(array('cultureFallback' => true))) ?>

<?php echo render_show(__('Extent'), $resource->getExtent(array('cultureFallback' => true))) ?> 
<?php echo render_show(__('Accrual Space'), $resource->getAccrualSpace(array('cultureFallback' => true))) ?> 
<?php echo render_show(__('Forms'), $resource->getForms(array('cultureFallback' => true))) ?> 
<?php echo render_show(__('Repository'), QubitRepository::getById($resource->getRepositoryId(array('cultureFallback' => true)))) ?> 

<div class="field">
  <h3><?php echo __('Related resources') ?></h3>
  <div>
    <ul>
      <?php foreach (QubitRelation::getRelatedObjectsBySubjectId('QubitInformationObject', $resource->id, array('typeId' => QubitTerm::HAS_PHYSICAL_OBJECT_ID)) as $item): ?>
        <li><?php echo link_to(render_title($item), array($item, 'module' => 'informationobject')) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<?php slot('after-content') ?>
  <section class="actions">
    <ul>
      <li><?php echo link_to(__('Edit'), array($resource, 'module' => 'physicalobject', 'action' => 'edit'), array('class' => 'c-btn')) ?></li>
      <li><?php echo link_to(__('Delete'), array($resource, 'module' => 'physicalobject', 'action' => 'delete', 'next' => $sf_request->getReferer()), array('class' => 'c-btn c-btn-delete')) ?></li>
          <li><?php echo link_to(__('Back to List'), array('module' => 'physicalobject', 'action' => 'browse'), array('title' => __('Back to list'), 'class' => 'c-btn')) ?></li>
    </ul>
  </section>
<?php end_slot() ?>
