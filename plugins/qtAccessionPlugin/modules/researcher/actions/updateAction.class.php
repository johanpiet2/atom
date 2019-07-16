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

class researcherUpdateAction extends DefaultEditAction
{
  // Arrays not allowed in class constants
  public static
    $NAMES = array('corporateBodyIdentifiers', 'authorizedFormOfName', 'repositoryId');

  protected function earlyExecute()
  {
    $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);

    if (isset($this->request->source))
    {
		$this->resource = QubitResearcher::getById($this->request->source);
	}
	else
	{
	    $this->resource = new QubitResearcher;
	}

    if (isset($this->getRoute()->resource))
    {
      $this->resource = $this->getRoute()->resource;

      // Check user authorization
      if (!QubitAcl::check($this->resource, 'update'))
      {
        //QubitAcl::forwardUnauthorized();
      }

      // Add optimistic lock
      $this->form->setDefault('serialNumber', $this->resource->serialNumber);
      $this->form->setValidator('serialNumber', new sfValidatorInteger);
      $this->form->setWidget('serialNumber', new sfWidgetFormInputHidden);
    }
    else
    {
      // Check user authorization
      if (!QubitAcl::check($this->resource, 'create'))
      {
        //QubitAcl::forwardUnauthorized();
      }
    }

    $title = $this->context->i18n->__('Edit Researcher');
    if (isset($this->getRoute()->resource))
    {
      if (1 > strlen($title = $this->resource->__toString()))
      {
        $title = $this->context->i18n->__('Untitled');
      }

      $title = $this->context->i18n->__('Edit %1%', array('%1%' => $title));
    }

    $this->response->setTitle("$title - {$this->response->getTitle()}");

    $this->contactInformationEditComponent = new ContactInformationEditComponent($this->context, 'contactinformation', 'editContactInformation');
    $this->contactInformationEditComponent->resource = $this->resource;
    $this->contactInformationEditComponent->execute($this->request);
  }

	protected function addField($name)
	{
		switch ($name)
		{
			case 'corporateBodyIdentifiers':
			$this->form->setDefault('corporateBodyIdentifiers', $this->resource->corporateBodyIdentifiers);
			$this->form->setValidator('corporateBodyIdentifiers', new sfValidatorString);
			$this->form->setWidget('corporateBodyIdentifiers', new sfWidgetFormInput);

			break;

			case 'authorizedFormOfName':
			$this->form->setDefault('authorizedFormOfName', $this->resource->authorizedFormOfName);
			$this->form->setValidator('authorizedFormOfName', new sfValidatorString);
			$this->form->setWidget('authorizedFormOfName', new sfWidgetFormInput);

			break;

			case 'repositoryId':
				// Show only Repositories linked to user - Administrator can see all
			 	$choices = array();
				$choices = QubitRepository::filteredUser($this->context->user->getAttribute('user_id'), $this->context->user->isAdministrator());

				$repoId = QubitResearcher::getById($this->resource->id);
				$this->form->setDefault('repositoryId', $repoId->repositoryId);
				$this->form->setValidator('repositoryId', new sfValidatorChoice(array('choices' => array_keys($choices))));
				$this->form->setWidget('repositoryId', new sfWidgetFormSelect(array('choices' => $choices)));

				break;

			default:

			return parent::addField($name);
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
				$this->contactInformationEditComponent->processForm();

				$this->processForm();

				$this->resource->save();

				$this->redirect('researcher/browse');
			}
		}
	}
}
