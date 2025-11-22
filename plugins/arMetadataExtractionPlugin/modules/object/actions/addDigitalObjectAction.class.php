<?php

/**
 * Minimal Plugin Shell - Delegates to Framework v2.
 *
 * This is just a thin wrapper. All logic is in Framework v2.
 */
class objectAddDigitalObjectAction extends sfAction
{
    public function execute($request)
    {
        $controller = new \AtomExtensions\Extensions\MetadataExtraction\Controllers\DigitalObjectController();
        $controller->handleUpload($request, $this);
    }
}
