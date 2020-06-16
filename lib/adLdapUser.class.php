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

// TODO: Should we be doing this from ProjectConfiguration? Or maybe a Symfony
// plugin with its own vendor directory?

if (is_file(dirname(__FILE__).'/../vendor/adLDAP/adLDAP.php'))
{
    include dirname(__FILE__).'/../vendor/adLDAP/adLDAP.php';
}
else
{
      $errorMsg = sfContext::getInstance()->i18n->__('Include file missing - adLDAP.php');
      throw new Exception($errorMsg);
}
if (is_file(dirname(__FILE__).'/../vendor/password_compat/password.php'))
{
	include dirname(__FILE__).'/../vendor/password_compat/password.php';
}
else
{
      $errorMsg = sfContext::getInstance()->i18n->__('Include file missing - password.php');
      throw new Exception($errorMsg);
}

class adLdapUser extends myUser implements Zend_Acl_Role_Interface
{
  public function authenticate($username, $password)
  {
  
	putenv('ATOM_DRMC_LDAP_ADMIN_USERNAME='.$username); // set MYVAR to an empty value.  It is in the environment
	putenv('ATOM_DRMC_LDAP_ADMIN_PASSWORD='.$password); // unset MYVAR.  It is removed from the environment
  
    // Allow Active Directory LDAP authentication to be overridden during development
    $configuration = sfContext::getInstance()->getConfiguration();
    if ($configuration->isDebug() || 'dev' == $configuration->getEnvironment())
    {
 //     return parent::authenticate($username, $password);
    }

	// jjp SITA - LDAP not set use normal authentication
	$ldap_method = QubitSetting::getByName('ldap_method');
	if ($ldap_method != null)
	{
		if ($ldap_method == "0")
		{
			if (parent::authenticate($username, $password) == 1)
			{
				return parent::authenticate($username, $password);
			}
			else
			{
				$authenticated = false;

				// Anonymous is not a real user
				if ($username == 'anonymous')
				{
				  return false;
				}

				$authenticated = $this->ldapAuthenticate($username, $password);
				// Fallback to non-LDAP authentication if need be and load/create user data
				if (!$authenticated)
				{
				  $authenticated = parent::authenticate($username, $password);

				  // Load user using username or, if one doesn't exist, create it
				  $criteria = new Criteria;
				  $criteria->add(QubitUser::EMAIL, $username);
				  $user = QubitUser::getOne($criteria);
				}
				else
				{
				  // Load user using username or, if one doesn't exist, create it
				  $criteria = new Criteria;
				  $criteria->add(QubitUser::USERNAME, $username);
				  if (null === $user = QubitUser::getOne($criteria))
				  {
					$user = $this->createUserFromLdapInfo($username);
				  }
				}

				// Sign in user if authentication was successful
				if ($authenticated)
				{
				  $this->signIn($user);
				}

				return $authenticated;
			}
		}
		elseif($ldap_method == "1")
		{
			return parent::authenticate($username, $password);
		}
		elseif($ldap_method == "2")
		{
			$authenticated = false;

			// Anonymous is not a real user
			if ($username == 'anonymous')
			{
			  return false;
			}

			$authenticated = $this->ldapAuthenticate($username, $password);
			// Fallback to non-LDAP authentication if need be and load/create user data
			if (!$authenticated)
			{
			  $authenticated = parent::authenticate($username, $password);

			  // Load user using username or, if one doesn't exist, create it
			  $criteria = new Criteria;
			  $criteria->add(QubitUser::EMAIL, $username);
			  $user = QubitUser::getOne($criteria);
			}
			else
			{
			  // Load user using username or, if one doesn't exist, create it
			  $criteria = new Criteria;
			  $criteria->add(QubitUser::USERNAME, $username);
			  if (null === $user = QubitUser::getOne($criteria))
			  {
				$user = $this->createUserFromLdapInfo($username);
			  }
			}

			// Sign in user if authentication was successful
			if ($authenticated)
			{
			  $this->signIn($user);
			}

			return $authenticated;
		}
	}
	else
	{
		// internal
		return parent::authenticate($username, $password);
	}

  }

