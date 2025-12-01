<?php

declare(strict_types=1);

/**
 * arSpectrum51PluginGrap Configuration.
 *
 * GRAP extension for Spectrum 5.1 collections management.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class arSpectrum51PluginGrapConfiguration extends sfPluginConfiguration
{
    /**
     * Initialize plugin.
     */
    public function initialize(): void
    {
        // Register routes
        $this->dispatcher->connect('routing.load_configuration', [$this, 'routingLoadConfiguration']);

        // Autoload service classes
        $this->registerAutoload();
    }

    /**
     * Load routing configuration.
     */
    public function routingLoadConfiguration(sfEvent $event): void
    {
        $routing = $event->getSubject();

        // Load plugin routes before application routes
        $routing->prependRoute('grap_report', new sfRoute(
            '/grap/report',
            ['module' => 'grapReport', 'action' => 'index']
        ));

        $routing->prependRoute('grap_report_export', new sfRoute(
            '/grap/report/export',
            ['module' => 'grapReport', 'action' => 'export']
        ));

        $routing->prependRoute('grap_compliance_check', new sfRoute(
            '/grap/compliance',
            ['module' => 'grapReport', 'action' => 'complianceCheck']
        ));

        $routing->prependRoute('grap_103_disclosure', new sfRoute(
            '/grap/disclosure/103',
            ['module' => 'grapReport', 'action' => 'grap103Disclosure']
        ));
    }

    /**
     * Register autoload paths.
     */
    protected function registerAutoload(): void
    {
        $libDir = $this->getRootDir() . '/lib';

        // Register Services directory
        if (is_dir($libDir . '/Services')) {
            foreach (glob($libDir . '/Services/*.php') as $file) {
                require_once $file;
            }
        }

        // Register form classes
        if (is_dir($libDir . '/form')) {
            foreach (glob($libDir . '/form/*.php') as $file) {
                require_once $file;
            }
        }

        // Register model classes
        if (is_dir($libDir . '/model')) {
            foreach (glob($libDir . '/model/*.php') as $file) {
                require_once $file;
            }
        }
    }
}
