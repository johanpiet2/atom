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
 * Qubit specifc extension to the sfPropelPager
 *
 * @package AccesstoMemory
 * @author  David Juhasz <david@artefactual.com>
 */
class QubitPagerAudit extends sfPropelPagerAudit
{
    protected 
    // Override sfPager::$nbResults = 0
        $nbResults = null;
    
    public static function addSelectColumns(Criteria $criteria)
    {
        $criteria->addSelectColumn(QubitAuditObject::ID);
        $criteria->addSelectColumn(QubitAuditObject::RECORD_ID);
        $criteria->addSelectColumn(QubitAuditObject::FIELD_KEY);
        $criteria->addSelectColumn(QubitAuditObject::ACTION);
        $criteria->addSelectColumn(QubitAuditObject::DB_TABLE);
        $criteria->addSelectColumn(QubitAuditObject::DB_QUERY);
        $criteria->addSelectColumn(QubitAuditObject::USER);
        $criteria->addSelectColumn(QubitAuditObject::ACTION_DATE_TIME);
        
        return $criteria;
    }
    
    public static function get(Criteria $criteria, array $options = array())
    {
        if (!isset($options['connection'])) {
            $options['connection'] = Propel::getConnection(QubitAuditObject::DATABASE_NAME);
        }
        
        self::addSelectColumns($criteria);
        
        return QubitQuery::createFromCriteria($criteria, 'QubitAuditObject', $options);
    }
    
    /**
     * BasePeer::doCount() returns PDOStatement
     */
    public function doCount(Criteria $criteria)
    {
        //call_user_func(array($this->class, BaseAuditObject::addSelectColumns), $criteria);
        
        return BasePeer::doCount($criteria)->fetchColumn(0);
    }
    
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        $dbMap = Propel::getDatabaseMap($criteria->getDbName());
        $db    = Propel::getDB($criteria->getDbName());
        
        if ($con === null) {
            $con = Propel::getConnection($criteria->getDbName(), Propel::CONNECTION_READ);
        }
        
        $stmt = null;
        
        if ($criteria->isUseTransaction())
            $con->beginTransaction();
        
        try {
            
            $params = array();
            $sql    = BasePeer::createSelectSql($criteria, $params);
            
            $stmt = $con->prepare($sql);
            BasePeer::populateStmtValues($stmt, $params, $dbMap, $db);
            
            $stmt->execute();
            
            if ($criteria->isUseTransaction())
                $con->commit();
            
        }
        catch (Exception $e) {
            if ($stmt)
                $stmt = null; // close
            if ($criteria->isUseTransaction())
                $con->rollBack();
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException($e);
        }
        
        return $stmt;
    }
    
    /**
     * @see sfPropelPager
     */
    public function getClassPeer()
    {
        return $this;
    }
    
    /**
     * Override ::getNbResults() to call ->init() first
     *
     * @see sfPager
     */
    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->init();
        }
        
        return parent::getNbResults();
    }
    
    /**
     * Override ::getResults() to call ->init() first
     *
     * @see sfPager
     */
    public function getResults()
    {
        $this->init();
        
        return parent::getResults();
    }
	
	/**
	* Similar to getResults but gets raw row data, not objects
	*
	* Columns need to be selected using the criteria
	*
	* Example: $criteria->addSelectColumn(QubitInformationObject::ID);
	* 
	*/
	public function getRows(Criteria $criteria)
	{
		$this->init();

		$class = $this->class;

		$options = array();
		$options['connection'] = Propel::getConnection($class::DATABASE_NAME);
		$options['rows'] = true;

		return QubitQuery::createFromCriteria($criteria, $this->class, $options);
	}
	
}