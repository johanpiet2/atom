<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['loan_out_number']->renderLabel(__('Loan Out Number').' <span class="required">*</span>'); ?>
            <?php echo $form['loan_out_number']->render(['class' => 'form-control']); ?>
            <?php echo $form['loan_out_number']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['loan_out_date']->renderLabel(__('Loan Out Date').' <span class="required">*</span>'); ?>
            <?php echo $form['loan_out_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['loan_out_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['borrower_name']->renderLabel(__('Borrower Name')); ?>
    <?php echo $form['borrower_name']->render(['class' => 'form-control']); ?>
    <?php echo $form['borrower_name']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['borrower_contact']->renderLabel(__('Borrower Contact')); ?>
    <?php echo $form['borrower_contact']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['borrower_contact']->renderError(); ?>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['loan_start_date']->renderLabel(__('Loan Start Date')); ?>
            <?php echo $form['loan_start_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['loan_start_date']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['loan_end_date']->renderLabel(__('Loan End Date')); ?>
            <?php echo $form['loan_end_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['loan_end_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['loan_purpose']->renderLabel(__('Purpose of Loan')); ?>
    <?php echo $form['loan_purpose']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['loan_purpose']->renderError(); ?>
</div>

<fieldset>
    <legend><?php echo __('Insurance Information'); ?></legend>
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <?php echo $form['insurance_value']->renderLabel(__('Insurance Value')); ?>
                <?php echo $form['insurance_value']->render(['class' => 'form-control', 'step' => '0.01', 'min' => '0']); ?>
                <?php echo $form['insurance_value']->renderError(); ?>
                <small class="form-text text-muted"><?php echo __('Enter amount without currency symbol'); ?></small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <?php echo $form['insurance_currency']->renderLabel(__('Currency')); ?>
                <?php echo $form['insurance_currency']->render(['class' => 'form-control']); ?>
                <?php echo $form['insurance_currency']->renderError(); ?>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <?php echo $form['insurance_policy']->renderLabel(__('Insurance Policy Number')); ?>
        <?php echo $form['insurance_policy']->render(['class' => 'form-control']); ?>
        <?php echo $form['insurance_policy']->renderError(); ?>
    </div>
</fieldset>

<div class="form-group">
    <?php echo $form['loan_conditions']->renderLabel(__('Conditions of Loan')); ?>
    <?php echo $form['loan_conditions']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['loan_conditions']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['loan_out_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['loan_out_note']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['loan_out_note']->renderError(); ?>
</div>
