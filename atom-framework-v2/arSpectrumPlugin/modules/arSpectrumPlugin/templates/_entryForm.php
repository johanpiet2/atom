<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['entry_number']->renderLabel(__('Entry Number').' <span class="required">*</span>'); ?>
            <?php echo $form['entry_number']->render(['class' => 'form-control']); ?>
            <?php echo $form['entry_number']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['entry_date']->renderLabel(__('Entry Date').' <span class="required">*</span>'); ?>
            <?php echo $form['entry_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['entry_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['entry_method']->renderLabel(__('Entry Method')); ?>
            <?php echo $form['entry_method']->render(['class' => 'form-control']); ?>
            <?php echo $form['entry_method']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['received_by']->renderLabel(__('Received By')); ?>
            <?php echo $form['received_by']->render(['class' => 'form-control']); ?>
            <?php echo $form['received_by']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['entry_reason']->renderLabel(__('Reason for Entry')); ?>
    <?php echo $form['entry_reason']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['entry_reason']->renderError(); ?>
</div>

<fieldset>
    <legend><?php echo __('Depositor Information'); ?></legend>
    
    <div class="form-group">
        <?php echo $form['depositor_name']->renderLabel(__('Depositor Name')); ?>
        <?php echo $form['depositor_name']->render(['class' => 'form-control']); ?>
        <?php echo $form['depositor_name']->renderError(); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form['depositor_contact']->renderLabel(__('Depositor Contact Details')); ?>
        <?php echo $form['depositor_contact']->render(['class' => 'form-control', 'rows' => 3]); ?>
        <?php echo $form['depositor_contact']->renderError(); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form['current_owner']->renderLabel(__('Current Owner')); ?>
        <?php echo $form['current_owner']->render(['class' => 'form-control']); ?>
        <?php echo $form['current_owner']->renderError(); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form['return_date']->renderLabel(__('Expected Return Date')); ?>
        <?php echo $form['return_date']->render(['class' => 'form-control']); ?>
        <?php echo $form['return_date']->renderError(); ?>
    </div>
</fieldset>

<div class="form-group">
    <?php echo $form['entry_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['entry_note']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['entry_note']->renderError(); ?>
</div>
