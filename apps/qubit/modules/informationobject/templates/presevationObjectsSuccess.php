<h1><?php echo __('Presevation storage') ?></h1>

<h1 class="label"><?php echo render_title($resource) ?></h1>

<table class="sticky-enabled">
  <thead>
    <tr>
      <th>
        <?php echo __('Name') ?>
      </th><th>
        <?php echo __('Condition') ?>
      </th><th>   
        <?php echo __('Usability') ?>
      </th><th>
        <?php echo __('Preservation Measure') ?>
      </th><th>
        <?php echo __('Medium') ?>
      </th><th>
        <?php echo __('Availability') ?>
      </th><th>
	  	<?php echo __('Hard') ?>
      </th><th>
	  	<?php echo __('Digital') ?>
      </th><th>
        <?php echo __('Grounds of refusal') ?>
      </th><th>
        <?php echo __('Restoration Intervation') ?>
      </th><th>
        <?php echo __('Conservation Intervation') ?>
      </th><th>
        <?php echo __('Type') ?>
      </th><th>
        <?php echo __('Sensitivity') ?>
      </th><th>
        <?php echo __('Publish') ?>
      </th><th>
        <?php echo __('Classification') ?>
      </th><th>
        <?php echo __('Restriction') ?>
      </th>    
      	  
    </tr>
  </thead> 
  <tbody>
    <?php foreach ($presevationObjects as $item): ?>
      <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
          <?php echo link_to(render_title($item), array($item, 'module' => 'presevationobject')) ?>
        </td><td>
          <?php echo render_value($item->condition) ?>
        </td><td>
          <?php echo render_value($item->usability) ?>
        </td><td>
          <?php echo render_value($item->measure) ?>
        </td><td>
          <?php echo render_value($item->medium) ?>
        </td><td>
          <?php echo render_value($item->availability) ?>
        </td><td>
			<?php echo render_value($item->hard) ?>
        </td><td>
			<?php echo render_value($item->digital) ?>
        </td><td>
          <?php echo render_value($item->refusal) ?>
        </td><td>
          <?php echo render_value($item->restoration) ?>
        </td><td>
          <?php echo render_value($item->conservation) ?>
        </td><td>
			<?php echo render_value($item->type) ?>
        </td><td>
          <?php echo render_value($item->sensitivity) ?>
        </td><td>
          <?php echo render_value($item->publish) ?>
        </td><td>
          <?php echo render_value($item->classification) ?>
        </td><td>
          <?php echo render_value($item->restriction) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  <tbody>
</table>

<?php echo get_partial('default/pager', array('pager' => $pager)) ?>
