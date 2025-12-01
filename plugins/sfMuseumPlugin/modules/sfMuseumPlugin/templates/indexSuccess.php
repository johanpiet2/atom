<?php decorate_with('layout_3col'); ?>

<?php slot('sidebar'); ?>
  <?php include_component('informationobject', 'contextMenu'); ?>
<?php end_slot(); ?>

<?php slot('title'); ?>
  <?php echo get_component('informationobject', 'descriptionHeader', ['resource' => $resource, 'title' => (string) $resource]); ?>
  
  <?php if (QubitInformationObject::ROOT_ID != $resource->parentId) { ?>
    <?php echo include_partial('default/breadcrumb', ['resource' => $resource, 'objects' => $resource->getAncestors()->andSelf()->orderBy('lft')]); ?>
  <?php } ?>
  
  <?php echo get_component('default', 'translationLinks', ['resource' => $resource]); ?>
<?php end_slot(); ?>

<?php slot('context-menu'); ?>
  <?php echo get_partial('informationobject/actionIcons', ['resource' => $resource]); ?>
  <?php echo get_partial('object/subjectAccessPoints', ['resource' => $resource, 'sidebar' => true]); ?>
  <?php echo get_partial('informationobject/nameAccessPoints', ['resource' => $resource, 'sidebar' => true]); ?>
  <?php echo get_partial('object/placeAccessPoints', ['resource' => $resource, 'sidebar' => true]); ?>
<?php end_slot(); ?>

  
  

<?php slot('before-content'); ?>

	<?php use_helper('informationobject'); ?>
	<?php if ('1' == QubitSetting::getByName('multi_digital_linked_display')) { // NARSSA / Plain Sailing option to disable display of multiple digital objects loaded?>
		<?php if (0 < count($resource->digitalObjectsRelatedByobjectId)) { // Multiple digital objects?>
			<?php foreach ($resource->digitalObjectsRelatedByobjectId as $obj) { ?>
				<?php echo render_digital_object_viewer($resource, $obj); ?>
			<?php } ?>
		<?php } ?>
	<?php } else { ?>
		<?php if (0 < count($resource->digitalObjectsRelatedByobjectId)) {?>
			<?php echo get_component('digitalobject', 'show', ['link' => $digitalObjectLink, 'resource' => $resource->digitalObjectsRelatedByobjectId[0], 'usageType' => QubitTerm::REFERENCE_ID]); ?>
		<?php } ?>
	<?php } ?>


	<?php // Feedback and Request to Publish buttons
          // Plain Sailing Information systems
          // Add to Favorites?>
		  
	<?php
        $userId = $this->context->user->getAttribute('user_id');
        $favorate_id = QubitFavorites::getByUserIDandObjectId($userId, $resource->id);
    ?>
	<?php echo link_to(__('Item Feedback'), [$resource, 'module' => 'informationobject', 'action' => 'editFeedback'], ['class' => 'c-btn c-btn-submit']); ?>
	<?php if ('' != $favorate_id->id && '' != $userId) {
            echo link_to(__('Remove from Favorites'), [$resource, 'module' => 'informationobject', 'action' => 'removeFavorites'], ['class' => 'c-btn c-btn-submit']);
        } elseif ('' != $userId) {
            echo link_to(__('Add to Favorites'), [$resource, 'module' => 'informationobject', 'action' => 'addFavorites'], ['class' => 'c-btn c-btn-submit']);
        }
        ?>
	<?php echo link_to(__('Request to Publish'), [$resource, 'module' => 'informationobject', 'action' => 'editRequestToPublish'], ['class' => 'c-btn c-btn-submit']); ?>

	<?php
        $userId = $this->context->user->getAttribute('user_id');
        $cart_id = QubitCart::getByUserIDandObjectId($userId, $resource->id);
    ?>

	<?php if ('' != $cart_id->id && '' != $userId) {
            echo link_to(__('Go to Cart'), [$resource, 'module' => 'cart', 'action' => 'browse'], ['class' => 'c-btn c-btn-submit']);
        } elseif ('' != $userId) {
            echo link_to(__('Add to Cart'), [$resource, 'module' => 'informationobject', 'action' => 'addCart'], ['class' => 'c-btn c-btn-submit']);
        }
        ?>

