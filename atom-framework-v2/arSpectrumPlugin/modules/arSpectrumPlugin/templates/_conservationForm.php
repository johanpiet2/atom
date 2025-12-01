<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['conservation_reference']->renderLabel(__('Conservation Reference')); ?>
            <?php echo $form['conservation_reference']->render(['class' => 'form-control']); ?>
            <?php echo $form['conservation_reference']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['treatment_date']->renderLabel(__('Treatment Date').' <span class="required">*</span>'); ?>
            <?php echo $form['treatment_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['treatment_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['conservator_name']->renderLabel(__('Conservator Name')); ?>
            <?php echo $form['conservator_name']->render(['class' => 'form-control']); ?>
            <?php echo $form['conservator_name']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['treatment_type']->renderLabel(__('Treatment Type')); ?>
            <?php echo $form['treatment_type']->render(['class' => 'form-control']); ?>
            <?php echo $form['treatment_type']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['condition_before']->renderLabel(__('Condition Before Treatment')); ?>
    <?php echo $form['condition_before']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['condition_before']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['treatment_performed']->renderLabel(__('Treatment Performed')); ?>
    <?php echo $form['treatment_performed']->render(['class' => 'form-control', 'rows' => 6]); ?>
    <?php echo $form['treatment_performed']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['materials_used']->renderLabel(__('Materials Used')); ?>
    <?php echo $form['materials_used']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['materials_used']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['condition_after']->renderLabel(__('Condition After Treatment')); ?>
    <?php echo $form['condition_after']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['condition_after']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['recommendations']->renderLabel(__('Recommendations for Future Care')); ?>
    <?php echo $form['recommendations']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['recommendations']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['conservation_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['conservation_note']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['conservation_note']->renderError(); ?>
</div>
