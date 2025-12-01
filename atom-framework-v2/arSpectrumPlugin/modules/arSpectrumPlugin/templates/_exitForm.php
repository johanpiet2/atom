<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['exit_number']->renderLabel(__('Exit Number').' <span class="required">*</span>'); ?>
            <?php echo $form['exit_number']->render(['class' => 'form-control']); ?>
            <?php echo $form['exit_number']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['exit_date']->renderLabel(__('Exit Date').' <span class="required">*</span>'); ?>
            <?php echo $form['exit_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['exit_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['exit_reason']->renderLabel(__('Reason for Exit')); ?>
            <?php echo $form['exit_reason']->render(['class' => 'form-control']); ?>
            <?php echo $form['exit_reason']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['exit_destination']->renderLabel(__('Destination')); ?>
            <?php echo $form['exit_destination']->render(['class' => 'form-control']); ?>
            <?php echo $form['exit_destination']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['recipient_name']->renderLabel(__('Recipient Name')); ?>
    <?php echo $form['recipient_name']->render(['class' => 'form-control']); ?>
    <?php echo $form['recipient_name']->renderError(); ?>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['authorization_name']->renderLabel(__('Authorized By')); ?>
            <?php echo $form['authorization_name']->render(['class' => 'form-control']); ?>
            <?php echo $form['authorization_name']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['authorization_date']->renderLabel(__('Authorization Date')); ?>
            <?php echo $form['authorization_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['authorization_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['exit_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['exit_note']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['exit_note']->renderError(); ?>
</div>
