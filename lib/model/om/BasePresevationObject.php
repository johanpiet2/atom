<?php

abstract class BasePresevationObject extends QubitObject implements ArrayAccess
{
  const
    DATABASE_NAME = 'propel',

    TABLE_NAME = 'presevation_object',

    ID = 'presevation_object.ID',
    CONDITION_ID = 'presevation_object.CONDITION_ID',
	USABILITY_ID = 'presevation_object.USABILITY_ID',
    MEASURE_ID = 'presevation_object.MEASURE_ID',
	MEDIUM_ID = 'presevation_object.MEDIUM_ID', 
	AVAILABILITY_ID = 'presevation_object.AVAILABILITY_ID',
	HARD_ID = 'presevation_object.HARD_ID',
	HARD_REASON = 'presevation_object.HARD_REASON',
	DIGITAL_ID = 'presevation_object.DIGITAL_ID',
	DIGITAL_REASON = 'presevation_object.DIGITAL_REASON',
	REFUSAL_ID = 'presevation_object.REFUSAL_ID', 	
	RESTORATION_ID= 'presevation_object.RESTORATION_ID',   
    CONSERVATION_ID = 'presevation_object.CONSERVATION_ID',
	TYPE_ID = 'presevation_object.TYPE_ID',
	SENSITIVITY_ID = 'presevation_object.SENSITIVITY_ID',
	PUBLISH_ID = 'presevation_object.PUBLISH_ID',
	CLASSIFICATION_ID = 'presevation_object.CLASSIFICATION_ID',
	RESTRICTION_ID = 'presevation_object.RESTRICTION_ID',
    PARENT_ID = 'presevation_object.PARENT_ID',
    OBJECT_ID = 'presevation_object.OBJECT_ID',
    LFT = 'presevation_object.LFT',
    RGT = 'presevation_object.RGT',
    SOURCE_CULTURE = 'presevation_object.SOURCE_CULTURE';

  public static function addSelectColumns(Criteria $criteria)
  {
    parent::addSelectColumns($criteria);

    $criteria->addJoin(QubitPresevationObject::ID, QubitObject::ID);

    $criteria->addSelectColumn(QubitPresevationObject::ID);
    $criteria->addSelectColumn(QubitPresevationObject::CONDITION_ID);
	$criteria->addSelectColumn(QubitPresevationObject::USABILITY_ID);
	$criteria->addSelectColumn(QubitPresevationObject::MEASURE_ID);
	$criteria->addSelectColumn(QubitPresevationObject::MEDIUM_ID);
	$criteria->addSelectColumn(QubitPresevationObject::AVAILABILITY_ID);
	$criteria->addSelectColumn(QubitPresevationObject::HARD_ID);
	$criteria->addSelectColumn(QubitPresevationObject::HARD_REASON);
	$criteria->addSelectColumn(QubitPresevationObject::DIGITAL_ID);
	$criteria->addSelectColumn(QubitPresevationObject::DIGITAL_REASON);
	$criteria->addSelectColumn(QubitPresevationObject::REFUSAL_ID);
	$criteria->addSelectColumn(QubitPresevationObject::RESTORATION_ID);
	$criteria->addSelectColumn(QubitPresevationObject::CONSERVATION_ID);
    $criteria->addSelectColumn(QubitPresevationObject::TYPE_ID);
    $criteria->addSelectColumn(QubitPresevationObject::SENSITIVITY_ID);	
	$criteria->addSelectColumn(QubitPresevationObject::PUBLISH_ID);
	$criteria->addSelectColumn(QubitPresevationObject::CLASSIFICATION_ID);
	$criteria->addSelectColumn(QubitPresevationObject::RESTRICTION_ID);
    $criteria->addSelectColumn(QubitPresevationObject::PARENT_ID);
    $criteria->addSelectColumn(QubitPresevationObject::OBJECT_ID);
    $criteria->addSelectColumn(QubitPresevationObject::LFT);
    $criteria->addSelectColumn(QubitPresevationObject::RGT);
    $criteria->addSelectColumn(QubitPresevationObject::SOURCE_CULTURE);

    return $criteria;
  }

