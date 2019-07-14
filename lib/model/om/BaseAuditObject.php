<?php

/*
**** Author: Johan Pieterse  ******
**** Module: Audit Trail Object component *****
**** Date  :01-02-2014   ******
**** Email :johan pieterse <johan.pieterse@sita.co.za>  *****
*/

abstract class BaseAuditObject extends QubitObject implements ArrayAccess
{
  const
    DATABASE_NAME = 'propel',

    TABLE_NAME = 'audit',
    ID = 'audit.ID',
    RECORD_ID = 'audit.RECORD_ID',
	FIELD_KEY = 'audit.FIELD_KEY',
    USER_ACTION = 'audit.USER_ACTION',
    DB_TABLE = 'audit.DB_TABLE',
    DB_QUERY = 'audit.DB_QUERY',
    USER = 'audit.USER',
    ACTION_DATE_TIME = 'audit.ACTION_DATE_TIME';

  public static function addSelectColumns(Criteria $criteria)
  {
  
    $criteria->addSelectColumn(QubitAuditObject::ID);
    $criteria->addSelectColumn(QubitAuditObject::RECORD_ID);
	$criteria->addSelectColumn(QubitAuditObject::FIELD_KEY);
    $criteria->addSelectColumn(QubitAuditObject::USER_ACTION);
    $criteria->addSelectColumn(QubitAuditObject::DB_TABLE);
    $criteria->addSelectColumn(QubitAuditObject::DB_QUERY);
    $criteria->addSelectColumn(QubitAuditObject::USER);
    $criteria->addSelectColumn(QubitAuditObject::ACTION_DATE_TIME);

    return $criteria;
  }

  public static function get(Criteria $criteria, array $options = array())
  {
    if (!isset($options['connection']))
    {
      $options['connection'] = Propel::getConnection(QubitAuditObject::DATABASE_NAME);
    }
  }

  public static function getAll(array $options = array())
  {
    return self::get(new Criteria, $options);
  }

  public static function getOne(Criteria $criteria, array $options = array())
  {
    $criteria->setLimit(1);

    return self::get($criteria, $options)->__get(0, array('defaultValue' => null));
  }

  public static function getById($id, array $options = array())
  {
    $criteria = new Criteria;
    $criteria->add(QubitAuditObject::RECORD_ID, $id);
    $query = self::get($criteria, $options);

    if (1 == count($query = self::get($criteria, $options)))
    {
      return $query[0];
    }
  }

  public static function addRootsCriteria(Criteria $criteria)
  {
    $criteria->add(QubitAuditObject::ID);

    return $criteria;
  }

  public function __construct()
  {
    parent::__construct();

    $this->tables[] = Propel::getDatabaseMap(QubitAuditObject::DATABASE_NAME)->getTable(QubitAuditObject::TABLE_NAME);
  }

  public function __isset($name)
  {
    $args = func_get_args();

    $options = array();
    if (1 < count($args))
    {
      $options = $args[1];
    }

    try
    {
      return call_user_func_array(array($this, 'QubitObject::__isset'), $args);
    }
    catch (sfException $e)
    {
    }

    throw new sfException("Unknown record property \"$name\" on \"".get_class($this).'"');
  }

  public function __get($name)
  {
    $args = func_get_args();

    $options = array();
    if (1 < count($args))
    {
      $options = $args[1];
    }

    try
    {
      return call_user_func_array(array($this, 'QubitAuditObject::__get'), $args);
    }
    catch (sfException $e)
    {
    throw new sfException($e);
    }

  }

  public function __set($name, $value)
  {
    $args = func_get_args();

    $options = array();
    if (2 < count($args))
    {
      $options = $args[2];
    }

    call_user_func_array(array($this, 'QubitAuditObject::__set'), $args);

    return $this;
  }

  public function __unset($name)
  {
    $args = func_get_args();

    $options = array();
    if (1 < count($args))
    {
      $options = $args[1];
    }

    call_user_func_array(array($this, 'QubitAuditObject::__unset'), $args);

    return $this;
  }

}
