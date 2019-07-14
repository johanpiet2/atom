  <h1 class="multiline">
    <?php echo render_title($resource) ?>
  </h1>

  <?php echo $form->renderGlobalErrors() ?>

  <?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'informationobject', 'action' => 'editPresevationObjects'))) ?>

    <?php echo $form->renderHiddenFields() ?>
<body onload="toggleOff('div0');toggleOff('div1');">

<table width="80%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
  <fieldset class="collapsible">
        <div class="form-item">
          <input class="add" type="hidden" value="<?php echo url_for(array($resource, 'module' => 'informationobject', 'action' => 'editPresevationObjects')) ?> #name"/>
          <input class="list" type="hidden" value="<?php echo url_for(array('module' => 'presevationobject', 'action' => 'autocomplete')) ?>"/>
        </div>
	  <tr>
		<td width="80%" colspan="3"><?php echo $form->name->renderRow(array('readonly'=>'true')) ?></td>
	  </tr>
	  <tr>
		<td><?php echo $form->availability->label(__('Available'))->renderRow() ?></td>
		<td>&nbsp;</td>
		<td><?php echo $form->restoration->renderRow() ?></td>
	  </tr>
	  <tr>
		<td><?php echo $form->conservation->renderRow(array('size' => 0)) ?></td>
		<td>&nbsp;</td>
		<td><?php echo $form->type->label(__('Method of Storage'))->renderRow() ?></td>
	  </tr>
	  <tr>
		<td><?php echo $form->measure->renderRow() ?></td>
		<td>&nbsp;</td>
		<td><?php echo $form->usability->renderRow() ?></td>
	  </tr>
	  <tr>
		<td><?php echo $form->condition->renderRow() ?></td>
		<td>&nbsp;</td>
		<td></td>
	  </tr>
    </fieldset>

   <tr>
   		<td colspan=3>
			<div class="actions section">

			<h2 class="element-invisible"><?php echo __('Actions') ?></h2>

			<section class="actions">
			  <ul>
				<li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
				<li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
			  </ul>
			</section>
		</td>
	</tr>
 </div>
</table>
</form>

<script type="text/javascript">
function toggleOff(id)
{
	if (id == "div0")
	{
		if ("/prod/index.php/digital-copy-permanantly-not-available-reason-as-text-field" == document.getElementById("hard").value)
		{
			document.getElementById(id).style.display="block";
		}
		else
		{
			document.getElementById(id).style.display="none";
		} 
	}
	if (id=="div1")
	{
		if ("/prod/index.php/document-permanantly-not-available-reason-as-text-field" == document.getElementById("digital").value)
		{
			document.getElementById(id).style.display="block";
		}
		else
		{
			document.getElementById(id).style.display="none";
		}
	}
}
</script>
