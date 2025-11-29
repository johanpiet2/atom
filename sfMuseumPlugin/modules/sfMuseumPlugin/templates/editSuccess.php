<?php decorate_with('layout_2col.php'); ?>
<?php use_helper('Date'); ?>

<?php slot('sidebar'); ?>
    <?php include_component('repository', 'contextMenu'); ?>
<?php end_slot(); ?>

<?php slot('title'); ?>
    <h1><?php echo isset($resource) && $resource->id ? __('Edit museum object') : __('Add new museum object'); ?></h1>
<?php end_slot(); ?>

<?php slot('content'); ?>

    <?php echo $form->renderGlobalErrors(); ?>

    <?php if (isset($resource) && $resource->id) { ?>
        <?php echo $form->renderFormTag(url_for([$resource, 'module' => 'sfMuseumPlugin', 'action' => 'edit']), ['id' => 'editForm']); ?>
    <?php } else { ?>
        <?php echo $form->renderFormTag(url_for(['module' => 'sfMuseumPlugin', 'action' => 'add']), ['id' => 'editForm']); ?>
    <?php } ?>

    <?php echo $form->renderHiddenFields(); ?>

    <div id="content">

        <!-- Identity Area -->
        <fieldset class="collapsible" id="identityArea">
            <legend><?php echo __('Identity area'); ?></legend>

            <?php echo render_field($form->identifier
                ->label(__('Identifier').' <span class="form-required" title="'.__('This is a mandatory element.').'">*</span>')
                ->help(__('Unique identifier for this object')), $resource); ?>

            <?php echo get_partial('sfMuseumPlugin/identifierOptions', ['mask' => $mask]); ?>
            <?php echo get_partial('sfMuseumPlugin/alternativeIdentifiers', $sf_data->getRaw('alternativeIdentifiersComponent')->getVarHolder()->getAll()); ?>

            <?php echo render_field($form->title
                ->label(__('Title').' <span class="form-required" title="'.__('This is a mandatory element.').'">*</span>')
                ->help(__('Title of the object')), $resource); ?>

            <?php // echo get_partial('event', $sf_data->getRaw('eventComponent')->getVarHolder()->getAll() + ['help' => __('Record the date(s) of the unit of description. The Date display field can be used to enter free-text date information.')]);?>

            <?php echo $form->levelOfDescription
                ->label(__('Level of description'))
                ->help(__('Level of archival description'))
                ->renderRow(); ?>

            <?php echo get_partial('sfMuseumPlugin/childLevels', ['help' => __('Add child levels to create hierarchical descriptions.')]); ?>

            <?php echo render_field($form->extentAndMedium
                ->label(__('Extent and medium'))
                ->help(__('Physical extent and medium of the object')), $resource, ['class' => 'resizable']); ?>

        </fieldset>

        <!-- Museum Object Specific Fields -->
        <fieldset class="collapsible" id="museumObjectArea">
            <legend><?php echo __('Museum object identification (CCO)'); ?></legend>

            <?php echo render_field($form->museum_work_type
                ->label(__('Work type'))
                ->help(__('Select the CCO work type that best describes this object')), null); ?>

            <?php echo render_field($form->museum_creation_date_earliest
                ->label(__('Creation date (earliest)'))
                ->help(__('Earliest possible creation date')), null); ?>

            <?php echo render_field($form->museum_creation_date_latest
                ->label(__('Creation date (latest)'))
                ->help(__('Latest possible creation date')), null); ?>

            <?php echo render_field($form->museum_object_type
                ->label(__('Object type'))
                ->help(__('Type of object')), null); ?>

            <?php echo render_field($form->museum_classification
                ->label(__('Classification'))
                ->help(__('Classification')), null); ?>

            <?php echo render_field($form->museum_dimensions
                ->label(__('Dimensions'))
                ->help(__('Dimensions')), null); ?>

            <?php echo render_field($form->museum_current_location
                ->label(__('Current location'))
                ->help(__('Current location')), null, ['class' => 'resizable']); ?>
        </fieldset>

        <!-- Materials and Techniques -->
        <fieldset class="collapsible" id="materialsArea">
            <legend><?php echo __('Materials and techniques'); ?></legend>

            <div class="form-item">
                <?php echo $form->museum_materials->renderLabel(__('Materials')); ?>
                <?php echo $form->museum_materials->render(['size' => 5, 'class' => 'form-control']); ?>
                <div class="description"><?php echo __('Hold Ctrl/Cmd to select multiple materials'); ?></div>
            </div>

            <div class="form-item">
                <?php echo $form->museum_techniques->renderLabel(__('Techniques')); ?>
                <?php echo $form->museum_techniques->render(['size' => 5, 'class' => 'form-control']); ?>
                <div class="description"><?php echo __('Hold Ctrl/Cmd to select multiple techniques'); ?></div>
            </div>

            <?php echo render_field($form->museum_measurements
                ->label(__('Measurements'))
                ->help(__('Physical measurements')), null, ['class' => 'resizable']); ?>
        </fieldset>

		<fieldset class="collapsible collapsed" id="ccoCreatorArea">
		  <legend><?php echo __('Creator information (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_creator_identity->label(__('Creator/Maker')), null); ?>
		  <?php echo render_field($form->museum_creator_role->label(__('Role')), null); ?>
		  <?php echo render_field($form->museum_creator_extent->label(__('Extent')), null); ?>
		  <?php echo render_field($form->museum_creator_qualifier->label(__('Qualifier')), null); ?>
		  <?php echo render_field($form->museum_creator_attribution->label(__('Attribution')), null, ['class' => 'resizable']); ?>
		</fieldset>
	
		
		<fieldset class="collapsible collapsed" id="ccoCreationDateArea">
		  <legend><?php echo __('Creation date (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_creation_date_display->label(__('Display date')), null); ?>
		  <?php echo render_field($form->museum_creation_date_qualifier->label(__('Date qualifier')), null); ?>
		</fieldset>

		<fieldset class="collapsible collapsed" id="ccoStylePeriodArea">
		  <legend><?php echo __('Styles, periods, groups, movements (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_style->label(__('Style')), null); ?>
		  <?php echo render_field($form->museum_period->label(__('Period')), null); ?>
		  <?php echo render_field($form->museum_cultural_group->label(__('Culture/Group')), null); ?>
		  <?php echo render_field($form->museum_movement->label(__('Movement')), null); ?>
		  <?php echo render_field($form->museum_school->label(__('School')), null); ?>
		  <?php echo render_field($form->museum_dynasty->label(__('Dynasty')), null); ?>
		</fieldset>

		<fieldset class="collapsible collapsed" id="ccoSubjectArea">
		  <legend><?php echo __('Subject matter (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_subject_indexing_type->label(__('Indexing type')), null); ?>
		  <?php echo render_field($form->museum_subject_display->label(__('Subject')), null, ['class' => 'resizable']); ?>
		  <?php echo render_field($form->museum_subject_extent->label(__('Extent')), null); ?>
		</fieldset>

		<fieldset class="collapsible collapsed" id="ccoContextArea">
		  <legend><?php echo __('Context (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_historical_context->label(__('Historical')), null, ['class' => 'resizable']); ?>
		  <?php echo render_field($form->museum_cultural_context->label(__('Cultural')), null); ?>
		  <?php echo render_field($form->museum_architectural_context->label(__('Architectural')), null); ?>
		  <?php echo render_field($form->museum_archaeological_context->label(__('Archaeological')), null); ?>
		</fieldset>

		<fieldset class="collapsible collapsed" id="ccoClassArea">
		  <legend><?php echo __('Classification (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_object_class->label(__('Class')), null); ?>
		  <?php echo render_field($form->museum_object_category->label(__('Category')), null); ?>
		  <?php echo render_field($form->museum_object_sub_category->label(__('Sub-category')), null); ?>
		</fieldset>

		<fieldset class="collapsible collapsed" id="ccoEditionArea">
		  <legend><?php echo __('Edition (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_edition_number->label(__('Number')), null); ?>
		  <?php echo render_field($form->museum_edition_size->label(__('Size')), null); ?>
		  <?php echo render_field($form->museum_edition_description->label(__('Description')), null, ['class' => 'resizable']); ?>
		  <?php echo render_field($form->museum_state_description->label(__('State description')), null); ?>
		  <?php echo render_field($form->museum_state_identification->label(__('State identification')), null); ?>
		</fieldset>

		<fieldset class="collapsible collapsed" id="ccoTechniqueArea">
		  <legend><?php echo __('Facture/Technique (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_facture_description->label(__('Facture')), null, ['class' => 'resizable']); ?>
		  <?php echo render_field($form->museum_technique_cco->label(__('Technique')), null); ?>
		  <?php echo render_field($form->museum_technique_qualifier->label(__('Qualifier')), null); ?>
		</fieldset>

		<fieldset class="collapsible collapsed" id="ccoPhysicalArea">
		  <legend><?php echo __('Physical description (CCO)'); ?></legend>
		  <?php echo render_field($form->museum_physical_appearance->label(__('Appearance')), null, ['class' => 'resizable']); ?>
		  <?php echo render_field($form->museum_color->label(__('Color')), null); ?>
		  <?php echo render_field($form->museum_shape->label(__('Shape')), null); ?>
		  <?php echo render_field($form->museum_orientation->label(__('Orientation')), null); ?>
		</fieldset>

        <!-- Condition and Provenance -->
        <fieldset class="collapsible collapsed" id="conditionArea">
            <legend><?php echo __('Condition and provenance'); ?></legend>

            <?php echo render_field($form->museum_inscription
                ->label(__('Inscription'))
                ->help(__('Any inscriptions, marks, or signatures')), null, ['class' => 'resizable']); ?>

            <?php echo render_field($form->museum_condition_notes
                ->label(__('Condition notes'))
                ->help(__('Current condition of the object')), null, ['class' => 'resizable']); ?>

            <?php echo render_field($form->museum_provenance
                ->label(__('Provenance'))
                ->help(__('History of ownership')), null, ['class' => 'resizable']); ?>

            <?php echo render_field($form->museum_style_period
                ->label(__('Style or period'))
                ->help(__('Artistic style or historical period')), null); ?>

            <?php echo render_field($form->museum_cultural_context
                ->label(__('Cultural context'))
                ->help(__('Cultural or geographic origin')), null); ?>
        </fieldset>

        <!-- Context Area -->
        <fieldset class="collapsible collapsed" id="contextArea">
            <legend><?php echo __('Context area'); ?></legend>

            <div class="form-item">
                <?php echo $form->creators
                    ->label(__('Name of creator(s)').' <span class="form-required" title="'.__('This archival description requires at least one creator.').'">*</span>')
                    ->renderLabel(); ?>
                <?php echo $form->creators->render(['class' => 'form-autocomplete']); ?>
                <?php echo $form->creators
                    ->help(__('Record the name of the organization(s) or the individual(s) responsible for the creation.'))
                    ->renderHelp(); ?>
                <input class="add" type="hidden" data-link-existing="true" value="<?php echo url_for(['module' => 'actor', 'action' => 'add']); ?> #authorizedFormOfName"/>
                <input class="list" type="hidden" value="<?php echo url_for(['module' => 'actor', 'action' => 'autocomplete']); ?>"/>
            </div>

            <div class="form-item">
                <?php echo $form->repository->renderLabel(); ?>
                <?php echo $form->repository->render(); ?>
                <input class="add" type="hidden" data-link-existing="true" value="<?php echo url_for(['module' => 'repository', 'action' => 'add']); ?> #authorizedFormOfName"/>
                <input class="list" type="hidden" value="<?php echo url_for($sf_data->getRaw('repoAcParams')); ?>"/>
                <?php echo $form->repository
                    ->help(__('Record the name of the organization which has custody of the archival material.'))
                    ->renderHelp(); ?>
            </div>

            <?php echo render_field($form->archivalHistory
                ->label(__('Archival history'))
                ->help(__('History of custody and ownership')), $resource, ['class' => 'resizable']); ?>

            <?php echo render_field($form->acquisition
                ->label(__('Immediate source of acquisition'))
                ->help(__('Source from which the object was acquired')), $resource, ['class' => 'resizable']); ?>
        </fieldset>

        <!-- Content and Structure Area -->
        <fieldset class="collapsible collapsed" id="contentArea">
            <legend><?php echo __('Content and structure area'); ?></legend>

            <?php echo render_field($form->scopeAndContent
                ->label(__('Scope and content'))
                ->help(__('Summary of the scope and content of the object')), $resource, ['class' => 'resizable']); ?>

            <?php echo render_field($form->accruals
                ->label(__('Accruals')), $resource, ['class' => 'resizable']); ?>

            <?php echo render_field($form->arrangement
                ->label(__('System of arrangement')), $resource, ['class' => 'resizable']); ?>
        </fieldset>

        <!-- Conditions of Access and Use -->
        <fieldset class="collapsible collapsed" id="conditionsArea">
            <legend><?php echo __('Conditions of access and use area'); ?></legend>

            <?php echo render_field($form->accessConditions
                ->label(__('Conditions governing access')), $resource, ['class' => 'resizable']); ?>

            <?php echo render_field($form->reproductionConditions
                ->label(__('Conditions governing reproduction')), $resource, ['class' => 'resizable']); ?>

            <?php echo $form->language
                ->label(__('Language of material'))
                ->renderRow(['class' => 'form-autocomplete']); ?>

            <?php echo $form->script
                ->label(__('Script of material'))
                ->renderRow(['class' => 'form-autocomplete']); ?>

            <?php // echo render_field($form->languageNotes
                // ->label(__('Language and script notes')), $isad, ['class' => 'resizable']);?>

            <?php echo render_field($form->physicalCharacteristics
                ->label(__('Physical characteristics and technical requirements')), $resource, ['class' => 'resizable']); ?>

            <?php echo render_field($form->findingAids
                ->label(__('Finding aids')), $resource, ['class' => 'resizable']); ?>
        </fieldset>

        <!-- Allied Materials Area -->
        <fieldset class="collapsible collapsed" id="alliedMaterialsArea">
            <legend><?php echo __('Allied materials area'); ?></legend>

            <?php echo render_field($form->locationOfOriginals
                ->label(__('Existence and location of originals')), $resource, ['class' => 'resizable']); ?>

            <?php echo render_field($form->locationOfCopies
                ->label(__('Existence and location of copies')), $resource, ['class' => 'resizable']); ?>

            <?php echo render_field($form->relatedUnitsOfDescription
                ->label(__('Related units of description')), $resource, ['class' => 'resizable']); ?>

            <div class="form-item">
                <?php echo $form->relatedMaterialDescriptions
                    ->label(__('Related descriptions'))
                    ->renderLabel(); ?>
                <?php echo $form->relatedMaterialDescriptions->render(['class' => 'form-autocomplete']); ?>
                <?php if (QubitAcl::check(QubitInformationObject::getRoot(), 'create')) { ?>
                    <input class="add" type="hidden" data-link-existing="true" value="<?php echo url_for(['module' => 'informationobject', 'action' => 'add']); ?> #title"/>
                <?php } ?>
                <input class="list" type="hidden" value="<?php echo url_for(['module' => 'informationobject', 'action' => 'autocomplete']); ?>"/>
                <?php echo $form->relatedMaterialDescriptions
                    ->help(__('To create a relationship between this description and another description.'))
                    ->renderHelp(); ?>
            </div>

            <?php // echo get_partial('object/notes', $sf_data->getRaw('publicationNotesComponent')->getVarHolder()->getAll());?>
        </fieldset>

        <!-- Notes Area -->
        <!--fieldset class="collapsible collapsed" id="notesArea">
            <legend><?php // echo __('Notes area');?></legend>

            <?php // echo get_partial('object/notes', $sf_data->getRaw('notesComponent')->getVarHolder()->getAll());?>
        </fieldset-->

        <?php echo get_partial('informationobject/adminInfo', ['form' => $form, 'resource' => $resource]); ?>

    </div>

    <?php echo get_partial('informationobject/editActions', ['resource' => (null !== $parent ? $parent : $resource)]); ?>

    </form>

<?php end_slot(); ?>