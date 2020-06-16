<?php
/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * Display a list of recently updates to the db
 *
 * @package AccesstoMemory
 * @subpackage search
 * @author Johan Pieterse <johan.pieterse@sita.co.za>
 */
class reportsReportDeletedAction extends sfAction
{
    public static $NAMES = array('className', 'dateStart', 'dateEnd', 'dateOf', 'publicationStatus', 'limit', 'levelOfDescription', 
    'repositories', // 'repository',
    'registries', // 'registers',
    'authorityRecords', // 'Client office, legal deposit and donor',
    'collection', // 'fonds/collection'
    'condition', 
    'formats', 
    'physicalStorage', 
    'sort',
	'usability',
	'measure',
	'availability',
	'refusal',
	'restoration',
	'conservation',
	'type',
	'sensitivity',
	'publish',
	'classification',
	'cbAuditTrail');
	
    protected function addField($name)
    {
        switch ($name) {
        case 'limit':
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormInputHidden);

            break;

        case 'sort':
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormInputHidden);

            break;
        }
    }
    public function execute($request)
    {
		// Check authorization
		if ((!$this->context->user->isAdministrator()) && (!$this->context->user->isSuperUser()) && (!$this->context->user->isAuditUser())) {
			QubitAcl::forwardUnauthorized();
		}

        $this->form = new sfForm;
        $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);
        foreach ($this::$NAMES as $name) {
            $this->addField($name);
        }
        $defaults = array(
		    'className' => 'QubitAuditObject', 
		    'dateStart' => date('Y-m-d', strtotime('-1 month')), 
		    'dateEnd' => date('Y-m-d'), 'dateOf' => 'CREATED_AT', 
		    'publicationStatus' => 'all', 
		    'limit' => '10', 
		    'sort' => 'updatedDown');
        
        $this->form->bind($request->getRequestParameters() + $request->getGetParameters() + $defaults);
	    if ($this->form->isValid()) {
	        $this->className = $this->form->getValue('className');
	        $this->doSearch();
	    }
	}
	
    public function doSearch()
    {
        $criteria = new Criteria;
        $this->object_id = $this->request->getParameter('source');
        $this->sort = $this->request->getParameter('sort', 'updatedDown');
        
        $criteria->add(QubitAuditObject::RECORD_ID, $this->object_id, Criteria::EQUAL);

	    // Add sort criteria
	    switch ($this->sort) {
	    case 'nameDown':
	        $criteria->addDescendingOrderByColumn($nameColumn);
	        break;

	    case 'nameUp':
	        $criteria->addAscendingOrderByColumn($nameColumn);
	        break;

	    case 'updatedUp':
	        $criteria->addAscendingOrderByColumn(QubitObject::UPDATED_AT);
	        break;

	    case 'updatedDown':
	    default:
	        $criteria->addDescendingOrderByColumn(QubitObject::UPDATED_AT);
	    }
	    // Add fallback criteria for name
	    if ('nameDown' == $this->sort || 'nameUp' == $this->sort) {
	        $criteria = QubitCultureFallback::addFallbackCriteria($criteria, $this->form->getValue('className'));
	    }

	    $this->pager = new QubitPager('QubitAuditObject');
	    $this->pager->setCriteria($criteria);
	    $this->pager->setMaxPerPage($this->form->getValue('limit'));
	    $this->pager->setPage($this->request->getParameter('page', 1));
	    
        $criteria = new Criteria;
        $this->object_id = $this->request->getParameter('source');
        $this->sort = $this->request->getParameter('sort', 'updatedDown');
        
        $criteria->add(QubitAuditObject::RECORD_ID, $this->object_id, Criteria::EQUAL);
		BaseAuditObject::addSelectColumns($criteria);
	    $this->auditDeletedObjects = self::doSelect($criteria);
    }
    
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$dbMap = Propel::getDatabaseMap($criteria->getDbName());
		$db = Propel::getDB($criteria->getDbName());

		if ($con === null) {
			$con = Propel::getConnection($criteria->getDbName(), Propel::CONNECTION_READ);
		}

		$stmt = null;

		if ($criteria->isUseTransaction()) $con->beginTransaction();

		try {

			$params = array();
			$sql = BasePeer::createSelectSql($criteria, $params);

			$stmt = $con->prepare($sql);
			BasePeer::populateStmtValues($stmt, $params, $dbMap, $db);

			$stmt->execute();

			if ($criteria->isUseTransaction()) $con->commit();

		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			if ($criteria->isUseTransaction()) $con->rollBack();
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}

		return $stmt;
	}
    
    
}

