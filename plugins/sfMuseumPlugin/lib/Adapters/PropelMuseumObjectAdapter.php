<?php

declare(strict_types=1);

namespace sfMuseumPlugin\Adapters;

/**
 * Propel adapter for museum objects.
 *
 * This adapter provides legacy compatibility by bridging Propel QubitInformationObject
 * instances with the Laravel-based museum metadata services.
 */
class PropelMuseumObjectAdapter
{
    private LaravelMuseumAdapter $adapter;

    public function __construct()
    {
        $this->adapter = new LaravelMuseumAdapter();
    }

    /**
     * Enrich a Propel information object with museum metadata.
     *
     * @param \QubitInformationObject $object
     * @param array                   $museumProperties
     */
    public function enrichInformationObject(\QubitInformationObject $object, array $museumProperties): void
    {
        if (!$object->id) {
            throw new \InvalidArgumentException('Information object must be saved before enriching with museum metadata');
        }

        $this->adapter->enrichWithCcoMetadata($object->id, $museumProperties);
    }

    /**
     * Get museum metadata for a Propel information object.
     *
     * @param \QubitInformationObject $object
     *
     * @return null|array Museum metadata as array or null if none exists
     */
    public function getMuseumMetadata(\QubitInformationObject $object): ?array
    {
        if (!$object->id) {
            return null;
        }

        $museumObject = $this->adapter->getMuseumMetadata($object->id);

        if (!$museumObject) {
            return null;
        }

        return $museumObject->toArray();
    }

    /**
     * Check if information object has museum metadata.
     *
     * @param \QubitInformationObject $object
     *
     * @return bool
     */
    public function hasMuseumMetadata(\QubitInformationObject $object): bool
    {
        if (!$object->id) {
            return false;
        }

        return $this->adapter->hasMuseumMetadata($object->id);
    }

    /**
     * Delete museum metadata for an information object.
     *
     * @param \QubitInformationObject $object
     *
     * @return bool
     */
    public function deleteMuseumMetadata(\QubitInformationObject $object): bool
    {
        if (!$object->id) {
            return false;
        }

        return $this->adapter->deleteMuseumMetadata($object->id);
    }

    /**
     * Extract museum metadata from information object properties.
     *
     * This method extracts museum-relevant data from standard AtoM fields
     * to populate museum metadata fields.
     *
     * @param \QubitInformationObject $object
     *
     * @return array
     */
    public function extractFromInformationObject(\QubitInformationObject $object): array
    {
        $properties = [];

        // Work type - try to infer from level of description or type
        if ($object->levelOfDescription) {
            $properties['work_type'] = $this->inferWorkType($object);
        }

        // Extract date information
        $dates = $object->getDates();
        if (count($dates) > 0) {
            $creationDates = [];
            foreach ($dates as $date) {
                if (\QubitTerm::CREATION_ID == $date->typeId) {
                    if ($date->startDate) {
                        $creationDates['earliest'] = $date->startDate;
                    }
                    if ($date->endDate) {
                        $creationDates['latest'] = $date->endDate;
                    }
                }
            }

            if (!empty($creationDates['earliest'])) {
                $properties['creation_date_earliest'] = $creationDates['earliest'];
            }
            if (!empty($creationDates['latest'])) {
                $properties['creation_date_latest'] = $creationDates['latest'];
            }
        }

        // Extract physical characteristics
        if ($object->extentAndMedium) {
            $properties['measurements'] = $this->adapter->parseMeasurements($object->extentAndMedium);
        }

        // Extract scope and content as potential description
        if ($object->scopeAndContent) {
            // Could be used for condition notes or inscription
            $properties['condition_notes'] = $object->scopeAndContent;
        }

        return $properties;
    }

    /**
     * Apply museum metadata to information object fields.
     *
     * This method updates standard AtoM fields based on museum metadata.
     *
     * @param \QubitInformationObject $object
     * @param array                   $museumMetadata
     */
    public function applyToInformationObject(\QubitInformationObject $object, array $museumMetadata): void
    {
        // Update extent and medium with formatted measurements
        if (!empty($museumMetadata['measurements'])) {
            $extentStatement = $this->adapter->getExtentStatement($museumMetadata['measurements']);
            if ($extentStatement) {
                $object->extentAndMedium = $extentStatement;
            }
        }

        // Add materials to physical characteristics
        if (!empty($museumMetadata['materials'])) {
            $materialsText = 'Materials: '.implode(', ', $museumMetadata['materials']);
            if ($object->physicalCharacteristics) {
                $object->physicalCharacteristics .= "\n\n".$materialsText;
            } else {
                $object->physicalCharacteristics = $materialsText;
            }
        }

        // Add techniques to physical characteristics
        if (!empty($museumMetadata['techniques'])) {
            $techniquesText = 'Techniques: '.implode(', ', $museumMetadata['techniques']);
            if ($object->physicalCharacteristics) {
                $object->physicalCharacteristics .= "\n".$techniquesText;
            } else {
                $object->physicalCharacteristics = $techniquesText;
            }
        }
    }

    /**
     * Get the underlying Laravel adapter.
     *
     * @return LaravelMuseumAdapter
     */
    public function getAdapter(): LaravelMuseumAdapter
    {
        return $this->adapter;
    }

    /**
     * Infer work type from information object properties.
     *
     * @param \QubitInformationObject $object
     *
     * @return string
     */
    private function inferWorkType(\QubitInformationObject $object): string
    {
        // Try to infer from title and scope/content
        $text = strtolower($object->getTitle().' '.$object->scopeAndContent);

        $visualKeywords = ['painting', 'photograph', 'drawing', 'print', 'sculpture'];
        $builtKeywords = ['building', 'architecture', 'monument', 'structure'];
        $movableKeywords = ['object', 'artifact', 'tool', 'vessel', 'textile'];

        foreach ($visualKeywords as $keyword) {
            if (false !== strpos($text, $keyword)) {
                return 'visual_works';
            }
        }

        foreach ($builtKeywords as $keyword) {
            if (false !== strpos($text, $keyword)) {
                return 'built_works';
            }
        }

        foreach ($movableKeywords as $keyword) {
            if (false !== strpos($text, $keyword)) {
                return 'movable_works';
            }
        }

        // Default to visual works
        return 'visual_works';
    }
}
