<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['deaccession_number']->renderLabel(__('Deaccession Number').' <span class="required">*</span>'); ?>
            <?php echo $form['deaccession_number']->render(['class' => 'form-control']); ?>
            <?php echo $form['deaccession_number']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['deaccession_date']->renderLabel(__('Deaccession Date').' <span class="required">*</span>'); ?>
            <?php echo $form['deaccession_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['deaccession_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['authorized_by']->renderLabel(__('Authorized By')); ?>
    <?php echo $form['authorized_by']->render(['class' => 'form-control']); ?>
    <?php echo $form['authorized_by']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['deaccession_reason']->renderLabel(__('Reason for Deaccession')); ?>
    <?php echo $form['deaccession_reason']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['deaccession_reason']->renderError(); ?>
</div>

<fieldset>
    <legend><?php echo __('Disposal Information'); ?></legend>
    
    <div class="form-group">
        <?php echo $form['disposal_method']->renderLabel(__('Disposal Method')); ?>
        <?php echo $form['disposal_method']->render(['class' => 'form-control']); ?>
        <?php echo $form['disposal_method']->renderError(); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form['disposal_recipient']->renderLabel(__('Disposal Recipient')); ?>
        <?php echo $form['disposal_recipient']->render(['class' => 'form-control']); ?>
        <?php echo $form['disposal_recipient']->renderError(); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form['disposal_price']->renderLabel(__('Disposal Price (if sold)')); ?>
        <?php echo $form['disposal_price']->render(['class' => 'form-control', 'step' => '0.01', 'min' => '0']); ?>
        <?php echo $form['disposal_price']->renderError(); ?>
        <small class="form-text text-muted"><?php echo __('Enter amount if item was sold'); ?></small>
    </div>
</fieldset>

<div class="form-group">
    <div class="form-check">
        <?php echo $form['legal_requirements_met']->render(['class' => 'form-check-input']); ?>
        <?php echo $form['legal_requirements_met']->renderLabel(__('All legal requirements have been met'), ['class' => 'form-check-label']); ?>
        <?php echo $form['legal_requirements_met']->renderError(); ?>
    </div>
</div>

<div class="form-group">
    <?php echo $form['deaccession_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['deaccession_note']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['deaccession_note']->renderError(); ?>
</div>
