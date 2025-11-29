<?php

class sfMuseumPluginIndexAction extends InformationObjectIndexAction
{
    public function execute($request)
    {
        parent::execute($request);

        $this->isad = new sfIsadPlugin($this->resource);

        // Load museum metadata
        $this->loadMuseumData();

        if (1 > strlen($title = $this->resource->__toString())) {
            $title = $this->context->i18n->__('Untitled');
        }

        $this->response->setTitle("{$title} - {$this->response->getTitle()}");

        // Function relations for context area
        $this->functionRelations = $this->getFunctionRelations($this->resource->id);

        // Creator history labels - use hardcoded term IDs
        // CORPORATE_BODY_ID = 131, PERSON_ID = 132, FAMILY_ID = 133
        $this->creatorHistoryLabels = [
            null => $this->context->i18n->__('Administrative / Biographical history'),
            131 => $this->context->i18n->__('Administrative history'),
            132 => $this->context->i18n->__('Biographical history'),
            133 => $this->context->i18n->__('Biographical history'),
        ];
    }

    protected function getUserCulture()
    {
        return sfContext::getInstance()->getUser()->getCulture();
    }

    protected function getFunctionRelations($objectId)
    {
        $results = \Illuminate\Database\Capsule\Manager::table('relation as r')
            ->join('function_object as f', 'r.subject_id', '=', 'f.id')
            ->leftJoin('function_object_i18n as fi', function ($join) {
                $join->on('f.id', '=', 'fi.id')
                    ->where('fi.culture', '=', $this->getUserCulture());
            })
            ->where('r.object_id', $objectId)
            ->select('r.id', 'r.subject_id', 'r.object_id', 'r.type_id', 'fi.authorized_form_of_name')
            ->get();

        $relations = [];
        foreach ($results as $row) {
            $relation = new \stdClass();
            $relation->id = $row->id;
            $relation->subjectId = $row->subject_id;
            $relation->objectId = $row->object_id;
            $relation->typeId = $row->type_id;

            $relation->subject = new \stdClass();
            $relation->subject->id = $row->subject_id;
            $relation->subject->authorizedFormOfName = $row->authorized_form_of_name;

            $relations[] = $relation;
        }

        return $relations;
    }

    protected function loadMuseumData()
    {
        $this->museumData = [];

        if (!$this->resource || !$this->resource->id) {
            return;
        }

        try {
            $result = \Illuminate\Database\Capsule\Manager::table('museum_metadata')
                ->where('object_id', $this->resource->id)
                ->first();

            if ($result) {
                $this->museumData = (array) $result;

                // Add labels for work types
                $workTypes = [
                    'visual_works' => $this->context->i18n->__('Visual Works'),
                    'built_works' => $this->context->i18n->__('Built Works'),
                    'movable_works' => $this->context->i18n->__('Movable Works'),
                    'object' => $this->context->i18n->__('Objects'),
                    'image' => $this->context->i18n->__('Images'),
                ];

                if (isset($this->museumData['work_type'], $workTypes[$this->museumData['work_type']])) {
                    $this->museumData['work_type_label'] = $workTypes[$this->museumData['work_type']];
                }

                // Add labels for controlled vocabularies
                $this->addControlledVocabularyLabels();

                // Fix HTML-encoded JSON fields
                $jsonFields = ['materials', 'techniques', 'measurements'];
                foreach ($jsonFields as $field) {
                    if (isset($this->museumData[$field])) {
                        $decoded = html_entity_decode($this->museumData[$field], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        if (false !== strpos($decoded, '&quot;')) {
                            $decoded = str_replace(['&quot;', '&amp;', '&lt;', '&gt;'], ['"', '&', '<', '>'], $decoded);
                        }
                        $this->museumData[$field] = $decoded;
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Museum metadata load error: '.$e->getMessage());
        }
    }

    /**
     * In sfMuseumPluginIndexAction, use CcoTaxonomyService for labels.
     */
    protected function addControlledVocabularyLabels()
    {
        $ccoService = new CcoTaxonomyService($this->context->user->getCulture());

        if (!empty($this->museumData['creator_role'])) {
            $this->museumData['creator_role_label'] = $ccoService->getCreatorRoleLabel($this->museumData['creator_role']);
        }

        if (!empty($this->museumData['creator_qualifier'])) {
            $this->museumData['creator_qualifier_label'] = $ccoService->getAttributionQualifierLabel($this->museumData['creator_qualifier']);
        }

        if (!empty($this->museumData['creation_date_qualifier'])) {
            $this->museumData['creation_date_qualifier_label'] = $ccoService->getDateQualifierLabel($this->museumData['creation_date_qualifier']);
        }

        if (!empty($this->museumData['condition_term'])) {
            $this->museumData['condition_term_label'] = $ccoService->getConditionTermLabel($this->museumData['condition_term']);
        }

        if (!empty($this->museumData['subject_indexing_type'])) {
            $this->museumData['subject_indexing_type_label'] = $ccoService->getSubjectTypeLabel($this->museumData['subject_indexing_type']);
        }

        if (!empty($this->museumData['inscription_type'])) {
            $this->museumData['inscription_type_label'] = $ccoService->getInscriptionTypeLabel($this->museumData['inscription_type']);
        }

        if (!empty($this->museumData['related_work_type'])) {
            $this->museumData['related_work_type_label'] = $ccoService->getRelatedWorkTypeLabel($this->museumData['related_work_type']);
        }

        if (!empty($this->museumData['rights_type'])) {
            $this->museumData['rights_type_label'] = $ccoService->getRightsTypeLabel($this->museumData['rights_type']);
        }

        if (!empty($this->museumData['work_type'])) {
            $this->museumData['work_type_label'] = $ccoService->getWorkTypeLabel($this->museumData['work_type']);
        }
    }
}
