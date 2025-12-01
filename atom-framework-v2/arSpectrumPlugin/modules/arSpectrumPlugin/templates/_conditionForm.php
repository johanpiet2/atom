<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['condition_check_reference']->renderLabel(__('Check Reference')); ?>
            <?php echo $form['condition_check_reference']->render(['class' => 'form-control']); ?>
            <?php echo $form['condition_check_reference']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['check_date']->renderLabel(__('Check Date').' <span class="required">*</span>'); ?>
            <?php echo $form['check_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['check_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['checked_by']->renderLabel(__('Checked By')); ?>
            <?php echo $form['checked_by']->render(['class' => 'form-control']); ?>
            <?php echo $form['checked_by']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['check_reason']->renderLabel(__('Reason for Check')); ?>
            <?php echo $form['check_reason']->render(['class' => 'form-control']); ?>
            <?php echo $form['check_reason']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['overall_condition']->renderLabel(__('Overall Condition')); ?>
            <?php echo $form['overall_condition']->render(['class' => 'form-control']); ?>
            <?php echo $form['overall_condition']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['completeness']->renderLabel(__('Completeness')); ?>
            <?php echo $form['completeness']->render(['class' => 'form-control']); ?>
            <?php echo $form['completeness']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['condition_description']->renderLabel(__('Condition Description')); ?>
    <?php echo $form['condition_description']->render(['class' => 'form-control', 'rows' => 5]); ?>
    <?php echo $form['condition_description']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['hazards_noted']->renderLabel(__('Hazards Noted')); ?>
    <?php echo $form['hazards_noted']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['hazards_noted']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['recommendations']->renderLabel(__('Recommendations')); ?>
    <?php echo $form['recommendations']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['recommendations']->renderError(); ?>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['treatment_priority']->renderLabel(__('Treatment Priority')); ?>
            <?php echo $form['treatment_priority']->render(['class' => 'form-control']); ?>
            <?php echo $form['treatment_priority']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['next_check_date']->renderLabel(__('Next Check Date')); ?>
            <?php echo $form['next_check_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['next_check_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['condition_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['condition_note']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['condition_note']->renderError(); ?>
</div>
