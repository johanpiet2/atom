<?php decorate_with('layout_2col'); ?>

<?php slot('title'); ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', ['width' => '42', 'height' => '42']); ?>
    <?php echo __('Browse Booked Out Items'); ?>
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

			<?php echo $form->renderFormTag(url_for(['module' => 'reports', 'action' => 'reportBookOut']), ['method' => 'get']); ?>

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
		<th><?php echo __('Identifier'); ?></th>
		<th><?php echo __('Title'); ?></th>
		<th><?php echo __('Name of Requestor'); ?></th>
		<th><?php echo __('Dispatcher'); ?></th>
		<th><?php echo __('Requested Period'); ?></th>
		<th><?php echo __('Remarks/Comments'); ?></th>

      </tr>
    </thead><tbody>
    <?php foreach ($pager->getResults() as $result) { ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd'; ?>">
    	<?php
            $bookOutSubjectId = QubitRelation::getObjectsBySubjectId($result->id);

            if (isset($bookOutSubjectId)) {
                foreach ($bookOutSubjectId as $relation) {
                    $informationObjectsBookOut = QubitInformationObject::getById($relation->objectId);
                }
                if (isset($informationObjectsBookOut)) {
                    ?> <td><?php echo link_to($informationObjectsBookOut->identifier, [$informationObjectsBookOut, 'module' => 'informationobject']); ?></td> <?php
                } else { ?> 
					<td>-</td> <?php
                }
            } else { ?> 
				<td>-</td> <?php
            } ?>

			<?php if (isset($result->name)) { ?> <td><?php echo $result->name; ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		
			<?php if (isset($result->requestorId)) { ?> <td><?php echo $result->requestorId; ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->dispatcherId)) { ?> <td><?php echo $result->dispatcherId; ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getTime_period(['cultureFallback' => true]); ?></td> <?php } else { ?> <td>-</td> <?php }	?>
			<?php if (isset($result->id)) { ?> <td><?php echo $result->getRemarks(['cultureFallback' => true]); ?></td> <?php } else { ?> <td>-</td> <?php }	?>

          </td>

        </tr>

      <?php } ?>

    </tbody>
  </table>

<?php end_slot(); ?>

<?php slot('after-content'); ?>
<?php echo get_partial('default/pager', ['pager' => $pager]); ?>
<?php end_slot(); ?>
