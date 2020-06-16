<?php use_helper('Date') ?>

<h1><?php echo __('Browse Booked Out Items %1%', array('%1%' => sfConfig::get('app_ui_label_bookoutobject'))) ?></h1>

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
        <?php echo link_to(__('Remarks/Comments'), array('sort' => ('remarksUp' == $sf_request->sort) ? 'remarksDown' : 'remarksUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('remarksUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('remarksDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th class="sortable">
        <?php echo link_to(__('Time Taken'), array('sort' => ('timeUp' == $sf_request->sort) ? 'timeDown' : 'timeUp') + $sf_request->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        <?php if ('timeUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('timeDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th>
        <?php echo __('Name of Requestor') ?>
      </th><th>
        <?php echo __('Dispatcher') ?>
      </th><th>
        <?php echo __('Condition') ?>
	  </th><th>
        <?php echo __('Due Date') ?>
	  </th><th>
        <?php echo __('Action') ?>
      </th>
    </tr>
  </thead><tbody>
    <?php foreach ($pager->getResults() as $item): ?>
      <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
		<?php echo link_to(render_title($item), array($item, 'module' => 'bookoutobject', 'action' => 'editBookout')) ?>
        </td><td>
          <?php echo $item->remarks ?>
        </td><td>
          <?php echo $item->time_period ?>
        </td><td>
          <?php echo $item->requestorId ?>
        </td><td>
          <?php echo $item->dispatcherId ?>
        </td><td>
          <?php echo $item->record_condition ?>
		</td><td>
		  <?php	
		  $date = ($item->time_period);
		  $date1 = str_replace('-', '/', $date);
		  $tomorrow = date('d-m-Y',strtotime($date1 . "+1 day"));
		  echo $date1 . "\n";
		  echo $tomorrow;
		  
//			$datetaken = new DateTime($item->time_period);
//			$datetaken->modify('+1 day');
//			echo '<font color="Red">'.$datetaken->format('d/m/Y H:i'); ?>
		</td><td>
		<?php echo link_to(__('Book In'), array($item, 'module' => 'informationobject', 'action' => 'editBookinObjects' ), array('class' => 'c-btn c-btn-submit')) ?>
		<?php echo link_to(__('ReBookout'), array($item, 'module' => 'bookoutobject', 'action' => 'editBookout'), array('class' => 'c-btn c-btn-submit')) ?>
		</td>
	  </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php echo get_partial('default/pager', array('pager' => $pager)) ?>

 <section class="actions">
      <ul>
		<li><input class="c-btn c-btn-submit" type="button" onclick="history.back();" value="Back"></li>
      </ul>
  </section>

