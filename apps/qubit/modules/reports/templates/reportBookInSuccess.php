<?php decorate_with('layout_2col'); ?>

<?php slot('title'); ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', ['width' => '42', 'height' => '42']); ?>
    <?php echo __('Browse Book In Items'); ?>
  </h1>
<?php end_slot(); ?>

<?php slot('sidebar'); ?>
<?php echo $form->renderGlobalErrors(); ?>
<section class="sidebar-widget">

	<body>
  
		<div>
	        <button type="submit" class="btn"><?php echo link_to(__('Back to reports'), ['module' => 'reports', 'action' => 'reportSelect'], ['title' => __('Back to reports')]); ?></button>
		</div>
		<h4><?php echo __('Filter options'); ?></h4>
		<div>

			<?php echo $form->renderFormTag(url_for(['module' => 'reports', 'action' => 'reportBookIn']), ['method' => 'get']); ?>

			<?php echo $form->renderHiddenFields(); ?>

			
			<td>
			  <?php echo render_field($form->dateStart->label(__('Date Start')), null, ['type' => 'date']); ?>
			<td>
			  <?php echo render_field($form->dateEnd->label(__('Date End')), null, ['type' => 'date']); ?>
			</td>						
						
	        <button type="submit" class="btn"><?php echo __('Search'); ?></button>
      </form>

	</div>

</section>
<?php end_slot(); ?>

<?php slot('content'); ?>

  <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
    <thead>
      <tr>
		<th><?php echo __('Title'); ?></th>
		<th><?php echo __('Name of Receiver'); ?></th>
		<th><?php echo __('Location'); ?></th>
		<th><?php echo __('Requested Period'); ?></th>
		<th><?php echo __('Remarks/Comments'); ?></th>
		<th><?php echo __('Record Condition'); ?></th>

        <?php if ('CREATED_AT' != $form->getValue('dateOf')) { ?>
          <th style="width: 110px"><?php echo __('Updated'); ?></th>
        <?php } else { ?>
          <th style="width: 110px"><?php echo __('Created'); ?></th>
        <?php } ?>
      </tr>
    </thead><tbody>
    <?php foreach ($pager->getResults() as $result) { ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd'; ?>">
		    
				<?php if (isset($result->name)) { ?> <td><?php echo $result->name; // link_to($result->name, array($result, 'module' => 'informationobject'))?></td> <?php } else { ?> <td>-</td> <?php }	?>

				<?php if (isset($result->requestorId)) { ?> <td><?php echo $result->requestorId; ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getLocation(['cultureFallback' => true]); ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getTime_period(['cultureFallback' => true]); ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->id)) { ?> <td><?php echo $result->getRemarks(['cultureFallback' => true]); ?></td> <?php } else { ?> <td>-</td> <?php }	?>
				<?php if (isset($result->record_condition)) { ?> <td><?php echo $result->record_condition; ?></td> <?php } else { ?> <td>-</td> <?php }	?>

          </td>
		<td>
			<?php if ('CREATED_AT' != $form->getValue('dateOf')) { ?>
			<?php echo $result->updatedAt; ?>
			<?php } else { ?>
			<?php echo $result->createdAt; ?>
			<?php } ?>
		</td>

        </tr>

      <?php } ?>

    </tbody>
  </table>

<?php end_slot(); ?>

<?php slot('after-content'); ?>
<?php echo get_partial('default/pager', ['pager' => $pager]); ?>
<?php end_slot(); ?>
