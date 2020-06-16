<?php decorate_with('layout_2col') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', array('width' => '42', 'height' => '42')) ?>
    <?php echo __('Browse Authority Record/Actor Report') ?>
  </h1>
<?php end_slot() ?>

<?php slot('sidebar') ?>
<?php echo $form->renderGlobalErrors() ?>
<section class="sidebar-widget">

	<body onload="javascript:NewCal('dateStart','ddmmyyyy',false,false,24,true);renderCalendar('dateStart','div0');
			  javascript:NewCal('dateEnd','ddmmyyyy',false,false,24,true);renderCalendar('dateEnd','div1');">
  
		<div>
	        <button type="submit" class="btn"><?php echo link_to(__('Back to reports'), array('module' => 'reports', 'action' => 'reportSelect'), array('title' => __('Back to reports'))) ?></button>
		</div>
		<h4><?php echo __('Filter options') ?></h4>
		<div>

			<?php echo $form->renderFormTag(url_for(array('module' => 'reports', 'action' => 'reportAuthorityRecord')), array('method' => 'get')) ?>

			<?php echo $form->renderHiddenFields() ?>

			<div id='divTypeOfReport' style="display: none"> 
				<?php echo $form->className->label('Types of Reports')->renderRow() ?>
			</div>
			
			<?php if (false): ?>
			<?php echo __('Date range') ?>
			<?php echo $form->dateStart->renderError() ?>
			<?php echo $form->dateEnd->renderError() ?>
			<?php echo __('%1% to %2%', array(
			  '%1%' => $form->dateStart->render(),
			  '%2%' => $form->dateEnd->render())) ?>
			<?php endif; ?>

			<?php echo $form->dateOf->renderRow() ?>
			
			<td>
				<?php $currentDate = date('d/m/Y H:i:s', strtotime("-3 months"));
				echo $form->dateStart->renderRow(array('value' => $currentDate,'readonly'=>'true','onchange' => 'checkBigger();'),'Start Date') ?>
				<div id="div0">Auto fill datepicker - Time Period - This will be deleted automatically</div>
			</td>

			<td>
				<?php $currentDate = date('d/m/Y H:i:s');
				echo $form->dateEnd->renderRow(array('value' => $currentDate,'readonly'=>'true','onchange' => ''),'End Date') ?>
				<div id="div1">Auto fill datepicker - dateEnd - This will be deleted automatically</div>
			</td>
						
	        <button type="submit" class="btn"><?php echo __('Search') ?></button>
      </form>

	</div>

</section>
<?php end_slot() ?>

<?php slot('content') ?>

  <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
    <thead>
      <tr>
		<th style="width: 110px"><?php echo __('Authorized Form Of Name') ?></th>
		<th style="width: 110px"><?php echo __('Dates Of Existence') ?></th>
		<th style="width: 110px"><?php echo __('History') ?></th>
		<th style="width: 110px"><?php echo __('Places') ?></th>
		<th style="width: 110px"><?php echo __('Legal Status') ?></th>
		<th style="width: 110px"><?php echo __('Mandates') ?></th>
		<th style="width: 110px"><?php echo __('Internal Structures') ?></th>
		<th style="width: 110px"><?php echo __('General Context') ?></th>
		<th style="width: 110px"><?php echo __('Institution Responsible') ?></th>
		<th style="width: 110px"><?php echo __('Rules') ?></th>
		<th style="width: 110px"><?php echo __('Sources') ?></th>
		<th style="width: 110px"><?php echo __('Revision History') ?></th>
		<th style="width: 110px"><?php echo __('Corporate Body Identifiers') ?></th>
		<th style="width: 110px"><?php echo __('Entity Type Id') ?></th>
		<th style="width: 110px"><?php echo __('Corporate Body Identifiers') ?></th>
		<th style="width: 110px"><?php echo __('Description Status') ?></th>
		<th style="width: 110px"><?php echo __('Description Detail') ?></th>
		<th style="width: 110px"><?php echo __('Description Identifier') ?></th>
		<th style="width: 110px"><?php echo __('Source Standard') ?></th>

        <?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
          <th style="width: 110px"><?php echo __('Updated'); ?></th>
        <?php else: ?>
          <th style="width: 110px"><?php echo __('Created'); ?></th>
        <?php endif; ?>
      </tr>
    </thead><tbody>
    <?php foreach ($pager->getResults() as $result): ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
          <?php $name = render_title($result->getAuthorizedFormOfName(array('cultureFallback' => true))) ?>
 		  <?php if (isset($result->authorizedFormOfName)) { ?> <td><?php echo link_to($result->authorizedFormOfName, array($result, 'module' => 'actor')) ?></td> <?php } else { ?> <td>-</td> <?php }	?>

		  <?php if ($result->datesOfExistence) { ?> <td><?php echo $result->datesOfExistence ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->history) { ?> <td><?php echo substr($result->history,0,55)."..." ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->places) { ?> <td><?php echo $result->places ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->legalStatus) { ?> <td><?php echo $result->legalStatus ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->mandates) { ?> <td><?php echo $result->mandates ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->internalStructures) { ?> <td><?php echo $result->internalStructures ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->generalContext) { ?> <td><?php echo $result->generalContext ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->institutionResponsibleIdentifier) { ?> <td><?php echo $result->institutionResponsibleIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->rules) { ?> <td><?php echo $result->rules ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->sources) { ?> <td><?php echo $result->sources ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->revisionHistory) { ?> <td><?php echo $result->revisionHistory ?></td> <?php } else { ?> <td>-</td> <?php }	?>

		  <?php if ($result->corporateBodyIdentifiers) { ?> <td><?php echo $result->corporateBodyIdentifiers ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->entityTypeId) { ?> <td><?php echo QubitTerm::getById($result->entityTypeId) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->corporateBodyIdentifiers) { ?> <td><?php echo $result->corporateBodyIdentifiers ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->descriptionStatusId) { ?> <td><?php echo QubitTerm::getById($result->descriptionStatusId) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->descriptionDetailId) { ?> <td><?php echo QubitTerm::getById($result->descriptionDetailId) ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->descriptionIdentifier) { ?> <td><?php echo $result->descriptionIdentifier ?></td> <?php } else { ?> <td>-</td> <?php }	?>
		  <?php if ($result->sourceStandard) { ?> <td><?php echo $result->sourceStandard ?></td> <?php } else { ?> <td>-</td> <?php }	?>
	<td>
			<?php if ('CREATED_AT' != $form->getValue('dateOf')): ?>
			<?php echo $result->updatedAt ?>
			<?php else: ?>
			<?php echo $result->createdAt ?>
			<?php endif; ?>
		</td>
    </tr>

      <?php endforeach; ?>
    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
<?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>
