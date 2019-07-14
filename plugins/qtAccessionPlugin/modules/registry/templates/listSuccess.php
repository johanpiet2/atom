<?php decorate_with('layout_1col') ?>

<?php slot('title') ?>
  <h1><?php echo __('List Register') ?></h1>
<?php end_slot() ?>

<?php slot('before-content') ?>
  <div class="nav">
    <div class="search">
      <form action="<?php echo url_for(array('module' => 'registry', 'action' => 'list')) ?>">
        <input name="subquery" value="<?php echo esc_entities($sf_request->subquery) ?>"/>
        <input class="form-submit" type="submit" value="<?php echo __('Search Registry') ?>"/>
      </form>
    </div>
  </div>
<?php end_slot() ?>

<?php slot('content') ?>

  <table class="table table-bordered sticky-enabled">
    <thead>
      <tr>
        <th>
          <?php echo __('Name') ?>
        </th>
      </tr>
    </thead><tbody>
      <?php foreach ($registry as $item): ?>
        <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
          <td>
            <?php echo link_to($item->id, array($item, 'module' => 'registry')) ?>
          </td>
          <td>
            <?php echo link_to(render_title($item), array($item, 'module' => 'registry')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php end_slot() ?>

<?php slot('after-content') ?>
  <?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>