  protected function createUserFromLdapInfo($username)
  {
    $user = new QubitUser();
    $user->username = $username;

    // Set LDAP-derived user properties
    $info = $this->ldapUserInfo($username);
    if (false !== $info)
    {
      foreach ($info as $field => $value)
      {
        $user->$field = $value;
      }
    }

    $user->save();

	// remove for now - jjp SITA
    // If user being created is the LDAP administrator, make user
    // an administrator
    if ($username === getenv('ATOM_DRMC_LDAP_ADMIN_USERNAME'))
    {
/*      $aclUserGroup = new QubitAclUserGroup;
      $aclUserGroup->userId = $user->id;
      $aclUserGroup->securityId = 1203;
      $aclUserGroup->groupId = QubitAclGroup::ADMINISTRATOR_ID;
      $aclUserGroup->save();
*/    }

    return $user;
  }

  // Get username of all users
  public static function allUsers()
  {
    $adldap = adLdapUser::getAdLdapConnection();

    $filter = "(&(objectClass=user)(memberOf=". sfConfig::get('app_ldap_user_group') ."))";
    $fields = array('sAMAccountName');

    // Do chunked fetch of all users
    $rawResults = adLdapUser::paginatedSearch($adldap, $filter, $fields);
    unset($rawResults['count']);

    // Simplify results to make them like normal ADLDAP->user()->all() method
    $users = array();

    foreach ($rawResults as $userData)
    {
      $userNameData = $userData['samaccountname'];
      unset($userNameData['count']);
      $username = array_pop($userNameData);
      array_push($users, $username);
    }

    // Add non-LDAP AtoM users to user list
    $criteria = new Criteria;
    $criteria->add(QubitUser::SALT, null, Criteria::ISNOTNULL);
    $normalUsers = QubitUser::get($criteria);

    foreach ($normalUsers as $user)
    {
      array_push($users, $user->username);
    }

    sort($users);

    return $users;
  }

  /**
   * ADLDAP doesn't support full searches, giving the following error:
   * "Partial search results returned: Sizelimit exceeded"
   *
   * The code in this function is a workaround, explained here:
   * http://sourceforge.net/p/adldap/discussion/358759/thread/17c74ca8/
   */
  private static function paginatedSearch($adldap, $filter, $fields, $pageSize = 500)
  {
    $cookie = '';
    $result = array();
    $result['count'] = 0;

    do {

      ldap_set_option($adldap->getLdapConnection(), LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_control_paged_result($adldap->getLdapConnection(), $pageSize, true, $cookie);

      $sr = ldap_search($adldap->getLdapConnection(), $adldap->getBaseDn(), $filter, $fields);
      $entries = ldap_get_entries($adldap->getLdapConnection(), $sr);
      $entries['count'] += $result['count'];

      $result = array_merge($result, $entries);

      ldap_control_paged_result_response($adldap->getLdapConnection(), $sr, $cookie);

    } while (!empty($cookie));

    return $result;
  }

  /**
   * Establishes connection with Active Directory.
   * TODO: Should we make this static to reuse the same connection?
   */
  private function getAdLdapConnection()
  {
    $admin_username = getenv('ATOM_DRMC_LDAP_ADMIN_USERNAME');
    $admin_password = getenv('ATOM_DRMC_LDAP_ADMIN_PASSWORD');

    if (!$admin_username || !$admin_password)
    {
      $exceptionMessage = 'The ATOM_DRMC_LDAP_ADMIN_USERNAME and ATOM_DRMC_LDAP_ADMIN_PASSWORD environment variables must be set';

      throw new sfConfigurationException($exceptionMessage);
    }

	$ldap_account_suffix = QubitSetting::getByName('ldap_account_suffix');
	$ldap_base_dn = QubitSetting::getByName('ldap_base_dn');
	$ldap_domain_controllers = QubitSetting::getByName('ldap_domain_controllers');
	$ldap_domain_port = QubitSetting::getByName('ldap_domain_port');
	if (is_numeric(trim($ldap_domain_port))) 
	{
		$ldap_domain_port = (int)trim($ldap_domain_port);
	}
	else
	{
		$ldap_domain_port = 389;
	}
	$ldap_user_group = QubitSetting::getByName('ldap_user_group');

    $options = array(
      'account_suffix'     => $ldap_account_suffix,
      'admin_username'     => $admin_username,
      'admin_password'     => $admin_password,
      'base_dn'            => $ldap_base_dn,
      'ad_port'            => $ldap_domain_port,
      'recursive_groups'   => $ldap_user_group,
      'domain_controllers' => explode(',', $ldap_domain_controllers)
    );

    try
    {
      $adldap = new adLDAP($options);
    }
    catch (adLDAPException $e)
    {
		//Hack to redirect to login screen again
		// create a new cURL resource
		$ch = curl_init();

		// set URL and other appropriate options
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		curl_setopt($ch, CURLOPT_URL, $actual_link);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		// grab URL and pass it to the browser
		curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);
		// This is in the PHP file and sends a Javascript alert to the client
		//Display error message
		$message = "An error accured while logging in: " . $e->getMessage();

		if (strpos($message, 'Invalid credentials') !== false) {
			$message = "User ID or Password does not match login credentials on file - Please try again";
		}
		echo "<script type='text/javascript'>alert('$message');</script>";

       	return;
    }

    return $adldap;
  }

