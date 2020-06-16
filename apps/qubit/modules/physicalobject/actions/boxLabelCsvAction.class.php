<?php

/*
 * This file is part of the AccesstoMemory (AtoM) software.
 *
 * AccesstoMemory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
  *
 * AccesstoMemory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AccesstoMemory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 * Johan Pieterse SITA 30 May 2014
 */

class PhysicalObjectBoxLabelCsvAction extends sfAction
{
  public function execute($request)
  {
    if (!isset($request->limit))
    {
      $request->limit = sfConfig::get('app_hits_per_page');
    }
    
	//get cookie variable
	$FilterVar = $_COOKIE["strongroom"]; //''
	$FilterVarLoc = $_COOKIE["strongroom2"]; //''

    $criteria = new Criteria;
    $criteria->addJoin(QubitPhysicalObject::ID, QubitPhysicalObjectI18n::ID);
    $criteria->add(QubitPhysicalObjectI18n::CULTURE, $this->context->user->getCulture());
    if ($FilterVar == 'All')
    {
    	$criteria->add(QubitPhysicalObjectI18n::NAME, '%' , Criteria::LIKE);
    }
    else
    {
    	$criteria->add(QubitPhysicalObjectI18n::NAME, $FilterVar , Criteria::LIKE);
	}
    if ($FilterVarLoc == 'All')
    {
    	$criteria->add(QubitPhysicalObjectI18n::LOCATION, '%' , Criteria::LIKE);
    }
    else
    {
    	$criteria->add(QubitPhysicalObjectI18n::LOCATION, $FilterVarLoc , Criteria::LIKE);
	}
    $criteria->addAscendingOrderByColumn(QubitPhysicalObjectI18n::NAME);

    $this->pager = new QubitPager('QubitPhysicalObject');
    $this->pager->setCriteria($criteria);
    $this->pager->setMaxPerPage($request->limit);
    $this->pager->setPage(1);

    $this->physicalObjects = $this->pager->getResults();
    
	$c2 = clone $criteria;
    $this->foundcount = BasePeer::doCount($c2)->fetchColumn(0);    
  }
}
