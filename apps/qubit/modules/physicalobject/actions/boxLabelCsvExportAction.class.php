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

class PhysicalObjectBoxLabelCsvExportAction extends sfAction
{
  public function execute($request)
  {
    if (!isset($request->limit))
    {
      $request->limit = sfConfig::get('app_hits_per_page');
    }

    $criteria = new Criteria;
    $criteria->addJoin(QubitPhysicalObject::ID, QubitPhysicalObjectI18n::ID);
    $criteria->add(QubitPhysicalObjectI18n::CULTURE, $this->context->user->getCulture());
    $criteria->add(QubitPhysicalObjectI18n::NAME, "$request->query%", Criteria::LIKE);

    $criteria->addAscendingOrderByColumn(QubitPhysicalObjectI18n::NAME);

    $this->pager = new QubitPager('QubitPhysicalObject');
    $this->pager->setCriteria($criteria);
    //$this->pager->setMaxPerPage($request->limit);
    //$this->pager->setPage(1);

    $this->physicalObjects = $this->pager->getResults();
    
    // Use php://temp stream, max 2M
    $csv = fopen('php://temp/maxmemory:'. (2*1024*1024), 'r+');

    // Write CSV header
    fputcsv($csv, array('Name', 'Location', 'Unique Identifier', 'Description/Title','Period Covered','Extend','Finding Aids','Accrual Space','Forms','Type'));

    foreach ($this->physicalObjects as $item)
    {
      // Write reference code, container name, title, creation dates
        fputcsv($csv, array($item->name,$item->location,$item->uniqueIdentifier,$item->descriptionTitle,$item->periodCovered,$item->extend,$item->findingAids,$item->accrualSpace,$item->forms,$item->type));
       
    }

    // Rewind the position of the pointer
    rewind($csv);

    // Disable layout
    $this->setLayout(false);

    // Set the file name
    $this->getResponse()->setHttpHeader('Content-Disposition', "attachment; filename=report.csv");

    // Send $csv content as the response body
    $this->getResponse()->setContent(stream_get_contents($csv));
    
	fclose($csv);
    
    return sfView::NONE;
  }
}
