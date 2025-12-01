<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['acquisition_number']->renderLabel(__('Acquisition Number').' <span class="required">*</span>'); ?>
            <?php echo $form['acquisition_number']->render(['class' => 'form-control']); ?>
            <?php echo $form['acquisition_number']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['acquisition_date']->renderLabel(__('Acquisition Date')); ?>
            <?php echo $form['acquisition_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['acquisition_date']->renderError(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['acquisition_method']->renderLabel(__('Acquisition Method')); ?>
            <?php echo $form['acquisition_method']->render(['class' => 'form-control']); ?>
            <?php echo $form['acquisition_method']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['accession_number']->renderLabel(__('Accession Number')); ?>
            <?php echo $form['accession_number']->render(['class' => 'form-control']); ?>
            <?php echo $form['accession_number']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['acquisition_source']->renderLabel(__('Acquisition Source')); ?>
    <?php echo $form['acquisition_source']->render(['class' => 'form-control']); ?>
    <?php echo $form['acquisition_source']->renderError(); ?>
</div>

<div class="form-group">
    <?php echo $form['acquisition_reason']->renderLabel(__('Reason for Acquisition')); ?>
    <?php echo $form['acquisition_reason']->render(['class' => 'form-control', 'rows' => 3]); ?>
    <?php echo $form['acquisition_reason']->renderError(); ?>
</div>

<fieldset>
    <legend><?php echo __('Financial Information'); ?></legend>
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <?php echo $form['purchase_price']->renderLabel(__('Purchase Price')); ?>
                <?php echo $form['purchase_price']->render(['class' => 'form-control', 'step' => '0.01', 'min' => '0']); ?>
                <?php echo $form['purchase_price']->renderError(); ?>
                <small class="form-text text-muted"><?php echo __('Enter amount without currency symbol'); ?></small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <?php echo $form['price_currency']->renderLabel(__('Currency')); ?>
                <?php echo $form['price_currency']->render(['class' => 'form-control']); ?>
                <?php echo $form['price_currency']->renderError(); ?>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo __('Provenance & Legal'); ?></legend>
    
    <div class="form-group">
        <?php echo $form['provenance_note']->renderLabel(__('Provenance')); ?>
        <?php echo $form['provenance_note']->render(['class' => 'form-control', 'rows' => 3]); ?>
        <?php echo $form['provenance_note']->renderError(); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form['legal_title']->renderLabel(__('Legal Title/Transfer')); ?>
        <?php echo $form['legal_title']->render(['class' => 'form-control', 'rows' => 2]); ?>
        <?php echo $form['legal_title']->renderError(); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form['conditions_of_acquisition']->renderLabel(__('Conditions of Acquisition')); ?>
        <?php echo $form['conditions_of_acquisition']->render(['class' => 'form-control', 'rows' => 3]); ?>
        <?php echo $form['conditions_of_acquisition']->renderError(); ?>
    </div>
</fieldset>

<div class="form-group">
    <?php echo $form['acquisition_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['acquisition_note']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['acquisition_note']->renderError(); ?>
</div>