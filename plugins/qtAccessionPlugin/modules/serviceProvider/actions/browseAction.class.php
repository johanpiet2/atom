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
 *
 * Show paginated list of service provider.
 *
 * @package    AccesstoMemory
 * @subpackage serviceprovider
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */

class serviceProviderBrowseAction extends sfAction
{
    public function execute($request)
    {
        if (!$this->context->user->hasCredential(array(
            'serviceprovider',
            'administrator'
        ), false)) {
            QubitAcl::forwardUnauthorized();
        }
        
        // Check user authorization TO DO - remove Add New if user does not have rights
        //get ROOT instance to filter on all serviceprovider not per single serviceprovider
        $this->serviceprovider = QubitServiceProvider::getById(QubitServiceProvider::ROOT_ID);
        
        if (!isset($request->limit)) {
            $request->limit = sfConfig::get('app_hits_per_page');
        }
        
        if (!isset($request->sort)) {
            if ($this->getUser()->isAuthenticated()) {
                $request->sort = sfConfig::get('app_sort_browser_user');
            } else {
                $request->sort = sfConfig::get('app_sort_browser_anonymous');
            }
        }
        
        $criteria = new Criteria;
        
        // Do source culture fallback
        $criteria = QubitCultureFallback::addFallbackCriteria($criteria, 'QubitActor');
        //do not get root Service Provider - one instance
        $criteria->add(QubitServiceProvider::ID, 21, Criteria::NOT_EQUAL);
        
        if (isset($request->subquery)) {
            $criteria->addJoin(QubitServiceProvider::ID, QubitActorI18n::ID);
            $criteria->add(QubitActorI18n::CULTURE, $this->context->user->getCulture());
            $criteria->add(QubitActorI18n::AUTHORIZED_FORM_OF_NAME, "%$request->subquery%", Criteria::LIKE);
        }
        
        // Show only Repositories linked to user - Administrator can see all - One Instance
        // Start
        if ((!$this->context->user->isAdministrator()) && (QubitSetting::getByName('open_system') == '0')) {
            $repositories = new QubitUser;
            if (0 < count($userRepos = $repositories->getRepositoriesById($this->context->user->getAttribute('user_id')))) {
                // Combined subquery
                foreach ($userRepos as $userRepo) {
                    $criteria->addOR(QubitServiceProvider::REPOSITORY_ID, $userRepo, Criteria::EQUAL);
                }
            } else {
                // if not logged in do not show any repositories
                $criteria->add(QubitServiceProvider::REPOSITORY_ID, '0000', Criteria::EQUAL);
            }
        }
        
        switch ($request->sort) {
            case 'alphabetic':
                $criteria->addAscendingOrderByColumn('authorized_form_of_name');
                
                break;
            
            case 'lastUpdated':
            default:
                $criteria->addDescendingOrderByColumn(QubitObject::UPDATED_AT);
                
                break;
        }
        
        // Page results
        $this->pager = new QubitPager('QubitServiceProvider');
        $this->pager->setCriteria($criteria);
        $this->pager->setMaxPerPage($request->limit);
        $this->pager->setPage($request->page);
    }
}
