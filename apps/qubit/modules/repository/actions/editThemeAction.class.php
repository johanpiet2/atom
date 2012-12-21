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

class RepositoryEditThemeAction extends sfAction
{
  public static
    $NAMES = array(
      'backgroundColor',
      'banner',
      'banner_delete',
      'htmlSnippet',
      'logo',
      'logo_delete');

  protected function addField($name)
  {
    switch ($name)
    {
      case 'backgroundColor':
        $this->form->setDefault('backgroundColor', $this->resource->backgroundColor);
        $this->form->setValidator('backgroundColor', new sfValidatorRegex(array('pattern' => '/^#(?:[0-9a-fA-F]{3}){1,2}$/'), array('invalid' => $this->context->i18n->__('Only hexadecimal color value'))));
        $this->form->setWidget('backgroundColor', new sfWidgetFormInput(array(), array('class' => 'color-picker')));

        break;

      case 'htmlSnippet':
        $this->form->setDefault('htmlSnippet', $this->resource->htmlSnippet);
        $this->form->setValidator('htmlSnippet', new sfValidatorString);
        $this->form->setWidget('htmlSnippet', new sfWidgetFormTextarea);

        break;

      case 'banner':
        sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

        $this->form->setValidator($name, new sfValidatorFile(array(
          'max_size' => '262144', // 256K
          'mime_types' => 'web_images',
          'path' => $this->resource->getUploadsPath(true),
          'required' => false)));

        $this->form->setWidget($name, new sfWidgetFormInputFileEditable(array(
          'file_src' => $this->existsBanner ? public_path($this->resource->getBannerPath()) : null,
          'edit_mode' => true,
          'is_image' => true,
          'with_delete' => $this->existsBanner)));
        break;

      case 'banner_delete':
        if ($this->existsBanner)
        {
          $this->form->setValidator('banner_delete', new sfValidatorBoolean);
          $this->form->setWidget('banner_delete', new sfWidgetFormInputCheckbox);
        }

        break;

      case 'logo':
        sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

        $this->form->setValidator($name, new sfValidatorFile(array(
          'max_size' => '262144', // 256K
          'mime_types' => 'web_images',
          'path' => $this->resource->getUploadsPath(true),
          'required' => false)));

        $this->form->setWidget($name, new sfWidgetFormInputFileEditable(array(
          'file_src' => $this->existsLogo ? public_path($this->resource->getLogoPath()) : null,
          'edit_mode' => true,
          'is_image' => true,
          'with_delete' => $this->existsLogo)));

        break;

      case 'logo_delete':
        if ($this->existsLogo)
        {
          $this->form->setValidator($name, new sfValidatorBoolean);
          $this->form->setWidget($name, new sfWidgetFormInputCheckbox);
        }

        break;
    }
  }

  protected function processField($field)
  {
    switch ($name = $field->getName())
    {
      case 'backgroundColor':
        $this->resource->setBackgroundColor($this->form->getValue($field->getName()), array('sourceCulture' => true));

      case 'htmlSnippet':
        $this->resource->setHtmlSnippet($this->form->getValue($field->getName()));

        break;
    }
  }

  public function processForm()
  {
    foreach ($this->form as $field)
    {
      if (isset($this->request[$field->getName()]))
      {
        $this->processField($field);
      }
    }

    return $this;
  }

  public function execute($request)
  {
    $this->resource = $this->getRoute()->resource;

    // Check user authorization
    if (!QubitAcl::check($this->resource, 'update'))
    {
      QubitAcl::forwardUnauthorized();
    }

    // We are going to need this later, when building the form
    $this->existsLogo = $this->resource->existsLogo();
    $this->existsBanner = $this->resource->existsBanner();

    $this->form = new sfForm;

    foreach ($this::$NAMES as $name)
    {
      $this->addField($name);
    }

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getPostParameters(), $request->getFiles());

      if ($this->form->isValid())
      {
        $this->processForm();

        // Process logo and logo_delete together
        if (null !== $this->form->getValue('logo_delete'))
        {
          unlink($this->resource->getLogoPath(true));
        }
        else if (null !== $logo = $this->form->getValue('logo'))
        {
          // Call save() method found in sfValidatedFile
          // TODO: force conversion to png
          $logo->save($this->resource->getLogoPath(true));
        }

        // Process banner and banner_delete together
        if (null !== $this->form->getValue('banner_delete'))
        {
          unlink($this->resource->getBannerPath(true));
        }
        else if (null !== $logo = $this->form->getValue('banner'))
        {
          // Call save() method found in sfValidatedFile
          // TODO: force conversion to png
          $logo->save($this->resource->getBannerPath(true));
        }

        $this->resource->save();

        $this->redirect(array($this->resource, 'module' => 'repository'));
      }
    }
  }
}