<?php end_slot(); ?>
<!-- Standard Identity Area -->
<section id="identityArea">
  <h2><?php echo __('Identity area'); ?></h2>
  
  <?php echo render_show(__('Reference code'), $resource->referenceCode); ?>
  <?php echo render_show(__('Title'), render_title($resource)); ?>
  
  <?php if ($resource->getDates()) { ?>
    <div class="field">
      <h3><?php echo __('Date(s)'); ?></h3>
      <div>
        <ul>
          <?php foreach ($resource->getDates() as $item) { ?>
            <li>
              <?php echo render_value_inline(Qubit::renderDateStartEnd($item->getDate(['cultureFallback' => true]), $item->startDate, $item->endDate)); ?> 
              (<?php echo $item->getType(['cultureFallback' => true]); ?>)
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  <?php } ?>
  
  <?php echo render_show(__('Level of description'), render_value($resource->levelOfDescription)); ?>
  <?php echo render_show(__('Extent and medium'), render_value($resource->getExtentAndMedium(['cultureFallback' => true]))); ?>
</section>

<!-- Context Area -->
<section id="contextArea">
  <h2><?php echo __('Context area'); ?></h2>
  
  <div class="creatorHistories">
    <?php echo get_component('informationobject', 'creatorDetail', [
        'resource' => $resource,
        'creatorHistoryLabels' => $creatorHistoryLabels, ]); ?>
  </div>
  
  <?php echo render_show_repository(__('Repository'), $resource); ?>
  <?php echo render_show(__('Archival history'), render_value($resource->getArchivalHistory(['cultureFallback' => true]))); ?>
  <?php echo render_show(__('Immediate source of acquisition'), render_value($resource->getAcquisition(['cultureFallback' => true]))); ?>
</section>

<!-- Museum Object Information -->
<?php if (isset($museumData) && !empty($museumData)) { ?>
<section id="museumObjectArea">
  <h2><?php echo __('Museum object information'); ?></h2>
  
  <?php if (!empty($museumData['work_type'])) { ?>
    <?php echo render_show(__('Work type'), isset($museumData['work_type_label']) ? $museumData['work_type_label'] : $museumData['work_type']); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['object_type'])) { ?>
    <?php echo render_show(__('Object type'), $museumData['object_type']); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['classification'])) { ?>
    <?php echo render_show(__('Classification'), $museumData['classification']); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['creation_date_earliest']) || !empty($museumData['creation_date_latest'])) { ?>
    <?php
      $dateRange = '';
      if (!empty($museumData['creation_date_earliest']) && !empty($museumData['creation_date_latest'])) {
        $dateRange = $museumData['creation_date_earliest'].' - '.$museumData['creation_date_latest'];
      } elseif (!empty($museumData['creation_date_earliest'])) {
        $dateRange = 'After '.$museumData['creation_date_earliest'];
      } elseif (!empty($museumData['creation_date_latest'])) {
        $dateRange = 'Before '.$museumData['creation_date_latest'];
      }
      echo render_show(__('Creation date'), $dateRange);
    ?>
  <?php } ?>

  <!-- Materials (already there, but let's ensure all labels are included) -->
  <?php if (!empty($museumData['materials'])) { ?>
    <?php
      $materials = json_decode($museumData['materials'], true);
      if (is_array($materials)) {
        $materialLabels = [
            'oil_paint' => 'Oil paint',
            'canvas' => 'Canvas',
            'paper' => 'Paper',
            'wood' => 'Wood',
            'metal' => 'Metal',
            'stone' => 'Stone',
            'textile' => 'Textile',
            'ceramic' => 'Ceramic',
            'glass' => 'Glass',
            'plastic' => 'Plastic',
        ];
        $display = [];
        foreach ($materials as $material) {
          $display[] = isset($materialLabels[$material]) ? $materialLabels[$material] : $material;
        }
        echo render_show(__('Materials'), implode(', ', $display));
      }
    ?>
  <?php } ?>

  <!-- Techniques (update with all labels) -->
  <?php if (!empty($museumData['techniques'])) { ?>
    <?php
      $techniques = json_decode($museumData['techniques'], true);
      if (is_array($techniques)) {
        $techniqueLabels = [
            'painted' => 'Painted',
            'glazed' => 'Glazed',
            'carved' => 'Carved',
            'etched' => 'Etched',
            'printed' => 'Printed',
            'woven' => 'Woven',
            'cast' => 'Cast',
            'molded' => 'Molded',
            'assembled' => 'Assembled',
            'fired' => 'Fired',
        ];
        $display = [];
        foreach ($techniques as $technique) {
          $display[] = isset($techniqueLabels[$technique]) ? $techniqueLabels[$technique] : $technique;
        }
        echo render_show(__('Techniques'), implode(', ', $display));
      }
    ?>
  <?php } ?>
  
  <?php if (!empty($museumData['measurements'])) { ?>
    <?php echo render_show(__('Measurements'), render_value($museumData['measurements'])); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['dimensions'])) { ?>
    <?php echo render_show(__('Dimensions'), $museumData['dimensions']); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['inscription'])) { ?>
    <?php echo render_show(__('Inscriptions and marks'), render_value($museumData['inscription'])); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['condition_notes'])) { ?>
    <?php echo render_show(__('Condition notes'), render_value($museumData['condition_notes'])); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['provenance'])) { ?>
    <?php echo render_show(__('Provenance'), render_value($museumData['provenance'])); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['cultural_context'])) { ?>
    <?php echo render_show(__('Cultural context'), $museumData['cultural_context']); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['style_period'])) { ?>
    <?php echo render_show(__('Style or period'), $museumData['style_period']); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['current_location'])) { ?>
    <?php echo render_show(__('Current location'), render_value($museumData['current_location'])); ?>
  <?php } ?>
  
  <?php if (!empty($museumData['creator_identity']) || !empty($museumData['creator_role']) || !empty($museumData['creator_attribution'])) { ?>
	<section id="ccoCreatorArea">
	  <h2><?php echo __('Creator information'); ?></h2>
	  <?php if (!empty($museumData['creator_identity'])) { ?>
		<?php echo render_show(__('Creator/Maker'), $museumData['creator_identity']); ?>
	  <?php } ?>
	  <?php if (!empty($museumData['creator_role'])) { ?>
		<?php echo render_show(__('Role'), isset($museumData['creator_role_label']) ? $museumData['creator_role_label'] : $museumData['creator_role']); ?>
	  <?php } ?>
	  <?php if (!empty($museumData['creator_extent'])) { ?>
		<?php echo render_show(__('Extent'), $museumData['creator_extent']); ?>
	  <?php } ?>
	  <?php if (!empty($museumData['creator_qualifier'])) { ?>
		<?php echo render_show(__('Qualifier'), isset($museumData['creator_qualifier_label']) ? $museumData['creator_qualifier_label'] : $museumData['creator_qualifier']); ?>
	  <?php } ?>
	  <?php if (!empty($museumData['creator_attribution'])) { ?>
		<?php echo render_show(__('Attribution'), render_value($museumData['creator_attribution'])); ?>
	  <?php } ?>
	</section>
  <?php } ?>

	<?php if (!empty($museumData['creation_date_display']) || !empty($museumData['creation_date_qualifier'])) { ?>
		<section id="ccoCreationDateArea">
		  <h2><?php echo __('Creation date'); ?></h2>
		  <?php if (!empty($museumData['creation_date_display'])) { ?>
			<?php echo render_show(__('Display date'), $museumData['creation_date_display']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['creation_date_qualifier'])) { ?>
			<?php echo render_show(__('Date qualifier'), isset($museumData['creation_date_qualifier_label']) ? $museumData['creation_date_qualifier_label'] : $museumData['creation_date_qualifier']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['style']) || !empty($museumData['period']) || !empty($museumData['cultural_group']) || !empty($museumData['movement']) || !empty($museumData['school']) || !empty($museumData['dynasty'])) { ?>
		<section id="ccoStylePeriodArea">
		  <h2><?php echo __('Styles, periods, groups, movements'); ?></h2>
		  <?php if (!empty($museumData['style'])) { ?>
			<?php echo render_show(__('Style'), $museumData['style']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['period'])) { ?>
			<?php echo render_show(__('Period'), $museumData['period']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['cultural_group'])) { ?>
			<?php echo render_show(__('Culture/Group'), $museumData['cultural_group']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['movement'])) { ?>
			<?php echo render_show(__('Movement'), $museumData['movement']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['school'])) { ?>
			<?php echo render_show(__('School'), $museumData['school']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['dynasty'])) { ?>
			<?php echo render_show(__('Dynasty'), $museumData['dynasty']); ?>
		  <?php } ?>
		</section>
	<?php } ?>
	
	<?php if (!empty($museumData['subject_display'])) { ?>
		<section id="ccoSubjectArea">
		  <h2><?php echo __('Subject matter'); ?></h2>
		  <?php if (!empty($museumData['subject_indexing_type'])) { ?>
			<?php echo render_show(__('Indexing type'), isset($museumData['subject_indexing_type_label']) ? $museumData['subject_indexing_type_label'] : $museumData['subject_indexing_type']); ?>
		  <?php } ?>
		  <?php echo render_show(__('Subject'), render_value($museumData['subject_display'])); ?>
		  <?php if (!empty($museumData['subject_extent'])) { ?>
			<?php echo render_show(__('Extent'), $museumData['subject_extent']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['historical_context']) || !empty($museumData['architectural_context']) || !empty($museumData['archaeological_context'])) { ?>
		<section id="ccoContextArea">
		  <h2><?php echo __('Context'); ?></h2>
		  <?php if (!empty($museumData['historical_context'])) { ?>
			<?php echo render_show(__('Historical'), render_value($museumData['historical_context'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['architectural_context'])) { ?>
			<?php echo render_show(__('Architectural'), $museumData['architectural_context']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['archaeological_context'])) { ?>
			<?php echo render_show(__('Archaeological'), $museumData['archaeological_context']); ?>
		  <?php } ?>
		</section>
	<?php } ?>
			
	<?php if (!empty($museumData['object_class']) || !empty($museumData['object_category']) || !empty($museumData['object_sub_category'])) { ?>
		<section id="ccoClassArea">
		  <h2><?php echo __('Classification'); ?></h2>
		  <?php if (!empty($museumData['object_class'])) { ?>
			<?php echo render_show(__('Class'), $museumData['object_class']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['object_category'])) { ?>
			<?php echo render_show(__('Category'), $museumData['object_category']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['object_sub_category'])) { ?>
			<?php echo render_show(__('Sub-category'), $museumData['object_sub_category']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['edition_number']) || !empty($museumData['edition_description']) || !empty($museumData['state_description'])) { ?>
		<section id="ccoEditionArea">
		  <h2><?php echo __('Edition/State'); ?></h2>
		  <?php if (!empty($museumData['edition_number'])) { ?>
			<?php
              $edition = $museumData['edition_number'];
              if (!empty($museumData['edition_size'])) {
                  $edition .= ' of '.$museumData['edition_size'];
              }
              echo render_show(__('Edition'), $edition);
            ?>
		  <?php } ?>
		  <?php if (!empty($museumData['edition_description'])) { ?>
			<?php echo render_show(__('Description'), render_value($museumData['edition_description'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['state_identification'])) { ?>
			<?php echo render_show(__('State'), $museumData['state_identification']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['state_description'])) { ?>
			<?php echo render_show(__('State description'), $museumData['state_description']); ?>
		  <?php } ?>
		</section>
	<?php } ?>		

	<?php if (!empty($museumData['facture_description']) || !empty($museumData['technique_cco'])) { ?>
		<section id="ccoTechniqueArea">
		  <h2><?php echo __('Facture/Technique'); ?></h2>
		  <?php if (!empty($museumData['facture_description'])) { ?>
			<?php echo render_show(__('Facture'), render_value($museumData['facture_description'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['technique_cco'])) { ?>
			<?php echo render_show(__('Technique'), $museumData['technique_cco']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['technique_qualifier'])) { ?>
			<?php echo render_show(__('Qualifier'), $museumData['technique_qualifier']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['physical_appearance']) || !empty($museumData['color']) || !empty($museumData['shape'])) { ?>
		<section id="ccoPhysicalArea">
		  <h2><?php echo __('Physical description'); ?></h2>
		  <?php if (!empty($museumData['physical_appearance'])) { ?>
			<?php echo render_show(__('Appearance'), render_value($museumData['physical_appearance'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['color'])) { ?>
			<?php echo render_show(__('Color'), $museumData['color']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['shape'])) { ?>
			<?php echo render_show(__('Shape'), $museumData['shape']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['orientation'])) { ?>
			<?php echo render_show(__('Orientation'), $museumData['orientation']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['condition_term']) || !empty($museumData['condition_description'])) { ?>
		<section id="ccoConditionArea">
		  <h2><?php echo __('Condition'); ?></h2>
		  <?php if (!empty($museumData['condition_term'])) { ?>
			<?php echo render_show(__('Condition'), isset($museumData['condition_term_label']) ? $museumData['condition_term_label'] : $museumData['condition_term']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['condition_date'])) { ?>
			<?php echo render_show(__('Date examined'), $museumData['condition_date']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['condition_description'])) { ?>
			<?php echo render_show(__('Description'), render_value($museumData['condition_description'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['condition_agent'])) { ?>
			<?php echo render_show(__('Examiner'), $museumData['condition_agent']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['treatment_type']) || !empty($museumData['treatment_description'])) { ?>
		<section id="ccoConservationArea">
		  <h2><?php echo __('Conservation/Treatment'); ?></h2>
		  <?php if (!empty($museumData['treatment_type'])) { ?>
			<?php echo render_show(__('Treatment type'), $museumData['treatment_type']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['treatment_date'])) { ?>
			<?php echo render_show(__('Date'), $museumData['treatment_date']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['treatment_agent'])) { ?>
			<?php echo render_show(__('Conservator'), $museumData['treatment_agent']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['treatment_description'])) { ?>
			<?php echo render_show(__('Description'), render_value($museumData['treatment_description'])); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['inscription_transcription']) || !empty($museumData['mark_description'])) { ?>
		<section id="ccoInscriptionArea">
		  <h2><?php echo __('Inscriptions/Marks'); ?></h2>
		  <?php if (!empty($museumData['inscription_type'])) { ?>
			<?php echo render_show(__('Type'), isset($museumData['inscription_type_label']) ? $museumData['inscription_type_label'] : $museumData['inscription_type']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['inscription_transcription'])) { ?>
			<?php echo render_show(__('Transcription'), render_value($museumData['inscription_transcription'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['inscription_location'])) { ?>
			<?php echo render_show(__('Location'), $museumData['inscription_location']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['inscription_language'])) { ?>
			<?php echo render_show(__('Language'), $museumData['inscription_language']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['inscription_translation'])) { ?>
			<?php echo render_show(__('Translation'), render_value($museumData['inscription_translation'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['mark_type'])) { ?>
			<?php echo render_show(__('Mark type'), $museumData['mark_type']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['mark_description'])) { ?>
			<?php echo render_show(__('Mark description'), render_value($museumData['mark_description'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['mark_location'])) { ?>
			<?php echo render_show(__('Mark location'), $museumData['mark_location']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['related_work_label'])) { ?>
		<section id="ccoRelatedWorksArea">
		  <h2><?php echo __('Related works'); ?></h2>
		  <?php if (!empty($museumData['related_work_type'])) { ?>
			<?php echo render_show(__('Relationship type'), isset($museumData['related_work_type_label']) ? $museumData['related_work_type_label'] : $museumData['related_work_type']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['related_work_relationship'])) { ?>
			<?php echo render_show(__('Relationship'), $museumData['related_work_relationship']); ?>
		  <?php } ?>
		  <?php echo render_show(__('Related work'), $museumData['related_work_label']); ?>
		  <?php if (!empty($museumData['related_work_id'])) { ?>
			<?php echo render_show(__('Identifier'), $museumData['related_work_id']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['current_location_repository']) || !empty($museumData['creation_place']) || !empty($museumData['discovery_place'])) { ?>
		<section id="ccoLocationArea">
		  <h2><?php echo __('Location'); ?></h2>
		  <?php if (!empty($museumData['current_location_repository'])) { ?>
			<?php echo render_show(__('Current repository'), $museumData['current_location_repository']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['current_location_geography'])) { ?>
			<?php echo render_show(__('Geography'), $museumData['current_location_geography']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['current_location_coordinates'])) { ?>
			<?php echo render_show(__('Coordinates'), $museumData['current_location_coordinates']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['current_location_ref_number'])) { ?>
			<?php echo render_show(__('Reference number'), $museumData['current_location_ref_number']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['creation_place'])) { ?>
			<?php echo render_show(__('Place of creation'), $museumData['creation_place']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['discovery_place'])) { ?>
			<?php echo render_show(__('Place of discovery'), $museumData['discovery_place']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['provenance_text']) || !empty($museumData['ownership_history'])) { ?>
		<section id="ccoProvenanceArea">
		  <h2><?php echo __('Provenance/Ownership'); ?></h2>
		  <?php if (!empty($museumData['provenance_text'])) { ?>
			<?php echo render_show(__('Provenance'), render_value($museumData['provenance_text'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['ownership_history'])) { ?>
			<?php echo render_show(__('Ownership history'), render_value($museumData['ownership_history'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['legal_status'])) { ?>
			<?php echo render_show(__('Legal status'), $museumData['legal_status']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['rights_type']) || !empty($museumData['rights_holder'])) { ?>
		<section id="ccoRightsArea">
		  <h2><?php echo __('Rights'); ?></h2>
		  <?php if (!empty($museumData['rights_type'])) { ?>
			<?php echo render_show(__('Rights type'), isset($museumData['rights_type_label']) ? $museumData['rights_type_label'] : $museumData['rights_type']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['rights_holder'])) { ?>
			<?php echo render_show(__('Rights holder'), $museumData['rights_holder']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['rights_date'])) { ?>
			<?php echo render_show(__('Date'), $museumData['rights_date']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['rights_remarks'])) { ?>
			<?php echo render_show(__('Remarks'), render_value($museumData['rights_remarks'])); ?>
		  <?php } ?>
		</section>
	<?php } ?>

	<?php if (!empty($museumData['cataloger_name']) || !empty($museumData['cataloging_institution'])) { ?>
		<section id="ccoCatalogingArea">
		  <h2><?php echo __('Cataloging information'); ?></h2>
		  <?php if (!empty($museumData['cataloger_name'])) { ?>
			<?php echo render_show(__('Cataloger'), $museumData['cataloger_name']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['cataloging_date'])) { ?>
			<?php echo render_show(__('Date cataloged'), $museumData['cataloging_date']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['cataloging_institution'])) { ?>
			<?php echo render_show(__('Institution'), $museumData['cataloging_institution']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['cataloging_remarks'])) { ?>
			<?php echo render_show(__('Remarks'), render_value($museumData['cataloging_remarks'])); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['record_type'])) { ?>
			<?php echo render_show(__('Record type'), $museumData['record_type']); ?>
		  <?php } ?>
		  <?php if (!empty($museumData['record_level'])) { ?>
			<?php echo render_show(__('Record level'), $museumData['record_level']); ?>
		  <?php } ?>
		</section>
	<?php } ?>

</section>
<?php } ?>

<!-- Content and Structure Area -->
<section id="contentAndStructureArea">
  <h2><?php echo __('Content and structure area'); ?></h2>
  
  <?php echo render_show(__('Scope and content'), render_value($resource->getScopeAndContent(['cultureFallback' => true]))); ?>
  <?php echo render_show(__('Appraisal, destruction and scheduling'), render_value($resource->getAppraisal(['cultureFallback' => true]))); ?>
  <?php echo render_show(__('Accruals'), render_value($resource->getAccruals(['cultureFallback' => true]))); ?>
  <?php echo render_show(__('System of arrangement'), render_value($resource->getArrangement(['cultureFallback' => true]))); ?>
</section>

<!-- Conditions of Access and Use Area -->
<section id="conditionsOfAccessAndUseArea">
  <h2><?php echo __('Conditions of access and use area'); ?></h2>
  
  <?php echo render_show(__('Conditions governing access'), render_value($resource->getAccessConditions(['cultureFallback' => true]))); ?>
  <?php echo render_show(__('Conditions governing reproduction'), render_value($resource->getReproductionConditions(['cultureFallback' => true]))); ?>
  <?php echo render_show(__('Physical characteristics'), render_value($resource->getPhysicalCharacteristics(['cultureFallback' => true]))); ?>
  <?php echo render_show(__('Finding aids'), render_value($resource->getFindingAids(['cultureFallback' => true]))); ?>
</section>

<!-- Access Points -->
<section id="accessPointsArea">
  <h2><?php echo __('Access points'); ?></h2>
  
  <?php echo get_partial('object/subjectAccessPoints', ['resource' => $resource]); ?>
  <?php echo get_partial('object/placeAccessPoints', ['resource' => $resource]); ?>
  <?php echo get_partial('informationobject/nameAccessPoints', ['resource' => $resource]); ?>
</section>

<!-- Rights Area -->
<?php if ($sf_user->isAuthenticated()) { ?>

  <div class="section border-bottom" id="rightsArea">

    <?php echo render_b5_section_heading(__('Rights area')); ?>

    <div class="relatedRights">
      <?php echo get_component('right', 'relatedRights', ['resource' => $resource]); ?>
    </div>

  </div> <!-- /section#rightsArea -->

<?php } ?>

<?php if (0 < count($resource->digitalObjectsRelatedByobjectId)) { // NARSSA / Plain Sailing multiple digital objects loaded against Archival Description?>
	<?php if ('1' == QubitSetting::getByName('multi_digital_linked_display')) { // NARSSA / Plain Sailing option to disable display of multiple digital objects loaded against Archival Description?>
		<?php for ($n = 0; $n < count($resource->digitalObjectsRelatedByobjectId); ++$n) { ?>
			  <div class="digitalObjectMetadata">
				<?php echo get_component('digitalobject', 'metadata', ['resource' => $resource->digitalObjectsRelatedByobjectId[$n], 'object' => $resource]); ?>
				<?php $imagePath = str_replace(QubitSetting::getByName('siteBaseUrl'), '', $digitalObjectLink); ?>
			  </div>
	<?php }
        } else { ?>
			  <div class="digitalObjectMetadata">
				<?php echo get_component('digitalobject', 'metadata', ['resource' => $resource->digitalObjectsRelatedByobjectId[0], 'object' => $resource]); ?>
			  </div>
		<?php
        }
}
?>

<section id="accessionArea" class="border-bottom">

  <?php echo render_b5_section_heading(__('Accession area')); ?>

  <div class="accessions">
    <?php echo get_component('informationobject', 'accessions', ['resource' => $resource]); ?>
  </div>

</section> <!-- /section#accessionArea -->

<?php slot('after-content'); ?>
  <?php echo get_partial('sfMuseumPlugin/actions', ['resource' => $resource]); ?>
<?php end_slot(); ?>

<?php echo get_component('object', 'gaInstitutionsDimension', ['resource' => $resource]); ?>

