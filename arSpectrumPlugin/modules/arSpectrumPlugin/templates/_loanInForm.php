<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['loan_in_number']->renderLabel(__('Loan In Number').' <span class="required">*</span>'); ?>
            <?php echo $form['loan_in_number']->render(['class' => 'form-control']); ?>
            <?php echo $form['loan_in_number']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['loan_in_date']->renderLabel(__('Loan In Date').' <span class="required">*</span>'); ?>
            <?php echo $form['loan_in_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['loan_in_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['lender_name']->renderLabel(__('Lender Name')); ?>
    <?php echo $form['lender_name']->render(['class' => 'form-control']); ?>
    <?php echo $form['lender_name']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['lender_contact']->renderLabel(__('Lender Contact')); ?>
    <?php echo $form['lender_contact']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['lender_contact']->renderError(); ?>
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

<div class="form-group">
    <?php echo $form['loan_conditions']->renderLabel(__('Conditions of Loan')); ?>
    <?php echo $form['loan_conditions']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['loan_conditions']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['loan_in_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['loan_in_note']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['loan_in_note']->renderError(); ?>
</div>
