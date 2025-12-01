<?php

/**
 * arSpectrumPlugin Configuration.
 *
 * Implements Spectrum 5.0 museum collections management procedures.
 * Uses Laravel Query Builder for database operations.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class arSpectrumPluginConfiguration extends sfPluginConfiguration
{
    public static $summary = 'Spectrum 5.0 Collections Management Procedures';
    public static $version = '1.0.0';

    public function initialize()
    {
        // Register event listeners
        $this->dispatcher->connect('information_object.post_save', [$this, 'onInformationObjectSave']);
        $this->dispatcher->connect('information_object.post_delete', [$this, 'onInformationObjectDelete']);
    }

    public function onInformationObjectSave(sfEvent $event)
    {
        // Could trigger cataloguing procedure update
    }

    public function onInformationObjectDelete(sfEvent $event)
    {
        // Could trigger deaccession procedure
    }
}
