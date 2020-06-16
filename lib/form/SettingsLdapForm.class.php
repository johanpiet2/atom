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

ProjectConfiguration::getActive()->loadHelpers('I18N');

/**
 * Global form definition for settings module - with validation.
 *
 * @package    AccesstoMemory
 * @subpackage settings
 */
class SettingsLdapForm extends sfForm
{
  public function configure()
  {
    // Build widgets
    $this->setWidgets(array(
      'ldap_method' => new sfWidgetFormSelectRadio(array('choices'=>array(2=>'LDAP', 1=>'Internal', 0=>'Both')), array('class'=>'radio')),
      'ldap_account_suffix' => new sfWidgetFormInput,
      'ldap_base_dn' => new sfWidgetFormInput,
      'ldap_domain_controllers' => new sfWidgetFormInput,
      'ldap_domain_port' => new sfWidgetFormInput,
      'ldap_user_group' => new sfWidgetFormInput
    ));

    // Add labels
    $this->widgetSchema->setLabels(array(
      'ldap_method' => __('Authentication Method'),
      'ldap_account_suffix' => __('LDAP/AD Account Suffix'),
      'ldap_base_dn' => __('LDAP/AD Base DN'),
      'ldap_domain_controllers' => __('LDAP/AD Domain Controllers'),
      'ldap_domain_port' => __('LDAP/AD Domain port'),
      'ldap_user_group' => __('LDAP/AD User Group')
    ));

    // Add helper text
     $this->widgetSchema->setHelps(array(
      'ldap_method' => __('When set to &quot;LDAP&quot;, this system will use LDAP authentication, when set to &quot;Internal&quot;, this system will use Internal authentication and when set to &quot;Both&quot;, this system will use LDAP or Internal authentication'),
      'ldap_account_suffix' => __('The Domain - @example.com'),
      'ldap_base_dn' => __('DC=EXAMPLE,DC=COM'),
      'ldap_domain_controllers' => __('Domain controllers - separated by &quot;,&quot; - ad01.example.com,ad02.example.com'),
      'ldap_domain_port' => __('Domain port - 389'),
      'ldap_user_group' => __('CN=AtoM users,OU=Archivists,OU=Groups,DC=EXAMPLE,DC=COM')
    ));

    $this->validatorSchema['ldap_method'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['ldap_account_suffix'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['ldap_base_dn'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['ldap_domain_controllers'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['ldap_domain_port'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['ldap_user_group'] = new sfValidatorString(array('required' => false));

    // Set decorator
    $decorator = new QubitWidgetFormSchemaFormatterList($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('list', $decorator);
    $this->widgetSchema->setFormFormatterName('list');

    // Set wrapper text for LDAP settings
    $this->widgetSchema->setNameFormat('ldap[%s]');
  }
}
