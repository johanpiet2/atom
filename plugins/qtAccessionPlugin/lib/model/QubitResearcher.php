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
 * @package    AccesstoMemory
 * @subpackage serviceprovider
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */

class QubitResearcher extends BaseResearcher
{
	const ROOT_ID = 20;

    public static function get(Criteria $criteria, array $options = array())
    {
        if (!isset($options['connection'])) {
            $options['connection'] = Propel::getConnection(QubitResearcher::DATABASE_NAME);
        }
        
        self::addSelectColumns($criteria);
        
        return QubitQuery::createFromCriteria($criteria, 'QubitResearcher', $options);
    }
    
    /**
     * Get an array of QubitRepository objects where the current user has been
     * added explicit access via its own user or any of its groups
     *
     * JJP SITA One Instance
     * @return array of repository id's linked to user
     */
    public function getRepositoriesById($rID, array $options = array())
    {
        // Get access control permissions
        $criteria = new Criteria;
        $criteria->add(QubitResearcher::REPOSITORY_ID, $rID, Criteria::EQUAL);
        if (0 != count($query = self::get($criteria, $options))) {
            return $query;
        }
    }

  /**
   * Only find Researcher objects, not other types
   *
   * @param Criteria $criteria current search criteria
   * @return Criteria modified search critieria
   */
  public static function addGetOnlyResearcherObjectCriteria($criteria)
  {
    $criteria->addJoin(QubitResearcher::ID, QubitObject::ID);
    $criteria->add(QubitObject::CLASS_NAME, 'QubitResearcher');

    return $criteria;
  }

  public function __construct()
  {
    parent::__construct();

    $this->tables[] = Propel::getDatabaseMap(QubitResearcher::DATABASE_NAME)->getTable(QubitResearcher::TABLE_NAME);
  }
}