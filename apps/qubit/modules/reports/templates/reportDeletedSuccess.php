<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Deleted Records') ?>
  </h1>
<?php end_slot() ?>

<?php slot('sidebar') ?>
<?php echo $form->renderGlobalErrors() ?>
<section class="sidebar-widget">

	<body>
  

			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportInformationObject')), array('method' => 'get')) ?>

			<?php echo $form->renderHiddenFields() ?>

      </form>

	</div>
		<div>
	        <button type="submit" class="btn"><?php echo link_to(__('Back to reports'), array('module' => 'reports', 'action' => 'reportSelect'), array('title' => __('Back to reports'))) ?></button>
		</div>

</section>
<?php end_slot() ?>

<?php slot('content') ?>

  <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
    <thead>
      <tr>
		<th style="width: 110px"><?php echo __('Identifier') ?></th>
		<th style="width: 200px"><?php echo __('User') ?></th>
		<th style="width: 200px"><?php echo __('Action Date') ?></th>
		<th style="width: 2500px"><?php echo __('Action') ?></th>
		<th style="width: 200px"><?php echo __('Table') ?></th>
		<th style="width: 200px"><?php echo __('Query') ?></th>

      </tr>
    </thead><tbody>
    <?php foreach ($auditDeletedObjects as $result): ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
			<td><?php echo $result['RECORD_ID'] ?></td>
			<td><?php echo $result['USER'] ?></td>
			<td><?php echo $result['ACTION_DATE_TIME'] ?></td>
			<td><?php echo $result['ACTION'] ?></td>
			<td><?php echo $result['DB_TABLE'] ?></td>
			<td><?php echo $result['DB_QUERY'] ?></td>
        </tr>
      <?php endforeach; ?>

    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
<?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>
