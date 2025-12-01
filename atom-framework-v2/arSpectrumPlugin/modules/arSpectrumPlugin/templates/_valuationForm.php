<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['valuation_date']->renderLabel(__('Valuation Date').' <span class="required">*</span>'); ?>
            <?php echo $form['valuation_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['valuation_date']->renderError(); ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['valuation_reference']->renderLabel(__('Reference Number')); ?>
            <?php echo $form['valuation_reference']->render(['class' => 'form-control']); ?>
            <?php echo $form['valuation_reference']->renderError(); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['valuation_type']->renderLabel(__('Valuation Type')); ?>
    <?php echo $form['valuation_type']->render(['class' => 'form-control']); ?>
    <?php echo $form['valuation_type']->renderError(); ?>
</div>

<fieldset>
    <legend><?php echo __('Valuation Amount'); ?></legend>
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <?php echo $form['valuation_amount']->renderLabel(__('Amount').' <span class="required">*</span>'); ?>
                <?php echo $form['valuation_amount']->render(['class' => 'form-control', 'step' => '0.01', 'min' => '0']); ?>
                <?php echo $form['valuation_amount']->renderError(); ?>
                <small class="form-text text-muted"><?php echo __('Enter amount without currency symbol'); ?></small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <?php echo $form['valuation_currency']->renderLabel(__('Currency')); ?>
                <?php echo $form['valuation_currency']->render(['class' => 'form-control']); ?>
                <?php echo $form['valuation_currency']->renderError(); ?>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo __('Valuer Information'); ?></legend>
    
    <div class="form-group">
        <?php echo $form['valuer_name']->renderLabel(__('Valuer Name')); ?>
        <?php echo $form['valuer_name']->render(['class' => 'form-control']); ?>
        <?php echo $form['valuer_name']->renderError(); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form['valuer_organization']->renderLabel(__('Valuer Organization')); ?>
        <?php echo $form['valuer_organization']->render(['class' => 'form-control']); ?>
        <?php echo $form['valuer_organization']->renderError(); ?>
    </div>
</fieldset>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo $form['renewal_date']->renderLabel(__('Renewal Date')); ?>
            <?php echo $form['renewal_date']->render(['class' => 'form-control']); ?>
            <?php echo $form['renewal_date']->renderError(); ?>
            <small class="form-text text-muted"><?php echo __('When this valuation should be renewed'); ?></small>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <label>&nbsp;</label>
            <div class="form-check">
                <?php echo $form['is_current']->render(['class' => 'form-check-input']); ?>
                <?php echo $form['is_current']->renderLabel(__('This is the current valuation'), ['class' => 'form-check-label']); ?>
                <?php echo $form['is_current']->renderError(); ?>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <?php echo $form['valuation_note']->renderLabel(__('Additional Notes')); ?>
    <?php echo $form['valuation_note']->render(['class' => 'form-control', 'rows' => 4]); ?>
    <?php echo $form['valuation_note']->renderError(); ?>
</div>
