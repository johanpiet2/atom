<?php

/**
 * Authority Record Report Action - Framework v2.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class reportsReportAuthorityRecordAction extends sfAction
{
    public function execute($request)
    {
        // Authorization check - use Symfony's built-in user methods for now
        if (!$this->hasReportAccess()) {
            QubitAcl::forwardUnauthorized();
        }

        $this->form = $this->createReportForm();
        
        $this->form->bind(
            $request->getRequestParameters() + 
            $request->getGetParameters() + 
            $this->getDefaultParameters()
        );

        if ($this->form->isValid()) {
            $this->executeSearch();
        }
    }

    private function executeSearch(): void
    {
        try {
            $filter = \AtomExtensions\Reports\Filters\ReportFilter::fromForm($this->form);
            $service = $this->getReportService();
            $this->results = $service->search($filter);
            $this->statistics = $service->getStatistics();
        } catch (Exception $e) {
            $this->getReportLogger()->error('Authority report failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->getUser()->setFlash('error', 'Error: ' . $e->getMessage());
        }
    }

    private function getReportService(): \AtomExtensions\Reports\Services\AuthorityRecordReportService
    {
        $repository = new \AtomExtensions\Repositories\ActorRepository();
        $logger = $this->getReportLogger();

        return new \AtomExtensions\Reports\Services\AuthorityRecordReportService(
            $repository,
            $logger
        );
    }

    private function createReportForm(): sfForm
    {
        $form = new sfForm([], [], false);
        $form->getValidatorSchema()->setOption('allow_extra_fields', true);

        \AtomExtensions\Forms\FormFieldFactory::addDateFields($form);
        \AtomExtensions\Forms\FormFieldFactory::addControlFields($form);

        $this->addEntityTypeField($form);

        return $form;
    }

    private function addEntityTypeField(sfForm $form): void
    {
        try {
            $termService = new \AtomExtensions\Services\TermService('en');
            $entityTypes = $termService->getActorEntityTypes();
            $choices = $termService->toChoices($entityTypes, true, 'All types');
        } catch (Exception $e) {
            $this->getReportLogger()->warning('Failed to load entity types', [
                'error' => $e->getMessage(),
            ]);
            $choices = [null => 'All types'];
        }

        $form->setValidator('entityType', new sfValidatorString(['required' => false]));
        $form->setWidget('entityType', new sfWidgetFormSelect(['choices' => $choices]));
    }

    private function getDefaultParameters(): array
    {
        return [
            'className' => 'QubitActor',
            'dateStart' => date('d/m/Y', strtotime('-1 month')),
            'dateEnd' => date('d/m/Y'),
            'dateOf' => 'CREATED_AT',
            'limit' => '20',
            'sort' => 'updatedDown',
            'page' => '1',
        ];
    }

    private function hasReportAccess(): bool
    {
        // Use Symfony's myUser class methods (simpler, works with existing AtoM)
        return $this->context->user->isAdministrator()
            || $this->context->user->isSuperUser()
            || $this->context->user->isAuditUser();
    }

    private function getReportLogger(): \Psr\Log\LoggerInterface
    {
        static $logger = null;

        if ($logger === null) {
            $logger = new \Monolog\Logger('authority-reports');
            $logger->pushHandler(
                new \Monolog\Handler\StreamHandler(
                    sfConfig::get('sf_log_dir', '/var/log/atom') . '/atom-reports.log',
                    \Monolog\Logger::INFO
                )
            );
        }

        return $logger;
    }
}
