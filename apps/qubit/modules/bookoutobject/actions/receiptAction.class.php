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

class BookoutObjectReceiptAction extends sfAction
{

    public function execute($request)
    {
        if (!isset($request->limit)) {
            $request->limit = sfConfig::get('app_hits_per_page');
        }
        
        $this->resource = $this->getRoute()->resource;
        
        $criteria = new Criteria;
		BaseBookoutObject::addSelectColumns($criteria);
		BaseBookoutObjectI18n::addSelectColumns($criteria);
        $criteria->addJoin(QubitBookoutObject::ID, QubitBookoutObjectI18n::ID);
        $criteria->add(QubitBookoutObject::ID, $request->source);
        
        $this->bookOutObject = self::doSelect($criteria);

	    $this->pager = new QubitPager('QubitBookoutObject');
	    $this->pager->setCriteria($criteria);
	    $this->pager->setMaxPerPage(1);
	    $this->pager->setPage(1);

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
}
