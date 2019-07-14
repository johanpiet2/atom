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
 * Physical Object edit
 *
 * @package    AccesstoMemory
 * @subpackage physicalobject
 * @author     David Juhasz <david@artefactual.com>
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class PhysicalObjectEditAction extends DefaultEditAction
{
  public static
    $NAMES = array(
      'location',
      'repositoryId',
      'name',
	  'uniqueIdentifier',	  
	  'extent',	  
 	  'descriptionTitle',
	  'periodCovered',
	  'findingAids',
	  'accrualSpace', 
	  'shelf', 
	  'rowNumber', 	  
	  'forms',
      'type');

  protected function earlyExecute()
  {
    $this->resource = new QubitPhysicalObject;
    if (isset($this->getRoute()->resource))
    {
      $this->resource = $this->getRoute()->resource;
    }

    $title = $this->context->i18n->__('Add new physical storage');
    if (isset($this->getRoute()->resource))
    {
      if (1 > strlen($title = $this->resource->__toString()))
      {
        $title = $this->context->i18n->__('Untitled');
      }

      $title = $this->context->i18n->__('Edit %1%', array('%1%' => $title));
    }

    $this->response->setTitle("$title - {$this->response->getTitle()}");
  }

  protected function addField($name)
  {
    switch ($name)
    {
      case 'repositoryId':
 		$choices = array();
        // Show only Repositories linked to user - Administrator can see all JJP SITA One Instance
        if ((!$this->context->user->isAdministrator()) && (QubitSetting::getByName('open_system') == '0')) {
			$repositories = new QubitUser;
			foreach (QubitRepository::getAll() as $item)
			{
				if ($item->__toString() != "")
				{
				    if (0 < count($userRepos = $repositories->getRepositoriesById($this->context->user->getAttribute('user_id')))) {
				    	$key = array_search($item->id, $userRepos);
				    	if (false !== $key) {
							$choices[$item->id] = $item->__toString();
						}
					}
				}
			}
		} else {
			foreach (QubitRepository::getAll() as $item)
			{
				if ($item->__toString() != "")
				{
					$choices[$item->id] = $item->__toString();
				}
			}
		} 				
		
        $this->form->setDefault('repositoryId', $this->resource['repositoryId']);
        $this->form->setValidator('repositoryId', new sfValidatorChoice(array('choices' => array_keys($choices))));
        $this->form->setWidget('repositoryId', new sfWidgetFormSelect(array('choices' => $choices)));

        break;

      case 'location':
	  case 'uniqueIdentifier': 	 	  
	  case 'extent':	  
 	  case 'descriptionTitle':
	  case 'periodCovered':
	  case 'findingAids': 	  
	  case 'accrualSpace':  
	  case 'shelf':
	  case 'rowNumber':	  
	  case 'forms':   
      case 'name':
        $this->form->setDefault($name, $this->resource[$name]);
        $this->form->setValidator($name, new sfValidatorString);
        $this->form->setWidget($name, new sfWidgetFormInput);

        break;

      case 'type':
        $this->form->setDefault('type', $this->context->routing->generate(null, array($this->resource->type, 'module' => 'term')));
        $this->form->setValidator('type', new sfValidatorString);
        $this->form->setWidget('type', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CONTAINER_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;

      default:

        return parent::addField($name);
    }
  }

  protected function processField($field)
  {
    switch ($field->getName())
    {
      case 'type':
        unset($this->resource->type);

        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('type')));
        $this->resource->type = $params['_sf_route']->resource;

        break;

      default:

        return parent::processField($field);
    }
  }

  public function execute($request)
  {
    parent::execute($request);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getPostParameters());
      if ($this->form->isValid())
      {
        $this->processForm();

        $this->resource->save();

        if (null !== $next = $this->form->getValue('next'))
        {
          $this->redirect($next);
        }

        $this->redirect(array($this->resource, 'module' => 'physicalobject'));
      }
    }
  }
}