  public static function get(Criteria $criteria, array $options = array())
  {
    if (!isset($options['connection']))
    {
      $options['connection'] = Propel::getConnection(QubitPresevationObject::DATABASE_NAME);
    }

    self::addSelectColumns($criteria);

    return QubitQuery::createFromCriteria($criteria, 'QubitPresevationObject', $options);
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
    $criteria->add(QubitPresevationObject::ID, $id);

    if (1 == count($query = self::get($criteria, $options)))
    {
      return $query[0];
    }
  }

  public static function addOrderByPreorder(Criteria $criteria, $order = Criteria::ASC)
  {
    if ($order == Criteria::DESC)
    {
      return $criteria->addDescendingOrderByColumn(QubitPresevationObject::LFT);
    }

    return $criteria->addAscendingOrderByColumn(QubitPresevationObject::LFT);
  }

  public static function addRootsCriteria(Criteria $criteria)
  {
    $criteria->add(QubitPresevationObject::PARENT_ID);

    return $criteria;
  }

  public function __construct()
  {
    parent::__construct();

    $this->tables[] = Propel::getDatabaseMap(QubitPresevationObject::DATABASE_NAME)->getTable(QubitPresevationObject::TABLE_NAME);
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

    if ('presevationObjectsRelatedByparentId' == $name)
    {
      return true;
    }

    if ('presevationObjectI18ns' == $name)
    {
      return true;
    }

    try
    {
      if (!$value = call_user_func_array(array($this->getCurrentpresevationObjectI18n($options), '__isset'), $args) && !empty($options['cultureFallback']))
      {
        return call_user_func_array(array($this->getCurrentpresevationObjectI18n(array('sourceCulture' => true) + $options), '__isset'), $args);
      }

      return $value;
    }
    catch (sfException $e)
    {
    }

    if ('ancestors' == $name)
    {
      return true;
    }

    if ('descendants' == $name)
    {
      return true;
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
      return call_user_func_array(array($this, 'QubitObject::__get'), $args);
    }
    catch (sfException $e)
    {
    }

    if ('presevationObjectsRelatedByparentId' == $name)
    {
      if (!isset($this->refFkValues['presevationObjectsRelatedByparentId']))
      {
        if (!isset($this->id))
        {
          $this->refFkValues['presevationObjectsRelatedByparentId'] = QubitQuery::create();
        }
        else
        {
          $this->refFkValues['presevationObjectsRelatedByparentId'] = self::getpresevationObjectsRelatedByparentIdById($this->id, array('self' => $this) + $options);
        }
      }

      return $this->refFkValues['presevationObjectsRelatedByparentId'];
    }

    if ('presevationObjectI18ns' == $name)
    {
      if (!isset($this->refFkValues['presevationObjectI18ns']))
      {
        if (!isset($this->id))
        {
          $this->refFkValues['presevationObjectI18ns'] = QubitQuery::create();
        }
        else
        {
          $this->refFkValues['presevationObjectI18ns'] = self::getpresevationObjectI18nsById($this->id, array('self' => $this) + $options);
        }
      }

      return $this->refFkValues['presevationObjectI18ns'];
    }

    try
    {
      if (1 > strlen($value = call_user_func_array(array($this->getCurrentpresevationObjectI18n($options), '__get'), $args)) && !empty($options['cultureFallback']))
      {
        return call_user_func_array(array($this->getCurrentpresevationObjectI18n(array('sourceCulture' => true) + $options), '__get'), $args);
      }

      return $value;
    }
    catch (sfException $e)
    {
    }

    if ('ancestors' == $name)
    {
      if (!isset($this->values['ancestors']))
      {
        if ($this->new)
        {
          $this->values['ancestors'] = QubitQuery::create(array('self' => $this) + $options);
        }
        else
        {
          $criteria = new Criteria;
          $this->addAncestorsCriteria($criteria);
          $this->addOrderByPreorder($criteria);
          $this->values['ancestors'] = self::get($criteria, array('self' => $this) + $options);
        }
      }

      return $this->values['ancestors'];
    }

    if ('descendants' == $name)
    {
      if (!isset($this->values['descendants']))
      {
        if ($this->new)
        {
          $this->values['descendants'] = QubitQuery::create(array('self' => $this) + $options);
        }
        else
        {
          $criteria = new Criteria;
          $this->addDescendantsCriteria($criteria);
          $this->addOrderByPreorder($criteria);
          $this->values['descendants'] = self::get($criteria, array('self' => $this) + $options);
        }
      }

      return $this->values['descendants'];
    }

    throw new sfException("Unknown record property \"$name\" on \"".get_class($this).'"');
  }

