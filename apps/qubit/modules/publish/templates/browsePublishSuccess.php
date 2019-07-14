<head>
</head>
<h1><?php echo __('Publish Preservation') ?></h1>
<h1><?php echo __('Publish Preservation %1%', array('%1%' => sfConfig::get('app_ui_label_presevationobject'))) ?></h1>

<?php echo get_partial('default/pager', array('pager' => $pager)) ?>

<?php decorate_with('layout_1col.php') ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="messages error">
    <h3><?php echo __('Error encountered') ?></h3>
    <div><?php echo $sf_user->getFlash('error') ?></div>
  </div>
<?php endif; ?>

<?php 
	$this->publishYes = QubitXmlImport::translateNameToTermId2('Publish', QubitTerm::PUBLISH_ID, 'Yes');
	$this->publishNo = QubitXmlImport::translateNameToTermId2('Publish', QubitTerm::PUBLISH_ID, 'No');
?>

<?php slot('content') ?>

	<?php echo $form->renderFormTag(url_for(array('module' => 'publish', 'action' => 'browsePublish')), array('method' => 'post')) ?>

    <?php echo $form->renderHiddenFields() ?>
    
<table class="table table-bordered sticky-enabled">
  <thead>
    <tr>
      <th class="sortable">
        <?php echo link_to(__('Name'), array('sort' => ('nameUp' == $sf_request->sort) ? 'nameDown' : 'nameUp') + $sf_data->getRaw('sf_request')->getParameterHolder()->getAll(), array('title' => __('Sort'), 'class' => 'sortable')) ?>
        
        <?php if ('nameUp' == $sf_request->sort): ?>
          <?php echo image_tag('up.gif') ?>
        <?php elseif ('nameDown' == $sf_request->sort): ?>
          <?php echo image_tag('down.gif') ?>
        <?php endif; ?>
      </th><th>
        <?php echo __('Identifier') ?>
      </th><th>
        <?php echo __('Publish') ?>
      </th><th>
        <?php echo __('Restriction Condition') ?>
      </th><th>
        <?php echo __('Refusal') ?>
      </th><th>
        <?php echo __('Sensitivity') ?>
      </th><th>
        <?php echo __('Classification') ?>
      </th><th>
        <?php echo __('Restriction') ?>
      </th>
    </tr>
  </thead><tbody>
    <?php foreach ($pager->getResults() as $item): ?>
      <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
        	<?php $d="id_{$row}"; ?>
        	<input type="hidden" name="<?php echo $d; ?>" id="<?php echo $d; ?>" value="<?php echo $item->id; ?>"/>
        	<?php //find Information Object to link to 
        		// find relation
        		foreach (QubitRelation::getObjectsBySubjectId($item->id, array('typeId' => QubitTaxonomy::ACCESS_TYPE_ID)) as $item2)
        		{
    				//find Information Object
					$this->informationObj = new QubitInformationObject;
					$this->informationObj = QubitInformationObject::getById($item2->objectId);
        		}
        	?>
         	<?php echo link_to(render_title($this->informationObj), array($this->informationObj, 'module' => 'informationobject')) ?>
        </td><td>
		     <?php echo $this->informationObj->identifier ?>
        </td><td>
			<?php if ($item->publish == 'Please Select') { ?>
		      <?php echo '-' ?>
		    <?php } else { ?>
			    <?php $n="publish_{$row}"; ?>

				<input type="radio" name="<?php echo $n; ?>" id="<?php echo $n; ?>" value="Yes" class="radio" onclick="updateYesNo('<?php echo $n; ?>~<?php echo $this->publishYes; ?>','<?php echo $d; ?>','<?php echo $pager->getResults()->count(); ?>')"
				
				<?php if (isset($item->publish) && $item->publish=="Yes") echo "checked";?>
				/> Yes
				<input type="radio" name="<?php echo $n; ?>" value="No" id="<?php echo $n; ?>" class="radio" onclick="updateYesNo('<?php echo $n; ?>~<?php echo $this->publishNo; ?>','<?php echo $d; ?>','<?php echo $pager->getResults()->count(); ?>')" 
				<?php if (isset($item->publish) && $item->publish=="No") echo "checked";?>
				/> No
		    <?php } ?>	    
        </td><td>
			<?php if ($item->restriction_condition == 'Please Select'): ?>
		      <?php echo '-' ?>
		    <?php else: ?>
		      <?php echo $item->restriction_condition ?>
		    <?php endif; ?>
        </td><td>
			<?php if ($item->refusal == 'Please Select'): ?>
		      <?php echo '-' ?>
		    <?php else: ?>
		      <?php echo $item->refusal ?>
		    <?php endif; ?>
 		</td><td>
			<?php if ($item->sensitivity == 'Please Select'): ?>
		      <?php echo '-' ?>
		    <?php else: ?>
		      <?php echo $item->sensitivity ?>
		    <?php endif; ?>
        </td><td>
			<?php if ($item->classification == 'Please Select'): ?>
		      <?php echo '-' ?>
		    <?php else: ?>
		      <?php echo $item->classification ?>
		    <?php endif; ?>
        </td>
        <td>
			<?php if ($item->restriction == 'Please Select'): ?>
		      <?php echo '-' ?>
		    <?php else: ?>
		      <?php echo $item->restriction ?>
		    <?php endif; ?>
        </td>
	</tr>
    <?php endforeach; ?>
   
  </tbody>
</table>
 <iframe name="inlineframe" src="" frameborder="0" scrolling="auto" width="0" height="0" marginwidth="0" marginheight="0" ></iframe>
<script>
function updateYesNo(eElement,eId,eCount)
{
	var partsOfStr = eElement.split('~');
	var x=document.getElementById(partsOfStr[0]);
	var y=document.getElementById(eId);
	x.value=partsOfStr[1];

//alert("y.value="+y.value'&cValue='+x.value);

	//to fix jjp
    window.frames['inlineframe'].location.replace('http://localhost/atom/order.php?id='+y.value+'&cValue='+x.value);
//    window.frames['inlineframe'].location.replace('http://192.168.65.117/atom/publish.php?id='+y.value+'&cValue='+x.value);
}

</script>
<section class="actions">
	<ul class="clearfix links">
		<li><?php echo link_to(__('Return'), array($resource, 'module' => 'informationobject', 'action' => 'browse'), array('class' => 'c-btn')) ?></li>
	  	<li><input class="form-submit c-btn c-btn-submit" type="submit" value="<?php echo __('Continue') ?>"/></li>
	</ul>
</section>
<?php end_slot() ?>
</form>
