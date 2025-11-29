<?php

class museumActions extends DefaultEditAction
{
    // Required NAMES property
    public static $NAMES = [
        'identifier',
        'title',
        'levelOfDescription',
        'repository',
        'parent',
        'displayStandard',
        'type',
    ];

    /**
     * Display action for museum objects.
     */
    public function executeIndex(sfWebRequest $request)
    {
        // Debug: Get slug from request
    echo 'DEBUG - Museum module executeIndex called!<br>';

    // Get slug from request
    $slug = $request->getParameter('slug');
    echo 'DEBUG - Slug received: '.$slug.'<br>';
        echo 'DEBUG - Slug received: '.$slug.'<br>';

        if (!$slug) {
            echo 'DEBUG - No slug provided<br>';
            $this->forward404();
        }

        // Try direct database query
        $sql = 'SELECT object_id FROM slug WHERE slug = ?';
        $objectId = QubitPdo::fetchColumn($sql, [$slug]);
        echo 'DEBUG - Object ID from database: '.$objectId.'<br>';

        if ($objectId) {
            $this->resource = QubitInformationObject::getById($objectId);
            echo 'DEBUG - Resource loaded: '.($this->resource ? 'Yes' : 'No').'<br>';

            if ($this->resource) {
                echo 'DEBUG - Resource ID: '.$this->resource->id.'<br>';
                echo 'DEBUG - Resource Title: '.$this->resource->getTitle().'<br>';
                echo 'DEBUG - Display Standard ID: '.$this->resource->displayStandardId.'<br>';
            }
        } else {
            echo 'DEBUG - No object found with slug: '.$slug.'<br>';
        }

        // Check if resource was found
        if (!$this->resource) {
            echo 'DEBUG - Resource is null, will show error<br>';
            // Don't forward404 yet, let template show the debug info
        }

        // Set page title if resource exists
        if ($this->resource) {
            if (1 > strlen($title = $this->resource->__toString())) {
                $title = $this->context->i18n->__('Untitled');
            }
            $this->response->setTitle("{$title} - {$this->response->getTitle()}");
        }
    }

    /**
     * Edit action for museum objects.
     */
    public function executeEdit(sfWebRequest $request)
    {
        // Get slug if editing existing
        $slug = $request->getParameter('slug');

        if ($slug) {
            $criteria = new Criteria();
            $criteria->add(QubitSlug::SLUG, $slug);
            $criteria->addJoin(QubitSlug::OBJECT_ID, QubitInformationObject::ID);
            $this->resource = QubitInformationObject::getOne($criteria);
        } else {
            // Creating new
            $this->resource = new QubitInformationObject();
            if ($request->hasParameter('parentId')) {
                $this->resource->parentId = $request->getParameter('parentId');
            }
        }

        // Use parent's edit functionality
        parent::execute($request);
    }

    /**
     * Add action - forward to edit.
     */
    public function executeAdd(sfWebRequest $request)
    {
        $this->resource = new QubitInformationObject();
        $this->forward('museum', 'edit');
    }
}
