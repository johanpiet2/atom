<?php

declare(strict_types=1);

namespace AtomExtensions\Extensions\MetadataExtraction\Controllers;

/**
 * Digital Object Controller.
 *
 * Handles digital object upload and metadata extraction for AtoM.
 * This is part of the atom-framework-v2 extension system.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class DigitalObjectController
{
    /**
     * Handle digital object upload from sfAction.
     *
     * @param \sfWebRequest $request The Symfony request
     * @param \sfAction     $action  The calling action (for access to resource, user, redirect)
     */
    public function handleUpload(\sfWebRequest $request, \sfAction $action): void
    {
        // Only process POST requests
        if (!$request->isMethod('post')) {
            return;
        }

        $resource = $action->getRoute()->resource;

        if (!$resource instanceof \QubitInformationObject) {
            $action->getUser()->setFlash('error', 'Invalid resource type');

            return;
        }

        try {
            // Get the uploaded file from the form
            $files = $request->getFiles('digitalobject');

            if (empty($files) || empty($files['file']['tmp_name'])) {
                $action->getUser()->setFlash('error', 'No file uploaded');

                return;
            }

            $file = $files['file'];

            // Validate file upload
            if (UPLOAD_ERR_OK !== $file['error']) {
                throw new \Exception($this->getUploadErrorMessage($file['error']));
            }

            // Create digital object using AtoM's native functionality
            $digitalObject = new \QubitDigitalObject();
            $digitalObject->usageId = \QubitTerm::MASTER_ID;
            $digitalObject->assets[] = new \QubitAsset($file['name'], $file['tmp_name']);
            $digitalObject->informationObjectId = $resource->id;
            $digitalObject->save();

            // Extract and apply metadata
            if ($this->shouldExtractMetadata()) {
                $this->extractAndApplyMetadata($digitalObject, $resource);
            }

            $action->getUser()->setFlash('notice', 'Digital object uploaded successfully');
            $action->redirect([$resource, 'module' => 'informationobject']);
        } catch (\Exception $e) {
            error_log('DigitalObjectController::handleUpload failed: '.$e->getMessage());
            $action->getUser()->setFlash('error', 'Upload failed: '.$e->getMessage());
        }
    }

    /**
     * Check if metadata extraction is enabled.
     */
    protected function shouldExtractMetadata(): bool
    {
        $setting = \QubitSetting::getByNameAndScope('metadata_extraction_enabled', 'metadata_extraction');

        if (null === $setting) {
            return true; // Default to enabled
        }

        return '1' === $setting->getValue(['sourceCulture' => true]);
    }

    /**
     * Extract and apply metadata to digital object and information object.
     */
    protected function extractAndApplyMetadata(\QubitDigitalObject $digitalObject, \QubitInformationObject $io): void
    {
        try {
            // Check if arEmbeddedMetadataParser is available
            if (!class_exists('arEmbeddedMetadataParser', true)) {
                error_log('DigitalObjectController: arEmbeddedMetadataParser not available');

                return;
            }

            // Get the file path
            $absPath = $digitalObject->getAbsolutePath();

            if (!$absPath || !is_readable($absPath)) {
                error_log('DigitalObjectController: File not readable: '.$absPath);

                return;
            }

            // Extract metadata
            $metadata = \arEmbeddedMetadataParser::extract($absPath);

            if (!is_array($metadata) || empty($metadata)) {
                error_log('DigitalObjectController: No metadata extracted from: '.$absPath);

                return;
            }

            // Apply metadata to information object
            $this->applyMetadataToInformationObject($metadata, $io);

            // Add technical metadata to physical characteristics
            $this->appendTechnicalMetadata($metadata, $io);

            error_log('DigitalObjectController: Metadata extraction completed for DO: '.$digitalObject->id);
        } catch (\Throwable $e) {
            error_log('DigitalObjectController: Metadata extraction failed: '.$e->getMessage());
        }
    }

    /**
     * Apply extracted metadata to the information object.
     */
    protected function applyMetadataToInformationObject(array $metadata, \QubitInformationObject $io): void
    {
        $overwriteTitle = $this->getSetting('overwrite_title', false);
        $overwriteDescription = $this->getSetting('overwrite_description', false);
        $autoGenerateKeywords = $this->getSetting('auto_generate_keywords', true);
        $extractGpsCoordinates = $this->getSetting('extract_gps_coordinates', true);

        // Apply title
        if (($overwriteTitle || empty($io->getTitle())) && !empty($metadata['title'])) {
            $io->setTitle($metadata['title']);
        }

        // Apply description
        if (($overwriteDescription || empty($io->getScopeAndContent())) && !empty($metadata['description'])) {
            $io->setScopeAndContent($metadata['description']);
        }

        // Apply creator/photographer
        if (!empty($metadata['creator'])) {
            $this->addCreator($metadata['creator'], $io);
        }

        // Apply creation date
        if (!empty($metadata['date_created'])) {
            $this->addCreationDate($metadata['date_created'], $io);
        }

        // Apply keywords
        if ($autoGenerateKeywords && !empty($metadata['keywords'])) {
            $this->addSubjectAccessPoints($metadata['keywords'], $io);
        }

        // Apply GPS coordinates
        if ($extractGpsCoordinates && !empty($metadata['gps'])) {
            $this->setGpsCoordinates($metadata['gps'], $io);
        }

        $io->save();
    }

    /**
     * Append technical metadata to physical characteristics field.
     */
    protected function appendTechnicalMetadata(array $metadata, \QubitInformationObject $io): void
    {
        if (!$this->getSetting('add_technical_metadata', true)) {
            return;
        }

        // Format the technical summary
        $summary = \arEmbeddedMetadataParser::formatSummary($metadata);

        if (empty($summary)) {
            return;
        }

        $targetField = $this->getSetting('technical_metadata_target_field', 'physicalCharacteristics');

        // Get existing content using getter method
        $getter = 'get'.ucfirst($targetField);
        $setter = 'set'.ucfirst($targetField);

        if (!method_exists($io, $getter) || !method_exists($io, $setter)) {
            error_log("DigitalObjectController: Invalid target field: {$targetField}");

            return;
        }

        $existing = (string) $io->{$getter}();

        // Remove previous technical metadata section
        if (!empty($existing)) {
            $existing = preg_replace('/\n*---\s*Technical Metadata\s*---.*$/s', '', $existing);
            $existing = rtrim($existing);
        }

        $io->{$setter}($existing ? $existing."\n\n".$summary : $summary);
        $io->save();
    }

    /**
     * Add creator actor and relation.
     */
    protected function addCreator(string $creatorName, \QubitInformationObject $io): void
    {
        // Find existing actor
        $criteria = new \Criteria();
        $criteria->add(\QubitActorI18n::AUTHORIZED_FORM_OF_NAME, $creatorName);
        $actorI18n = \QubitActorI18n::getOne($criteria);

        if (null === $actorI18n) {
            $actor = new \QubitActor();
            $actor->setAuthorizedFormOfName($creatorName);
            $actor->save();
        } else {
            $actor = $actorI18n->getActor();
        }

        // Check if relation already exists
        $criteria = new \Criteria();
        $criteria->add(\QubitEvent::INFORMATION_OBJECT_ID, $io->id);
        $criteria->add(\QubitEvent::ACTOR_ID, $actor->id);
        $criteria->add(\QubitEvent::TYPE_ID, \QubitTerm::CREATION_ID);

        if (null === \QubitEvent::getOne($criteria)) {
            $event = new \QubitEvent();
            $event->informationObjectId = $io->id;
            $event->actorId = $actor->id;
            $event->typeId = \QubitTerm::CREATION_ID;
            $event->save();
        }
    }

    /**
     * Add creation date event.
     */
    protected function addCreationDate(string $date, \QubitInformationObject $io): void
    {
        $criteria = new \Criteria();
        $criteria->add(\QubitEvent::INFORMATION_OBJECT_ID, $io->id);
        $criteria->add(\QubitEvent::TYPE_ID, \QubitTerm::CREATION_ID);

        $event = \QubitEvent::getOne($criteria);

        if (null === $event) {
            $event = new \QubitEvent();
            $event->informationObjectId = $io->id;
            $event->typeId = \QubitTerm::CREATION_ID;
        }

        $event->date = $date;
        $event->save();
    }

    /**
     * Add subject access points from keywords.
     */
    protected function addSubjectAccessPoints(array $keywords, \QubitInformationObject $io): void
    {
        foreach ($keywords as $keyword) {
            $keyword = trim($keyword);
            if (empty($keyword)) {
                continue;
            }

            // Find existing term
            $criteria = new \Criteria();
            $criteria->add(\QubitTermI18n::NAME, $keyword);
            $criteria->add(\QubitTerm::TAXONOMY_ID, \QubitTaxonomy::SUBJECT_ID);
            $termI18n = \QubitTermI18n::getOne($criteria);

            if (null === $termI18n) {
                $term = new \QubitTerm();
                $term->taxonomyId = \QubitTaxonomy::SUBJECT_ID;
                $term->setName($keyword);
                $term->save();
            } else {
                $term = $termI18n->getTerm();
            }

            // Check if relation already exists
            $criteria = new \Criteria();
            $criteria->add(\QubitObjectTermRelation::OBJECT_ID, $io->id);
            $criteria->add(\QubitObjectTermRelation::TERM_ID, $term->id);

            if (null === \QubitObjectTermRelation::getOne($criteria)) {
                $relation = new \QubitObjectTermRelation();
                $relation->objectId = $io->id;
                $relation->termId = $term->id;
                $relation->save();
            }
        }
    }

    /**
     * Set GPS coordinates.
     */
    protected function setGpsCoordinates(array $gps, \QubitInformationObject $io): void
    {
        if (empty($gps['latitude']) || empty($gps['longitude'])) {
            return;
        }

        $note = sprintf('GPS Coordinates: %s, %s', $gps['latitude'], $gps['longitude']);

        $existingNote = (string) $io->getScopeAndContent();
        if (false === strpos($existingNote, 'GPS Coordinates:')) {
            if (!empty($existingNote)) {
                $io->setScopeAndContent($existingNote."\n\n".$note);
                $io->save();
            }
        }
    }

    /**
     * Get a metadata extraction setting.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    protected function getSetting(string $name, $default = null)
    {
        $setting = \QubitSetting::getByNameAndScope($name, 'metadata_extraction');

        if (null === $setting) {
            return $default;
        }

        $value = $setting->getValue(['sourceCulture' => true]);

        if ('1' === $value) {
            return true;
        }
        if ('0' === $value) {
            return false;
        }

        return $value;
    }

    /**
     * Get human-readable upload error message.
     */
    protected function getUploadErrorMessage(int $errorCode): string
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension',
        ];

        return $messages[$errorCode] ?? 'Unknown upload error';
    }
}