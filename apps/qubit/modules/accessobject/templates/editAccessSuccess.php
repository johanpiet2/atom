<?php //decorate_with('layout_1col.php') ?>

<h1><?php echo __('Edit Access%1%', array('%1%' => sfConfig::get('app_ui_label_accessobject'))) ?></h1>
<h1 class="multiline"><?php echo render_title($resource) ?></h1>
<?php echo $form->renderGlobalErrors() ?>
<body onload="toggleOff();">
	<?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
	  <?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'accessobject', 'action' => 'editAccess'))) ?>
	<?php else: ?>
	  <?php echo $form->renderFormTag(url_for(array('module' => 'accessobject', 'action' => 'add'))) ?>
	<?php endif; ?>

<table width="80%" cellspacing=0 border="0" cellpadding="0" align="left" summary="">
  <fieldset class="collapsible">
	  <tr>
		<td colspan="3"><?php echo $form->name->renderRow(array('readonly'=>'true')) ?></td>
	  </tr>
	  <tr>
		<td width="40%" ><?php echo $form->classification->renderRow(array('size' => 0,'onchange'=>'toggleOff();'), 'Classification') ?></td>
		<td width="1%">&nbsp;</td>
		<td width="40%"><?php echo $form->refusal->renderRow(array('onchange'=>'toggleOff();')) ?></td>
	  </tr>
	  <tr>
	  	<td><?php echo $form->sensitivity->renderRow(array('size' => 0,'onchange'=>'toggleOff();'), 'Sensitive') ?></td>
		<td>&nbsp;</td>
		<td><?php echo $form->restriction->renderRow() ?></td>
	  </tr>
	  	<tr>
			<td><?php echo $form->publish->renderRow() ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>

	  	</tr>
  </fieldset>
   <tr>
   		<td colspan=3>
			<div class="actions section">

			<h2 class="element-invisible"><?php echo __('Actions') ?></h2>

			<section class="actions">
			  <ul>
				<li><?php echo link_to(__('Cancel'), array($informationObj, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
        		<li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
			  </ul>
			</section>
		</td>
	</tr>
 </div>
</table>

<script type="text/javascript">

function toggleOff()
{
	var str = document.getElementById("classification").value;
	var n = str.indexOf("public"); 

	if (n >= 0)
	{
		var str = document.getElementById("sensitivity").value;
		var n = str.indexOf("yes"); 
		if (n == -1)
		{
			var str = document.getElementById("refusal").value;
			var n = str.indexOf("none"); 
			if (n >= 0)
			{
				document.getElementById("div0").style.display="block";
			}
			else
			{
				var str = document.getElementById("refusal").value;
				var n = str.indexOf("please"); 
				if (n >= 0)
				{
					document.getElementById("div0").style.display="block";
				}
				else
				{
					document.getElementById("div0").style.display="none";
				} 
			} 
		}
		else
		{
			document.getElementById("div0").style.display="none";
			document.getElementById("publish").value = "No";
		} 
	}
	else
	{
		document.getElementById("div0").style.display="none";
		document.getElementById("publish").value = "No";
	} 
}
</script>
