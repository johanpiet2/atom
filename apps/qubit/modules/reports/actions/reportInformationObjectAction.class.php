<?php

/**
 * Information Object Report Action - Framework v2.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class reportsReportInformationObjectAction extends sfAction
{
    public function execute($request)
    {
        // Authorization check
        if (!$this->hasReportAccess()) {
            QubitAcl::forwardUnauthorized();
        }

        // Create and configure form
        $this->form = $this->createReportForm();
        
        // Bind request parameters with defaults
        $this->form->bind(
            $request->getRequestParameters() + 
            $request->getGetParameters() + 
            $this->getDefaultParameters()
        );

        // Execute search if form is valid
        if ($this->form->isValid()) {
            $this->executeSearch();
        }
    }

    private function executeSearch(): void
    {
        try {
            $filter = \AtomExtensions\Reports\Filters\ReportFilter::fromForm($this->form);
            $service = $this->getReportService();
            $searchResults = $service->search($filter);

            $this->results = $searchResults['results'];
            $this->total = $searchResults['total'];
            $this->statistics = $service->getStatistics();
            
        } catch (Exception $e) {
            $this->getReportLogger()->error('Information object report failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->getUser()->setFlash('error', 'Error: ' . $e->getMessage());
        }
    }

    private function getReportService(): \AtomExtensions\Reports\Services\InformationObjectReportService
    {
        $repository = new \AtomExtensions\Repositories\InformationObjectRepository();
        $termService = new \AtomExtensions\Services\TermService('en');
        $logger = $this->getReportLogger();

        return new \AtomExtensions\Reports\Services\InformationObjectReportService(
            $repository,
            $termService,
            $logger
        );
    }

    private function createReportForm(): sfForm
    {
        $form = new sfForm([], [], false);
        $form->getValidatorSchema()->setOption('allow_extra_fields', true);

        \AtomExtensions\Forms\FormFieldFactory::addDateFields($form);
        \AtomExtensions\Forms\FormFieldFactory::addControlFields($form);

        $this->addLevelOfDescriptionField($form);
        $this->addPublicationStatusField($form);

        return $form;
    }

    private function addLevelOfDescriptionField(sfForm $form): void
    {
        $choices = ['' => 'All levels'];

        try {
            $termService = new \AtomExtensions\Services\TermService('en');
            $levels = $termService->getLevelsOfDescription();
            
            foreach ($levels as $level) {
                $choices[$level->id] = $level->name;
            }
        } catch (Exception $e) {
            $this->getReportLogger()->warning('Failed to load levels', [
                'error' => $e->getMessage(),
            ]);
        }

        $form->setValidator('levelOfDescription', new sfValidatorChoice([
            'choices' => array_keys($choices),
            'required' => false
        ]));
        $form->setWidget('levelOfDescription', new sfWidgetFormSelect([
            'choices' => $choices
        ]));
    }

    private function addPublicationStatusField(sfForm $form): void
    {
        $choices = ['' => 'All statuses'];

        try {
            $termService = new \AtomExtensions\Services\TermService('en');
            $statuses = $termService->getPublicationStatuses();
            
            foreach ($statuses as $status) {
                $choices[$status->id] = $status->name;
            }
        } catch (Exception $e) {
            $this->getReportLogger()->warning('Failed to load statuses', [
                'error' => $e->getMessage(),
            ]);
        }

        $form->setValidator('publicationStatus', new sfValidatorChoice([
            'choices' => array_keys($choices),
            'required' => false
        ]));
        $form->setWidget('publicationStatus', new sfWidgetFormSelect([
            'choices' => $choices
        ]));
    }

    private function getDefaultParameters(): array
    {
        return [
            'className' => 'QubitInformationObject',
            'dateStart' => date('Y-m-d', strtotime('-1 month')),
            'dateEnd' => date('Y-m-d'),
            'dateOf' => 'CREATED_AT',
            'limit' => '20',
            'sort' => 'updatedDown',
            'page' => '1',
        ];
    }

    private function hasReportAccess(): bool
    {
        return $this->context->user->isAdministrator()
            || $this->context->user->isSuperUser()
            || $this->context->user->isAuditUser();
    }

    private function getReportLogger(): \Psr\Log\LoggerInterface
    {
        static $logger = null;

        if ($logger === null) {
            $logger = new \Monolog\Logger('informationobject-reports');
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
