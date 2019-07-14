<?php

class QubitAuditObject extends BaseAuditObject
{

  public function __toString()
  {
    $string = $this->name;	
    if (!isset($string))
    {
      $string = $this->getName(array('sourceCulture' => true));  
    }

    return (string) $string;
  }

  public function getLabel()
  {
    $label = '';

	if (0 == strlen($record_id = $this->getRecord_id()))
    {
      $record_id = $this->getRecord(array('sourceCulture' => true));

    }

    if (0 < strlen($record_id))
    {
      $label .= ' - '.$record_id;
    }
		return $label;
	
	if (0 == strlen($field_key = $this->getField_key()))
    {
      $field_key = $this->getField_key(array('sourceCulture' => true));
    }

    if (0 < strlen($field_key))
    {
      $label .= ' - '.$field_key;
    }
		return $label;

    if (0 == strlen($action = $this->getAction()))
    {
      $action = $this->getAction(array('sourceCulture' => true));
    }

    if (0 < strlen($time_period))
    {
      $label .= ' - '.$time_period;
    }
		return $label;

	if (0 == strlen($db_table = $this->getDb_table()))
    {
      $db_table = $this->getDb_table(array('sourceCulture' => true));
    }

    if (0 < strlen($db_table))
    {
      $label .= ' - '.$db_table;
    }
	 return $label;
 
	if (0 == strlen($db_query = $this->getDb_query()))
    {
      $db_query = $this->getDb_query(array('sourceCulture' => true));
    }

    if (0 < strlen($db_query))
    {
      $label .= ' - '.$db_query;
    }
		return $label;	 
	 
	if (0 == strlen($user = $this->getUser()))
    {
      $user = $this->getUser(array('sourceCulture' => true));
    }

    if (0 < strlen($user))
    {
      $label .= ' - '.$user;
    }
		return $label;	 
	 
	if (0 == strlen($action_date_time = $this->getAction_date_time()))
    {
    }

    if (0 < strlen($action_date_time))
    {
      $label .= ' - '.$action_date_time;
    }
	 return $label;	 

 } 
 
  /**
   * Get related information object linked to QubitAuditObject
   *
   * @param array $options list of options to pass to QubitQuery
   * @return QubitQuery collection of Information Objects and Audit Objects
   */
  public static function getAllJoined(Criteria $criteria, array $options = array())
  {
/*    $c1 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'actor', Criteria::EQUAL);
    $c2 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'information_object', Criteria::EQUAL);
    $c3 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'repository', Criteria::EQUAL);
    $c4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'researcher', Criteria::EQUAL);
    $c5 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'service_provider', Criteria::EQUAL);
    $c6 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'physical_object', Criteria::EQUAL);
    $c7 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'registry', Criteria::EQUAL);
    $c1->addOr($c2);
    $c1->addOr($c3);
    $c1->addOr($c4);
    $c1->addOr($c5);
    $c1->addOr($c6);
    $c1->addOr($c7);
    $criteria->add($c1);
    */
	$criteria->add(QubitAuditObject::ID, null, Criteria::ISNOTNULL);

	return $criteria;
  }

  /**
   * Get QubitAuditObject deleted items
   *
   * @param array $options list of options to pass to QubitQuery
   * @return QubitQuery collection of Information Objects and Audit Objects
   */
  public static function getAllDeleted(Criteria $criteria, array $options = array())
  {
	$criteria->add(QubitAuditObject::USER_ACTION, 'delete', Criteria::EQUAL);

    return $criteria;
  }

  
}
