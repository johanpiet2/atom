<?php decorate_with('layout_1col') ?>
<?php use_helper('Date') ?>

<?php slot('title') ?>
  <h1><?php echo __('Browse Service Provider') ?></h1>
<?php end_slot() ?>

<?php slot('before-content') ?>

  <section class="header-options">
    <div class="row">
      <div class="span6">
        <?php echo get_component('search', 'inlineSearch', array(
          'label' => __('Search %1%', array('%1%' => strtolower(sfConfig::get('app_ui_label_serviceProvider')))))) ?>
      </div>
      <div class="span6">
        <?php echo get_partial('default/sortPicker',
          array(
            'options' => array(
              'alphabetic' => __('Alphabetic'),
              'lastUpdated' => __('Most recent')))) ?>
      </div>
    </div>
  </section>

<?php end_slot() ?>

<?php slot('content') ?>
  <table class="table table-bordered sticky-enabled">
    <thead>
      <tr>
        <th>
          <?php echo __('Identifier') ?>
        </th>
        <th>
          <?php echo __('Name') ?>
        </th>
        <th>
          <?php echo __('Repository') ?>
        </th>
        <?php if ('alphabetic' != $sf_request->sort): ?>
          <th>
            <?php echo __('Updated') ?>
          </th>
        <?php endif; ?>
      </tr>
    </thead><tbody>
      <?php foreach ($pager->getResults() as $item): ?>
        <tr>
          <td>
            <?php echo $item->corporateBodyIdentifiers ?>
          </td>
          <td>
            <?php echo link_to(render_title($item), array('module' => 'serviceProvider', 'action' => 'index', 'source' => $item->id)) ?>
          </td>
		    <td>
			  <?php echo render_value(QubitRepository::getById($item->getRepositoryId(array('cultureFallback' => true)))) ?> 
		    </td>
          <?php if ('alphabetic' != $sf_request->sort): ?>
            <td>
              <?php echo format_date($item->updatedAt, 'f') ?>
            </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php end_slot() ?>

<?php slot('after-content') ?>

  <?php echo get_partial('default/pager', array('pager' => $pager)) ?>
  <?php if (QubitAcl::check($serviceprovider, 'create') || sfContext::getInstance()->getUser()->isAdministrator()): ?>
    <section class="actions">
      <ul>
        <li><?php echo link_to(__('Add new'), array('module' => 'serviceProvider', 'action' => 'add'), array('title' => __('Add new'), 'class' => 'c-btn')) ?></li>
      </ul>
    </section>
  <?php endif; ?>
<?php end_slot() ?>
