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
 * Researcher functionallity
 *
 * @package    AccesstoMemory
 * @subpackage 
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class researcherIndexAction extends sfAction
{
    public function execute($request)
    {
        $this->resource = QubitResearcher::getById($this->request->source);
         
        //get ROOT instance to filter on all researchers not per single researcher
        if ($this->resource instanceOf QubitResearcher) {
            $this->researcher = QubitResearcher::getById(QubitResearcher::ROOT_ID);
        }
        // Check user authorization
        if (!QubitAcl::check($this->researcher, 'read')) {
            QubitAcl::forwardUnauthorized();
        }
        
        if (1 > strlen($title = $this->resource->__toString())) {
            $title = $this->context->i18n->__('Untitled');
        }
        $this->response->setTitle("$title - {$this->response->getTitle()}");
        if (QubitAcl::check($this->researcher, 'create')) {
            $validatorSchema = new sfValidatorSchema;
            $values          = array();
            
            $validatorSchema->authorizedFormOfName = new sfValidatorString(array(
                'required' => true
            ), array(
                'required' => $this->context->i18n->__('Authorized form of name - This is a mandatory element.')
            ));
            $values['authorizedFormOfName']        = $this->resource->getAuthorizedFormOfName(array(
                'cultureFallback' => true
            ));
            
            try {
                $validatorSchema->clean($values);
            }
            catch (sfValidatorErrorSchema $e) {
                $this->errorSchema = $e;
            }
        }
    }
}