<?php if ($sf_user->getAttribute('search-realm') && sfConfig::get('app_enable_institutional_scoping')): ?>
  <?php include_component('repository', 'holdingsInstitution', array('resource' => QubitRepository::getById($sf_user->getAttribute('search-realm')))) ?>
<?php else: ?>
  <?php echo get_component('repository', 'logo') ?>
<?php endif; ?>

<?php echo get_component('informationobject', 'treeView') ?>

<?php if (check_field_visibility('app_element_visibility_physical_storage_')): ?>
<?php echo get_component('physicalobject', 'contextMenu', array($resource, 'module' => 'informationobject', 'action' => 'editPhysicalObjects'),array('resource' => $resource)) ?>
<?php endif; ?>

<?php if (check_field_visibility('app_element_visibility_presevetion_storage')): ?>
<?php echo get_component('presevationobject', 'contextMenu', array($resource, 'module' => 'presevationobject', 'action' => 'editPresevationObjects'),array('resource' => $resource)) ?>
<?php endif; ?>

<?php if (check_field_visibility('app_element_visibility_access_storage')): ?>
<?php echo get_component('accessobject', 'contextMenu', array($resource, 'module' => 'accessobject', 'action' => 'editAccessObjects'),array('resource' => $resource)) ?>
<?php endif; ?>

<?php if (check_field_visibility('app_element_visibility_bookout_storage')): ?>
<?php echo get_component('bookoutobject', 'contextMenu', array($resource, 'module' => 'bookoutobject', 'action' => 'editBookoutObjects'),array('resource' => $resource)) ?>
<?php endif; ?>

<?php if (check_field_visibility('app_element_visibility_bookin_storage')): ?>
<?php echo get_component('bookinobject', 'contextMenu', array($resource, 'module' => 'bookinobject', 'action' => 'editBookinObjects'),array('resource' => $resource)) ?>
<?php endif; ?>
