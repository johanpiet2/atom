<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['movement_reference']->renderLabel(__('Movement Reference')); ?>
            <?php echo $form['movement_reference']->render(['class' => 'form-control']); ?>
            <?php echo $form['movement_reference']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['movement_date']->renderLabel(__('Movement Date').' <span class="required">*</span>'); ?>
            <?php echo $form['movement_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['movement_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['from_location_id']->renderLabel(__('From Location')); ?>
            <?php echo $form['from_location_id']->render(['class' => 'form-control']); ?>
            <?php echo $form['from_location_id']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['to_location_id']->renderLabel(__('To Location').' <span class="required">*</span>'); ?>
            <?php echo $form['to_location_id']->render(['class' => 'form-control']); ?>
            <?php echo $form['to_location_id']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['movement_reason']->renderLabel(__('Reason for Movement')); ?>
            <?php echo $form['movement_reason']->render(['class' => 'form-control']); ?>
            <?php echo $form['movement_reason']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['movement_method']->renderLabel(__('Transport Method')); ?>
            <?php echo $form['movement_method']->render(['class' => 'form-control']); ?>
            <?php echo $form['movement_method']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['moved_by']->renderLabel(__('Moved By')); ?>
    <?php echo $form['moved_by']->render(['class' => 'form-control']); ?>
    <?php echo $form['moved_by']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['movement_note']->renderLabel(__('Movement Notes')); ?>
    <?php echo $form['movement_note']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['movement_note']->renderError(); ?>
</div>
