<?php

declare(strict_types=1);

namespace AtomExtensions\Extensions\MetadataExtraction\Controllers;

use AtomExtensions\Extensions\MetadataExtraction\MetadataExtractionAdapter;

/**
 * Digital Object Controller.
 *
 * Handles digital object upload and metadata extraction.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class DigitalObjectController
{
    /**
     * Handle digital object upload.
     */
    public function handleUpload($request, $action): void
    {
        $resource = $action->getRoute()->resource;

        if (!$request->isMethod('post')) {
            return;
        }

        try {
            $file = $request->getFiles('file');
            
            if (!$file || !isset($file['tmp_name'])) {
                $action->getUser()->setFlash('error', 'No file uploaded');
                return;
            }

            // Create digital object using AtoM's native functionality
            $digitalObject = new \QubitDigitalObject();
            $digitalObject->usageId = \QubitTerm::MASTER_ID;
            $digitalObject->assets[] = new \QubitAsset($file['name'], $file['tmp_name']);
            $digitalObject->informationObjectId = $resource->id;
            $digitalObject->save();

            // Extract metadata using Framework v2
            if ($this->shouldExtractMetadata()) {
                $this->extractAndApplyMetadata($digitalObject->id, $resource);
            }

            $action->getUser()->setFlash('notice', 'Digital object uploaded successfully');
            $action->redirect([$resource, 'module' => 'informationobject']);

        } catch (\Exception $e) {
            error_log('Digital object upload failed: ' . $e->getMessage());
            $action->getUser()->setFlash('error', $e->getMessage());
        }
    }

    /**
     * Extract and apply metadata.
     */
    private function extractAndApplyMetadata(int $digitalObjectId, $informationObject): void
    {
        try {
            // Extract metadata using Framework v2 adapter
            $metadata = MetadataExtractionAdapter::extract($digitalObjectId, true);

            if (empty($metadata)) {
                return;
            }

            // Apply to information object
            $this->applyMetadataToInformationObject($informationObject, $metadata);

        } catch (\Exception $e) {
            error_log('Metadata extraction failed: ' . $e->getMessage());
        }
    }

    /**
     * Apply extracted metadata to information object.
     */
    private function applyMetadataToInformationObject($io, array $metadata): void
    {
        $settings = $this->getSettings();

        // Update title
        if ($settings['overwrite_title'] || empty($io->getTitle(['sourceCulture' => true]))) {
            $title = $this->extractTitle($metadata);
            if ($title) {
                $io->setTitle($title);
            }
        }

        // Update description
        if ($settings['overwrite_description'] || empty($io->getScopeAndContent(['sourceCulture' => true]))) {
            $description = $this->extractDescription($metadata);
            if ($description) {
                $io->setScopeAndContent($description);
            }
        }

        // Add technical metadata
        if ($settings['add_technical_metadata']) {
            $technical = $this->buildTechnicalMetadata($metadata);
            if ($technical) {
                $this->addToField($io, $settings['technical_metadata_target_field'], $technical);
            }
        }

        // Extract GPS coordinates
        if ($settings['extract_gps_coordinates']) {
            $this->extractGpsCoordinates($io, $metadata);
        }

        $io->save();
    }

    /**
     * Extract title from metadata.
     */
    private function extractTitle(array $metadata): ?string
    {
        $titleFields = [
            'IPTC:Headline',
            'IPTC:ObjectName',
            'XMP:Title',
            'EXIF:ImageDescription',
            'File:FileName',
        ];

        foreach ($titleFields as $field) {
            if (isset($metadata[$field]) && !empty($metadata[$field])) {
                return trim($metadata[$field]);
            }
        }

        return null;
    }

    /**
     * Extract description from metadata.
     */
    private function extractDescription(array $metadata): ?string
    {
        $descriptionFields = [
            'IPTC:Caption-Abstract',
            'XMP:Description',
            'EXIF:UserComment',
        ];

        foreach ($descriptionFields as $field) {
            if (isset($metadata[$field]) && !empty($metadata[$field])) {
                return trim($metadata[$field]);
            }
        }

        return null;
    }

    /**
     * Build technical metadata string.
     */
    private function buildTechnicalMetadata(array $metadata): string
    {
        $technical = [];

        if (isset($metadata['File:ImageWidth']) && isset($metadata['File:ImageHeight'])) {
            $technical[] = "Dimensions: {$metadata['File:ImageWidth']} x {$metadata['File:ImageHeight']} pixels";
        }

        if (isset($metadata['File:FileType'])) {
            $technical[] = "Format: {$metadata['File:FileType']}";
        }

        if (isset($metadata['EXIF:ColorSpace'])) {
            $technical[] = "Color Space: {$metadata['EXIF:ColorSpace']}";
        }

        if (isset($metadata['EXIF:Make']) && isset($metadata['EXIF:Model'])) {
            $technical[] = "Camera: {$metadata['EXIF:Make']} {$metadata['EXIF:Model']}";
        }

        if (isset($metadata['EXIF:DateTimeOriginal'])) {
            $technical[] = "Date Taken: {$metadata['EXIF:DateTimeOriginal']}";
        }

        if (isset($metadata['File:FileSize'])) {
            $technical[] = "File Size: {$metadata['File:FileSize']}";
        }

        return implode("\n", $technical);
    }

    /**
     * Extract GPS coordinates.
     */
    private function extractGpsCoordinates($io, array $metadata): void
    {
        $lat = $metadata['EXIF:GPSLatitude'] ?? null;
        $lon = $metadata['EXIF:GPSLongitude'] ?? null;

        if ($lat && $lon) {
            // Store as property or in appropriate field
            $coordinates = "GPS: {$lat}, {$lon}";
            $this->addToField($io, 'locationInformation', $coordinates);
        }
    }

    /**
     * Add content to information object field.
     */
    private function addToField($io, string $field, string $content): void
    {
        $method = 'get' . ucfirst($field);
        $setMethod = 'set' . ucfirst($field);

        if (!method_exists($io, $method)) {
            return;
        }

        $existing = $io->$method(['sourceCulture' => true]);
        
        if ($existing) {
            $io->$setMethod($existing . "\n\n" . $content);
        } else {
            $io->$setMethod($content);
        }
    }

    /**
     * Check if metadata extraction is enabled.
     */
    private function shouldExtractMetadata(): bool
    {
        return (bool) $this->getSetting('metadata_extraction_enabled', true);
    }

    /**
     * Get all settings.
     */
    private function getSettings(): array
    {
        return [
            'overwrite_title' => (bool) $this->getSetting('overwrite_title', false),
            'overwrite_description' => (bool) $this->getSetting('overwrite_description', false),
            'add_technical_metadata' => (bool) $this->getSetting('add_technical_metadata', true),
            'technical_metadata_target_field' => $this->getSetting('technical_metadata_target_field', 'physicalCharacteristics'),
            'extract_gps_coordinates' => (bool) $this->getSetting('extract_gps_coordinates', true),
            'auto_generate_keywords' => (bool) $this->getSetting('auto_generate_keywords', true),
            'extract_exif' => (bool) $this->getSetting('extract_exif', true),
            'extract_iptc' => (bool) $this->getSetting('extract_iptc', true),
            'extract_xmp' => (bool) $this->getSetting('extract_xmp', true),
        ];
    }

    /**
     * Get setting value.
     */
    private function getSetting(string $name, $default = null)
    {
        $setting = \QubitSetting::getByNameAndScope($name, 'metadata_extraction');
        
        if (!$setting) {
            return $default;
        }

        $value = $setting->getValue(['sourceCulture' => true]);

        if ($value === '1') {
            return true;
        }
        if ($value === '0') {
            return false;
        }

        return $value ?: $default;
    }
}
