<?php

/**
 * Minimal Plugin Shell - Delegates to Framework v2.
 */
class SettingsMetadataExtractionAction extends sfAction
{
    public function execute($request)
    {
        $controller = new \AtomExtensions\Extensions\MetadataExtraction\Controllers\SettingsController();
        $controller->handleSettings($request, $this);
    }
}
