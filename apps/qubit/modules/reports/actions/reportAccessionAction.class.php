<?php

/**
 * Accession Report Action - Framework v2.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class reportsReportAccessionAction extends sfAction
{
    public function execute($request)
    {
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
            $searchResults = $service->search($filter);

            $this->results = $searchResults['results'];
            $this->total = $searchResults['total'];
            $this->statistics = $service->getStatistics();
            
        } catch (Exception $e) {
            $this->getReportLogger()->error('Accession report failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->getUser()->setFlash('error', 'Error: ' . $e->getMessage());
            error_log('Accession Report Error: ' . $e->getMessage());
        }
    }

    private function getReportService(): \AtomExtensions\Reports\Services\AccessionReportService
    {
        $repository = new \AtomExtensions\Repositories\AccessionRepository();
        $termService = new \AtomExtensions\Services\TermService('en');
        $logger = $this->getReportLogger();

        return new \AtomExtensions\Reports\Services\AccessionReportService(
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
        
        $this->addCultureField($form);

        return $form;
    }

    private function addCultureField(sfForm $form): void
    {
        $repository = new \AtomExtensions\Repositories\AccessionRepository();
        $cultures = $repository->getAvailableCultures();
        
        $choices = [];
        foreach ($cultures as $culture) {
            $choices[$culture] = $this->getCultureLabel($culture);
        }
        
        $form->setValidator('culture', new sfValidatorChoice([
            'choices' => array_keys($choices),
            'required' => false
        ]));
        
        $form->setWidget('culture', new sfWidgetFormSelect([
            'choices' => $choices
        ]));
    }

    private function getCultureLabel(string $code): string
    {
        $labels = [
            'en' => 'English',
            'af' => 'Afrikaans',
            'zu' => 'Zulu',
        ];
        
        return $labels[$code] ?? strtoupper($code);
    }

    private function getDefaultParameters(): array
    {
        return [
            'className' => 'QubitAccession',
            'dateStart' => date('Y-m-d', strtotime('-1 year')),
            'dateEnd' => date('Y-m-d'),
            'dateOf' => 'CREATED_AT',
            'culture' => 'en',
            'limit' => '20',
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
            $logger = new \Monolog\Logger('accession-reports');
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
