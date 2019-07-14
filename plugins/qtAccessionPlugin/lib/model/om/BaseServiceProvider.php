<?php

abstract class BaseServiceProvider extends QubitActor implements ArrayAccess
{
  const
    DATABASE_NAME = 'propel',

    TABLE_NAME = 'service_provider',

    ID = 'service_provider.ID',
    REPOSITORY_ID = 'service_provider.REPOSITORY_ID';

  public static function addSelectColumns(Criteria $criteria)
  {
    parent::addSelectColumns($criteria);

    $criteria->addJoin(QubitServiceProvider::ID, QubitActor::ID);

    $criteria->addSelectColumn(QubitServiceProvider::ID);
    $criteria->addSelectColumn(QubitServiceProvider::REPOSITORY_ID);

    return $criteria;
  }

  public static function get(Criteria $criteria, array $options = array())
  {
    if (!isset($options['connection']))
    {
      $options['connection'] = Propel::getConnection(QubitServiceProvider::DATABASE_NAME);
    }

    self::addSelectColumns($criteria);

    return QubitQuery::createFromCriteria($criteria, 'QubitServiceProvider', $options);
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
    $criteria->add(QubitServiceProvider::ID, $id);

    if (1 == count($query = self::get($criteria, $options)))
    {
      return $query[0];
    }
    else
    {
      return "Unknown";
    }
  }

  /**
   * Only find Service Provider objects, not other types
   *
   * @param Criteria $criteria current search criteria
   * @return Criteria modified search critieria
   */
  public static function addGetOnlyServiceProviderObjectCriteria($criteria)
  {
    $criteria->addJoin(QubitServiceProvider::ID, QubitObject::ID);
    $criteria->add(QubitObject::CLASS_NAME, 'QubitServiceProvider');

    return $criteria;
  }


  public function __construct()
  {
    parent::__construct();

    $this->tables[] = Propel::getDatabaseMap(QubitServiceProvider::DATABASE_NAME)->getTable(QubitServiceProvider::TABLE_NAME);
  }
}