  /**
   * ldapAuthenticate caches the result of _ldapAuthenticate with a short TTL
   * to avoid hitting the directory.
   */
  private function ldapAuthenticate($username, $password)
  {
    try
    {
      // Try to load a cache engine
      $cache = QubitCache::getInstance();
    }
    catch (Exception $e)
    {
      return $this->_ldapAuthenticate($username, $password);
    }

    $cacheKey = 'adldap-hash:'.$username;

    // Look up cache entry and verify hash if exists
    if ($cache->has($cacheKey) && (null !== $hash = $cache->get($cacheKey)))
    {
      return password_verify($password, $hash);
    }

    // Authenticate against LDAP
    if (!$this->_ldapAuthenticate($username, $password))
    {
      return false;
    }

    // Cache entry
    $hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => 10));
    $cache->set($cacheKey, $hash, 120);

    return true;
  }

  private function _ldapAuthenticate($username, $password)
  {
    $adldap = adLdapUser::getAdLdapConnection();

    // Check to see if user is the admin or part of the DRMC LDAP user group
    // TODO: see if there's a way to retool things so user()->inGroup can be used
    $infoCollection = $adldap->user()->infoCollection($username);

    if ($username != getenv('ATOM_DRMC_LDAP_ADMIN_USERNAME'))
    {
      if (
        is_array($infoCollection->memberof)
        && !in_array(sfConfig::get('app_ldap_user_group'), $infoCollection->memberof)
      )
      {
        return false;
      }
      else if (
        is_string($infoCollection->memberof)
        && $infoCollection->memberof != sfConfig::get('app_ldap_user_group')
      )
      {
        return false;
      }
    }

    // Authenticate via LDAP
    return $adldap->user()->authenticate($username, $password);
  }

  public static function ldapUserInfo($username)
  {
    $adldap = adLdapUser::getAdLdapConnection();

    $infoCollection = $adldap->user()->infoCollection($username);

    if (!$infoCollection)
    {
      return false;
    }

    $info = array();

    $ldapUserPropertiesToAtomUserProperties = array(
      'mail' => 'email'
    );

    // Translate LDAP properties to AtoM user properties
    foreach ($ldapUserPropertiesToAtomUserProperties as $ldapProperty => $atomProperty)
    {
      if (isset($infoCollection->$ldapProperty))
      {
        $info[$atomProperty] = $infoCollection->$ldapProperty;
      }
    }

    return $info;
  }
}
