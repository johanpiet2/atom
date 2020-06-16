<?php use_helper('Date') ?>

<h1><?php echo __('Browse %1%', array('%1%' => sfConfig::get('app_ui_label_physicalobject'))) ?></h1>
    
      <a href="<?php echo url_for(array($resource, 'module' => 'physicalobject', 'action' => 'boxLabelCsvExport')) ?>">
        <i class="icon-print"></i>
        
        <?php echo __('Export') ?>
      </a>
    
<table class="table table-bordered sticky-enabled">
  <thead>
    <tr>
      <th class="sortable">
        <?php echo link_to(__('Name'), array('sort' => ('nameUp' == $sf_request->sort) ? 'nameDown' : 'nameUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('nameUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('nameDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Location'), array('sort' => ('locationUp' == $sf_request->sort) ? 'locationDown' : 'locationUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('locationUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('locationDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Unique Identifier'), array('sort' => ('identifierUp' == $sf_request->sort) ? 'identifierDown' : 'identifierUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('identifierUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('identifierDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Description Title'), array('sort' => ('tittleUp' == $sf_request->sort) ? 'tittleDown' : 'tittleUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('tittleUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('tittleDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Period Covered'), array('sort' => ('periodUp' == $sf_request->sort) ? 'periodDown' : 'periodUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('periodUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('periodDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Extend'), array('sort' => ('extendUp' == $sf_request->sort) ? 'extendDown' : 'extendUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('extendUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('extendDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Finding Aids'), array('sort' => ('findingUp' == $sf_request->sort) ? 'findingDown' : 'findingUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('findingUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('findingDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Forms'), array('sort' => ('formsUp' == $sf_request->sort) ? 'formsDown' : 'formsUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('formsUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('formsDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Accrual Space'), array('sort' => ('accrualUp' == $sf_request->sort) ? 'accrualDown' : 'accrualUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('accrualUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('accrualDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th>
        <?php echo __('Type') ?>
      </th>
    </tr>
  </thead><tbody>
    <?php foreach ($pager->getResults() as $item): ?>
      <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
          <?php echo link_to(render_title($item), array($item, 'module' => 'physicalobject')) ?>
        </td><td>
          <?php echo $item->location ?>
		</td><td>
          <?php echo $item->uniqueIdentifier ?>
        </td><td>
          <?php echo $item->descriptionTitle ?>
        </td><td>
          <?php echo $item->periodCovered ?>
        </td><td>
          <?php echo $item->extend ?>
        </td><td>
          <?php echo $item->findingAids ?>
        </td><td>
          <?php echo $item->accrualSpace ?>
        </td><td>
          <?php echo $item->forms ?>
        </td><td>
          <?php echo $item->type ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php echo get_partial('default/pager', array('pager' => $pager)) ?>


<section class="actions">

   <ul>

      <?php if (QubitAcl::check($resource, 'create')): ?>
        <li><?php echo link_to(__('Add new'), array('module' => 'physicalobject', 'action' => 'add', 'parent' => url_for(array($resource, 'module' => 'physicalobject'))), array('class' => 'c-btn')) ?></li>
      <?php endif; ?>

  </ul>

</section>
