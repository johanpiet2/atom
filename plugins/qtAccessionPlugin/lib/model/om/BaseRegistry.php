<?php

abstract class BaseRegistry extends QubitActor implements ArrayAccess
{
  const
    DATABASE_NAME = 'propel',

    TABLE_NAME = 'registry',

    ID = 'registry.ID';

  public static function addSelectColumns(Criteria $criteria)
  {
    parent::addSelectColumns($criteria);

    $criteria->addJoin(QubitRegistry::ID, QubitActor::ID);

    $criteria->addSelectColumn(QubitRegistry::ID);

    return $criteria;
  }

  public static function get(Criteria $criteria, array $options = array())
  {
    if (!isset($options['connection']))
    {
      $options['connection'] = Propel::getConnection(QubitRegistry::DATABASE_NAME);
    }

    self::addSelectColumns($criteria);

    return QubitQuery::createFromCriteria($criteria, 'QubitRegistry', $options);
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
    $criteria->add(QubitRegistry::ID, $id);

    if (1 == count($query = self::get($criteria, $options)))
    {
      return $query[0];
    }
    else
    {
      return "Unknown";
    }
  }

  public static function getByIdentifier($identifier, array $options = array())
  {
	$criteria = new Criteria;
	$criteria->addJoin(QubitRegistry::ID, QubitActorI18n::ID);
	$criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
	$criteria->add(QubitActorI18n::CULTURE, $this->context->user->getCulture());
	$criteria->add(QubitActorI18n::AUTHORIZED_FORM_OF_NAME, $identifier, Criteria::LIKE);
    if (1 == count($query = self::getOne($criteria, $options)))
    {
      return $query;
    }
  }

  /**
   * Only find Register objects, not other types
   *
   * @param Criteria $criteria current search criteria
   * @return Criteria modified search critieria
   */
  public static function addGetOnlyRegisterObjectCriteria($criteria)
  {
    $criteria->addJoin(QubitRegistry::ID, QubitObject::ID);
    $criteria->add(QubitObject::CLASS_NAME, 'QubitRegistry');

    return $criteria;
  }

  public function __construct()
  {
    parent::__construct();

    $this->tables[] = Propel::getDatabaseMap(QubitRegistry::DATABASE_NAME)->getTable(QubitRegistry::TABLE_NAME);
  }
}
