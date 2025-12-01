<div class="form-group">
    <?php echo $form['location_name']->renderLabel(__('Location Name').' <span class="required">*</span>'); ?>
    <?php echo $form['location_name']->render(['class' => 'form-control']); ?>
    <?php echo $form['location_name']->renderError(); ?>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <?php echo $form['location_building']->renderLabel(__('Building')); ?>
            <?php echo $form['location_building']->render(['class' => 'form-control']); ?>
            <?php echo $form['location_building']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            <?php echo $form['location_floor']->renderLabel(__('Floor')); ?>
            <?php echo $form['location_floor']->render(['class' => 'form-control']); ?>
            <?php echo $form['location_floor']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            <?php echo $form['location_room']->renderLabel(__('Room')); ?>
            <?php echo $form['location_room']->render(['class' => 'form-control']); ?>
            <?php echo $form['location_room']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['location_unit']->renderLabel(__('Storage Unit/Cabinet')); ?>
            <?php echo $form['location_unit']->render(['class' => 'form-control']); ?>
            <?php echo $form['location_unit']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['location_coordinates']->renderLabel(__('Coordinates/GPS')); ?>
            <?php echo $form['location_coordinates']->render(['class' => 'form-control']); ?>
            <?php echo $form['location_coordinates']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['location_type']->renderLabel(__('Location Type')); ?>
    <?php echo $form['location_type']->render(['class' => 'form-control']); ?>
    <?php echo $form['location_type']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['security_level']->renderLabel(__('Security Level')); ?>
    <?php echo $form['security_level']->render(['class' => 'form-control']); ?>
    <?php echo $form['security_level']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['location_note']->renderLabel(__('Location Notes')); ?>
    <?php echo $form['location_note']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['location_note']->renderError(); ?>
</div>