  public function __set($name, $value)
  {
    $args = func_get_args();

    $options = array();
    if (2 < count($args))
    {
      $options = $args[2];
    }

    call_user_func_array(array($this, 'QubitObject::__set'), $args);

    call_user_func_array(array($this->getCurrentpresevationObjectI18n($options), '__set'), $args);

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

    call_user_func_array(array($this, 'QubitObject::__unset'), $args);

    call_user_func_array(array($this->getCurrentpresevationObjectI18n($options), '__unset'), $args);

    return $this;
  }

  public function clear()
  {
    foreach ($this->presevationObjectI18ns as $presevationObjectI18n)
    {
      $presevationObjectI18n->clear();
    }

    return parent::clear();
  }

  public function save($connection = null)
  {
    parent::save($connection);

    foreach ($this->presevationObjectI18ns as $presevationObjectI18n)
    {
      $presevationObjectI18n->id = $this->id;

      $presevationObjectI18n->save($connection);
    }

    return $this;
  }

  protected function param($column)
  {
    $value = $this->values[$column->getPhpName()];

    // Convert to DateTime or SQL zero special case
    if (isset($value) && $column->isTemporal() && !$value instanceof DateTime)
    {
      // Year only: one or more digits.  Convert to SQL zero special case
      if (preg_match('/^\d+$/', $value))
      {
        $value .= '-0-0';
      }

      // Year and month only: one or more digits, plus separator, plus
      // one or more digits.  Convert to SQL zero special case
      else if (preg_match('/^\d+[-\/]\d+$/', $value))
      {
        $value .= '-0';
      }

      // Convert to DateTime if not SQL zero special case: year plus
      // separator plus zero to twelve (possibly zero padded) plus
      // separator plus one or more zeros
      if (!preg_match('/^\d+[-\/]0*(?:1[0-2]|\d)[-\/]0+$/', $value))
      {
        try
        {
          $value = new DateTime($value);
        }
        catch (Exception $e)
        {
          return null;
        }
      }
    }

    return $value;
  }

  protected function insert($connection = null)
  {
    $this->updateNestedSet($connection);

    parent::insert($connection);

    return $this;
  }

  protected function update($connection = null)
  {
    // Update nested set keys only if parent id has changed
    if (isset($this->values['parentId']))
    {
      // Get the "original" parentId before any updates
      $offset = 0;
      $originalParentId = null;
      foreach ($this->tables as $table)
      {
        foreach ($table->getColumns() as $column)
        {
          if ('parentId' == $column->getPhpName())
          {
            $originalParentId = $this->row[$offset];
            break;
          }
          $offset++;
        }
      }

      // If updated value of parentId is different then original value,
      // update the nested set
      if ($originalParentId != $this->values['parentId'])
      {
        $this->updateNestedSet($connection);
      }
    }

    parent::update($connection);

    return $this;
  }

  public function delete($connection = null)
  {
    if ($this->deleted)
    {
      throw new PropelException('This object has already been deleted.');
    }

    $this->clear();
    $this->deleteFromNestedSet($connection);

    parent::delete($connection);

    return $this;
  }
//000000000000000000000000000000000000000
//0000000000000000000000000000000000000000000000
//000000000000000000000000000000000000000000000000000

