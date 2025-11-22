<head>
</head>
<h1><?php echo __('Publish Access'); ?></h1>
<h1><?php echo __('Publish Access %1%', ['%1%' => sfConfig::get('app_ui_label_accessobject')]); ?></h1>

<?php echo get_partial('default/pager', ['pager' => $pager]); ?>

<?php decorate_with('layout_1col.php'); ?>

<?php if ($sf_user->hasFlash('error')) { ?>
  <div class="messages error">
    <h3><?php echo __('Error encountered'); ?></h3>
    <div><?php echo $sf_user->getFlash('error'); ?></div>
  </div>
<?php } ?>

<?php slot('content'); ?>

	<?php echo $form->renderFormTag(url_for(['module' => 'reports', 'action' => 'browseUnPublish']), ['method' => 'post']); ?>

    <?php echo $form->renderHiddenFields(); ?>
    
<table class="table table-bordered sticky-enabled">
  <thead>
    <tr>
      <th class="sortable">
        <?php echo link_to(__('Name'), ['sort' => ('nameUp' == $sf_request->sort) ? 'nameDown' : 'nameUp'] + $sf_data->getRaw('sf_request')->getParameterHolder()->getAll(), ['title' => __('Sort'), 'class' => 'sortable']); ?>
        <?php if ('nameUp' == $sf_request->sort) { ?>
          <?php echo image_tag('up.gif'); ?>
        <?php } elseif ('nameDown' == $sf_request->sort) { ?>
          <?php echo image_tag('down.gif'); ?>
        <?php } ?>
      </th><th>
        <?php echo __('Publish'); ?>
      </th><th>
        <?php echo __('Restriction Condition'); ?>
      </th><th>
        <?php echo __('Refusal'); ?>
      </th><th>
        <?php echo __('Sensitivity'); ?>
      </th><th>
        <?php echo __('Classification'); ?>
      </th><th>
        <?php echo __('Restriction'); ?>
      </th>
    </tr>
  </thead><tbody>
    <?php foreach ($pager->getResults() as $item) { ?>
      <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd'; ?>">
        <td>
        	<?php $d = "id_{$row}"; ?>
        	<input type="hidden" name="<?php echo $d; ?>" id="<?php echo $d; ?>" value="<?php echo $item->id; ?>"/>
         <?php echo link_to(render_title($item), [$item, 'module' => 'accessobject']); ?>
        </td><td>
			<?php if ('Please Select' == $item->publish) { ?>
		      <?php echo '-'; ?>
		    <?php } else { ?>
		      <?php echo $item->publish; ?>
		    <?php } ?>	    
        </td><td>
			<?php if ('Please Select' == $item->restriction_condition) { ?>
		      <?php echo '-'; ?>
		    <?php } else { ?>
		      <?php echo $item->restriction_condition; ?>
		    <?php } ?>
        </td><td>
			<?php if ('Please Select' == $item->refusal) { ?>
		      <?php echo '-'; ?>
		    <?php } else { ?>
		      <?php echo $item->refusal; ?>
		    <?php } ?>
 		</td><td>
			<?php if ('Please Select' == $item->sensitivity) { ?>
		      <?php echo '-'; ?>
		    <?php } else { ?>
		      <?php echo $item->sensitivity; ?>
		    <?php } ?>
        </td><td>
			<?php if ('Please Select' == $item->classification) { ?>
		      <?php echo '-'; ?>
		    <?php } else { ?>
		      <?php echo $item->classification; ?>
		    <?php } ?>
        </td><td>
			<?php if ('Please Select' == $item->restriction) { ?>
		      <?php echo '-'; ?>
		    <?php } else { ?>
		      <?php echo $item->restriction; ?>
		    <?php } ?>
        </td>
		</tr>
    <?php } ?>
   
  </tbody>
</table>
 <iframe name="inlineframe" src="" frameborder="0" scrolling="auto" width="0" height="0" marginwidth="0" marginheight="0" ></iframe>
<section class="actions">
	<ul class="clearfix links">
		<li><?php echo link_to(__('Return'), [$resource, 'module' => 'informationobject', 'action' => 'browse'], ['class' => 'c-btn']); ?></li>
	  	<li><input class="form-submit c-btn c-btn-submit" type="submit" value="<?php echo __('Unpublish'); ?>"/></li>
	</ul>
</section>
<?php end_slot(); ?>
</form>
