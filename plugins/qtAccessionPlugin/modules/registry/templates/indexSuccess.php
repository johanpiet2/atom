<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo __('View Register') ?>
    <span class="sub"><?php echo render_title($resource) ?></span>
  </h1>
<?php end_slot() ?>

<?php slot('before-content') ?>
  <?php if (isset($errorSchema)): ?>
    <div class="messages error">
      <ul>
        <?php foreach ($errorSchema as $error): ?>
          <li><?php echo $error ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
<?php end_slot() ?>

<?php echo render_show(__('Identifier'), render_value($resource->getCorporateBodyIdentifiers(array('cultureFallback' => true)))) ?>

<?php echo render_show(__('Authorized form of name'), render_value($resource->getAuthorizedFormOfName(array('cultureFallback' => true)))) ?>

<div class="section" id="contactArea">

  <h2><?php echo __('Contact area') ?></h2>

  <?php foreach ($resource->contactInformations as $contactItem): ?>
    <?php echo get_partial('contactinformation/contactInformation', array('contactInformation' => $contactItem)) ?>
  <?php endforeach; ?>

</div> <!-- /.section#contactArea -->

<div class="section" id="archivalDescriptionArea">

  <h2><?php echo __('Archival Description area') ?></h2>

  <div class="field">

    <h3><?php echo __('Related Archival Description(s)') ?></h3>

    <div>
      <ul>
 
		<?php foreach ($registry as $item): ?>
			<table border="0" width="80%">
				<tr>
					<td width="30%">

						<?php echo link_to(render_title($item->identifier), array($item, 'module' => 'informationobject')) ?> 
					</td>
					<td width="70%">
						<?php echo $item ?>
					</td>	
				</tr>
			</table>
		<?php endforeach; ?>
      </ul>
    </div>

  </div>

</div> <!-- /.section#archivalDescriptionArea -->

<?php slot('after-content') ?>

  <?php echo get_partial('default/pager', array('pager' => $pager)) ?>
  
  <section class="actions">
    <ul>            
      <?php if (QubitAcl::check($resource, 'update')): ?>
        <li><?php echo link_to(__('Edit'), array($resource, 'module' => 'registry', 'action' => 'update'), array('title' => __('Edit'), 'class' => 'c-btn')) ?></li>
      <?php endif; ?>
      <?php if (QubitAcl::check($resource, 'delete')): ?>
        <li><?php echo link_to(__('Delete'), array($resource, 'module' => 'registry', 'action' => 'delete', 'source' => $resource->id), array('title' => __('Delete'), 'class' => 'c-btn c-btn-delete')) ?></li>
      <?php endif; ?>
      <?php if (QubitAcl::check($resource, 'create')): ?>
        <li><?php echo link_to(__('Add new'), array('module' => 'registry', 'action' => 'add'), array('title' => __('Add new'), 'class' => 'c-btn')) ?></li>
      <li><?php echo link_to(__('Back to list'), array('module' => 'registry', 'action' => 'browse'), array('title' => __('Back to list'), 'class' => 'c-btn')) ?></li>
      <?php endif; ?>
    </ul>
  </section>
  
<?php end_slot() ?>