  public static function addJointypeCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::CONDITION_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinusabilityCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::USABILITY_ID, QubitTerm::ID);

    return $criteria;
  }
 
  public static function addJoinpresevationmeasureCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::MEASURE_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinmediumCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::MEDIUM_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinavailabilityCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::AVAILABILITY_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinhardCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::HARD_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoindigitalCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::DIGITAL_ID, QubitTerm::ID);

    return $criteria;
  }
  
  
  public static function addJoinrefusalCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::REFUSAL_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinrestorationintervetionCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::RESTORATION_ID, QubitTerm::ID);

    return $criteria;
  }
  
 public static function addJoinconservationCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::CONSERVATION_ID, QubitTerm::ID);

    return $criteria;
  }
  
 public static function addJointypeidCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::TYPE_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinsensitivityidCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::SENSITIVITY_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinpublishidCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::PUBLISH_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinclassificationidCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::CLASSIFICATION_ID, QubitTerm::ID);

    return $criteria;
  }
  
  public static function addJoinrestrictionidCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::RESTRICTION_ID, QubitTerm::ID);

    return $criteria;
  }
    
  //000000000000000000000000000000000000000000000000000000000000000000000
  
  public static function addJoinparentCriteria(Criteria $criteria)
  {
    $criteria->addJoin(QubitPresevationObject::PARENT_ID, QubitPresevationObject::ID);

    return $criteria;
  }

  public static function addpresevationObjectsRelatedByparentIdCriteriaById(Criteria $criteria, $id)
  {
    $criteria->add(QubitPresevationObject::PARENT_ID, $id);

    return $criteria;
  }

  public static function getpresevationObjectsRelatedByparentIdById($id, array $options = array())
  {
    $criteria = new Criteria;
    self::addpresevationObjectsRelatedByparentIdCriteriaById($criteria, $id);

    return QubitPresevationObject::get($criteria, $options);
  }

  public function addpresevationObjectsRelatedByparentIdCriteria(Criteria $criteria)
  {
    return self::addpresevationObjectsRelatedByparentIdCriteriaById($criteria, $this->id);
  }

  public static function addpresevationObjectI18nsCriteriaById(Criteria $criteria, $id)
  {
    $criteria->add(QubitPresevationObjectI18n::ID, $id);

    return $criteria;
  }

  public static function getpresevationObjectI18nsById($id, array $options = array())
  {
    $criteria = new Criteria;
    self::addpresevationObjectI18nsCriteriaById($criteria, $id);

    return QubitPresevationObjectI18n::get($criteria, $options);
  }

  public function addpresevationObjectI18nsCriteria(Criteria $criteria)
  {
    return self::addpresevationObjectI18nsCriteriaById($criteria, $this->id);
  }

  public function getCurrentpresevationObjectI18n(array $options = array())
  {
    if (!empty($options['sourceCulture']))
    {
      $options['culture'] = $this->sourceCulture;
    }

    if (!isset($options['culture']))
    {
      $options['culture'] = sfPropel::getDefaultCulture();
    }

    $presevationObjectI18ns = $this->presevationObjectI18ns->indexBy('culture');
    if (!isset($presevationObjectI18ns[$options['culture']]))
    {
      $presevationObjectI18ns[$options['culture']] = new QubitPresevationObjectI18n;
    }

    return $presevationObjectI18ns[$options['culture']];
  }

  public function hasChildren()
  {
    return ($this->rgt - $this->lft) > 1;
  }

  public function addAncestorsCriteria(Criteria $criteria)
  {
    return $criteria->add(QubitPresevationObject::LFT, $this->lft, Criteria::LESS_THAN)->add(QubitPresevationObject::RGT, $this->rgt, Criteria::GREATER_THAN);
  }

  public function addDescendantsCriteria(Criteria $criteria)
  {
    return $criteria->add(QubitPresevationObject::LFT, $this->lft, Criteria::GREATER_THAN)->add(QubitPresevationObject::RGT, $this->rgt, Criteria::LESS_THAN);
  }

  protected function updateNestedSet($connection = null)
  {

unset($this->values['lft']);
unset($this->values['rgt']);
    if (!isset($connection))
    {
      $connection = QubitTransactionFilter::getConnection(QubitPresevationObject::DATABASE_NAME);
    }

    if (!isset($this->lft) || !isset($this->rgt))
    {
      $delta = 2;
    }
    else
    {
      $delta = $this->rgt - $this->lft + 1;
    }

    if (null === $parent = $this->__get('parent', array('connection' => $connection)))
    {
      $statement = $connection->prepare('
        SELECT MAX('.QubitPresevationObject::RGT.')
        FROM '.QubitPresevationObject::TABLE_NAME);
      $statement->execute();
      $row = $statement->fetch();
      $max = $row[0];

      if (!isset($this->lft) || !isset($this->rgt))
      {
        $this->lft = $max + 1;
        $this->rgt = $max + 2;

        return $this;
      }

      $shift = $max + 1 - $this->lft;
    }
    else
    {
      $parent->clear();

      if (isset($this->lft) && isset($this->rgt) && $this->lft <= $parent->lft && $this->rgt >= $parent->rgt)
      {
        throw new PropelException('An object cannot be a descendant of itself.');
      }

      $statement = $connection->prepare('
        UPDATE '.QubitPresevationObject::TABLE_NAME.'
        SET '.QubitPresevationObject::LFT.' = '.QubitPresevationObject::LFT.' + ?
        WHERE '.QubitPresevationObject::LFT.' >= ?');
      $statement->execute(array($delta, $parent->rgt));

      $statement = $connection->prepare('
        UPDATE '.QubitPresevationObject::TABLE_NAME.'
        SET '.QubitPresevationObject::RGT.' = '.QubitPresevationObject::RGT.' + ?
        WHERE '.QubitPresevationObject::RGT.' >= ?');
      $statement->execute(array($delta, $parent->rgt));

      if (!isset($this->lft) || !isset($this->rgt))
      {
        $this->lft = $parent->rgt;
        $this->rgt = $parent->rgt + 1;
        $parent->rgt += 2;

        return $this;
      }

      if ($this->lft > $parent->rgt)
      {
        $this->lft += $delta;
        $this->rgt += $delta;
      }

      $shift = $parent->rgt - $this->lft;
    }

    $statement = $connection->prepare('
      UPDATE '.QubitPresevationObject::TABLE_NAME.'
      SET '.QubitPresevationObject::LFT.' = '.QubitPresevationObject::LFT.' + ?, '.QubitPresevationObject::RGT.' = '.QubitPresevationObject::RGT.' + ?
      WHERE '.QubitPresevationObject::LFT.' >= ?
      AND '.QubitPresevationObject::RGT.' <= ?');
    $statement->execute(array($shift, $shift, $this->lft, $this->rgt));

    $this->deleteFromNestedSet($connection);

    if ($shift > 0)
    {
      $this->lft -= $delta;
      $this->rgt -= $delta;
    }

    $this->lft += $shift;
    $this->rgt += $shift;

    return $this;
  }

  protected function deleteFromNestedSet($connection = null)
  {
    if (!isset($connection))
    {
      $connection = QubitTransactionFilter::getConnection(QubitPresevationObject::DATABASE_NAME);
    }

    $delta = $this->rgt - $this->lft + 1;

    $statement = $connection->prepare('
      UPDATE '.QubitPresevationObject::TABLE_NAME.'
      SET '.QubitPresevationObject::LFT.' = '.QubitPresevationObject::LFT.' - ?
      WHERE '.QubitPresevationObject::LFT.' >= ?');
    $statement->execute(array($delta, $this->rgt));

    $statement = $connection->prepare('
      UPDATE '.QubitPresevationObject::TABLE_NAME.'
      SET '.QubitPresevationObject::RGT.' = '.QubitPresevationObject::RGT.' - ?
      WHERE '.QubitPresevationObject::RGT.' >= ?');
    $statement->execute(array($delta, $this->rgt));

    return $this;
  }

  public function isInTree()
  {
    return $this->lft > 0 && $this->rgt > $this->lft;
  }

  public function isRoot()
  {
      return $this->isInTree() && $this->lft == 1;
  }

  public function isDescendantOf($parent)
  {
    return $this->isInTree() && $this->lft > $parent->lft && $this->rgt < $parent->rgt;
  }

  public function moveToFirstChildOf($parent, PropelPDO $con = null)
  {
    if ($parent->isDescendantOf($this))
    {
      throw new PropelException('Cannot move a node as child of one of its subtree nodes.');
    }

    $this->moveSubtreeTo($parent->lft + 1, $con);

    return $this;
  }

  public function moveToLastChildOf($parent, PropelPDO $con = null)
  {
    if ($parent->isDescendantOf($this))
    {
      throw new PropelException('Cannot move a node as child of one of its subtree nodes.');
    }

    $this->moveSubtreeTo($parent->rgt, $con);

    return $this;
  }

  public function moveToPrevSiblingOf($sibling, PropelPDO $con = null)
  {
    if (!$this->isInTree())
    {
      throw new PropelException('This object must be already in the tree to be moved. Use the insertAsPrevSiblingOf() instead.');
    }

    if ($sibling->isRoot())
    {
      throw new PropelException('Cannot move to previous sibling of a root node.');
    }

    if ($sibling->isDescendantOf($this))
    {
      throw new PropelException('Cannot move a node as sibling of one of its subtree nodes.');
    }

    $this->moveSubtreeTo($sibling->lft, $con);

    return $this;
  }

  public function moveToNextSiblingOf($sibling, PropelPDO $con = null)
  {
    if (!$this->isInTree())
    {
      throw new PropelException('This object must be already in the tree to be moved. Use the insertAsPrevSiblingOf() instead.');
    }

    if ($sibling->isRoot())
    {
      throw new PropelException('Cannot move to previous sibling of a root node.');
    }

    if ($sibling->isDescendantOf($this))
    {
      throw new PropelException('Cannot move a node as sibling of one of its subtree nodes.');
    }

    $this->moveSubtreeTo($sibling->rgt + 1, $con);

    return $this;
  }

  protected function moveSubtreeTo($destLeft, PropelPDO $con = null)
  {
    $left  = $this->lft;
    $right = $this->rgt;

    $treeSize = $right - $left +1;

    if ($con === null)
    {
      $con = Propel::getConnection();
    }

    $con->beginTransaction();

    try
    {
      // make room next to the target for the subtree
      self::shiftRLValues($treeSize, $destLeft, null, $con);

      if ($left >= $destLeft) // src was shifted too?
      {
        $left += $treeSize;
        $right += $treeSize;
      }

      // move the subtree to the target
      self::shiftRLValues($destLeft - $left, $left, $right, $con);

      // remove the empty room at the previous location of the subtree
      self::shiftRLValues(-$treeSize, $right + 1, null, $con);

      // update all loaded nodes
      // self::updateLoadedNodes(null, $con);

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollback();

      throw $e;
    }
  }


  protected function shiftRLValues($delta, $first, $last = null, PropelPDO $con = null)
  {
    if ($con === null)
    {
      $con = Propel::getConnection();
    }

    // Shift left column values
    $whereCriteria = new Criteria;
    $criterion = $whereCriteria->getNewCriterion(QubitPresevationObject::LFT, $first, Criteria::GREATER_EQUAL);
    if (null !== $last)
    {
      $criterion->addAnd($whereCriteria->getNewCriterion(QubitPresevationObject::LFT, $last, Criteria::LESS_EQUAL));
    }
    $whereCriteria->add($criterion);

    $valuesCriteria = new Criteria;
    $valuesCriteria->add(QubitPresevationObject::LFT, array('raw' => QubitPresevationObject::LFT . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);

    BasePeer::doUpdate($whereCriteria, $valuesCriteria, $con);

    // Shift right column values
    $whereCriteria = new Criteria;
    $criterion = $whereCriteria->getNewCriterion(QubitPresevationObject::RGT, $first, Criteria::GREATER_EQUAL);
    if (null !== $last)
    {
      $criterion->addAnd($whereCriteria->getNewCriterion(QubitPresevationObject::RGT, $last, Criteria::LESS_EQUAL));
    }
    $whereCriteria->add($criterion);

    $valuesCriteria = new Criteria;
    $valuesCriteria->add(QubitPresevationObject::RGT, array('raw' => QubitPresevationObject::RGT . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);

    BasePeer::doUpdate($whereCriteria, $valuesCriteria, $con);
  }
}
