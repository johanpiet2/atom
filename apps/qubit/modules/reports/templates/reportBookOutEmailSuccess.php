<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Booked Out Items') ?>
  </h1>
<?php end_slot() ?>

<?php slot('sidebar') ?>
<?php echo $form->renderGlobalErrors() ?>
<section class="sidebar-widget">

	<body onload="javascript:NewCal('dateStart','ddmmyyyy',false,false,24,true);renderCalendar('dateStart','div0');
			  javascript:NewCal('dateEnd','ddmmyyyy',false,false,24,true);renderCalendar('dateEnd','div1');toggleOff('div3');">
  
		<div>
	        <button type="submit" class="btn"><?php echo link_to(__('Back to reports'), array('module' => 'reports', 'action' => 'reportSelect'), array('title' => __('Back to reports'))) ?></button>
		</div>
		<h4><?php echo __('Filter options') ?></h4>
		<div>

			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportBookOut')), array('method' => 'get')) ?>

			<?php echo $form->renderHiddenFields() ?>

						
	        <button type="submit" class="btn"><?php echo __('Search') ?></button>
      </form>

	</div>

</section>
<?php end_slot() ?>

<?php slot('content') ?>

  <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
    <thead>
      <tr>
		<th><?php echo __('Identifier') ?></th>
		<th><?php echo __('Title') ?></th>
		<th><?php echo __('Dispatcher') ?></th>
		<th><?php echo __('e-Mail') ?></th>
		<th><?php echo __('Due date') ?></th>
		<th><?php echo __('ID') ?></th>
      </tr>
    </thead><tbody>
    
	<?php 
		foreach ($overdue as $result): ?>
		<?php  //get all overdue items
            $vDay = substr($result["TIME_PERIOD"], 0, strpos($result["TIME_PERIOD"], "/"));
            $vRes = substr($result["TIME_PERIOD"], strpos($result["TIME_PERIOD"], "/") + 1);
            $vMonth = substr($vRes, 0, strpos($vRes, "/"));
            $vYear = substr($vRes, strpos($vRes, "/") + 1,4);
	        $s = $vYear . "/" . $vMonth . "/" . $vDay . " 23.59.59";
	        $dueDate = strtotime($s);
			$dueDate=date('Y-m-d H:i:s', strtotime('+1 day', $dueDate));
			echo $dueDate."<br>";
			$datetime = date('Y-m-d H:i:s');
			$diff = strtotime($datetime) - strtotime($dueDate);
			echo $diff."<br>";
			if ($diff <= 0) {
		?>
				<tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
				<?php
					$bookOutSubjectId = QubitRelation::getObjectsBySubjectId($result["ID"]);  

					if (isset($bookOutSubjectId)) 
					{ 
						foreach ($bookOutSubjectId as $relation)
						{
							$informationObjectsBookOut = QubitInformationObject::getById($relation->objectId); 
						}
						if (isset($informationObjectsBookOut)) 
						{ 
							?> <td><?php echo $informationObjectsBookOut->identifier ?></td> <?php
						} 
						else { ?> 
							<td>-</td> <?php 
						} 
					} 
					else { ?> 
						<td>-</td> <?php 
					} ?>
					<?php if (isset($result["NAME"])) { ?> <td><?php echo $result["NAME"] ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result["USERNAME"])) { ?> <td><?php echo $result["USERNAME"] ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result["EMAIL"])) { ?> <td><?php echo $result["EMAIL"] ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result["TIME_PERIOD"])) { ?> <td><?php echo $result["TIME_PERIOD"] ?></td> <?php } else { ?> <td>-</td> <?php }	?>
					<?php if (isset($result["ID"])) { ?> <td><?php echo $result["ID"] ?></td> <?php } else { ?> <td>-</td> <?php }	?>

				</tr>
	      <?php } ?>
      <?php endforeach; ?>

    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
<?php end_slot() ?>
