<?php decorate_with('layout_2col.php'); ?>

<?php slot('sidebar'); ?>
    <?php include_component('informationobject', 'contextMenu'); ?>
<?php end_slot(); ?>

<?php slot('title'); ?>
    <h1><?php echo render_title($resource); ?></h1>
    <h2><?php echo $sf_response->getTitle(); ?></h2>
<?php end_slot(); ?>

<?php slot('content'); ?>

<div class="spectrum-edit-form">
	<form method="post" action="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => $procedureType]); ?>">
        
        <?php echo $form->renderHiddenFields(); ?>
        
        <?php if ($form->hasErrors()) { ?>
            <div class="alert alert-danger">
                <h4><?php echo __('Please correct the following errors:'); ?></h4>
                <ul>
                    <?php foreach ($form->getErrorSchema()->getErrors() as $error) { ?>
                        <li><?php echo $error; ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <fieldset class="mb-4">
            <?php if ('entry' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/entryForm', ['form' => $form]); ?>
            
            <?php } elseif ('acquisition' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/acquisitionForm', ['form' => $form]); ?>
            
            <?php } elseif ('location' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/locationForm', ['form' => $form]); ?>
            
            <?php } elseif ('movement' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/movementForm', ['form' => $form]); ?>
            
            <?php } elseif ('loan_in' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/loanInForm', ['form' => $form]); ?>
            
            <?php } elseif ('loan_out' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/loanOutForm', ['form' => $form]); ?>
            
            <?php } elseif ('condition' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/conditionForm', ['form' => $form]); ?>
            
            <?php } elseif ('conservation' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/conservationForm', ['form' => $form]); ?>
            
            <?php } elseif ('exit' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/exitForm', ['form' => $form]); ?>
            
            <?php } elseif ('deaccession' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/deaccessionForm', ['form' => $form]); ?>
            
            <?php } elseif ('valuation' == $procedureType) { ?>
                <?php include_partial('arSpectrumPlugin/valuationForm', ['form' => $form]); ?>
            
            <?php } ?>

        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo __('Save'); ?>
            </button>
			<a href="<?php echo url_for('spectrum_index', ['slug' => $resource->slug]); ?>" class="btn btn-secondary">
				<i class="fas fa-times"></i> <?php echo __('Cancel'); ?>
			</a>
        </div>

    </form>

</div>

<?php end_slot(); ?>
