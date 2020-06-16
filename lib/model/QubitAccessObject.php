<?php

class QubitAccessObject extends BaseAccessObject
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

  public function insert($connection = null)
  {
    if (!isset($this->slug))
    {
      $this->slug = QubitSlug::slugify($this->__get('name', array('sourceCulture' => true)));
    }

    return parent::insert($connection);
  }

  public function getLabel()
  {
    $label = '';

   if ($this->refusal)
    {
      $label .= $this->refusal.': ';
    }
	
	if ($this->sensitivity)
    {
      $label .= $this->sensitivity.': ';
    }
	
	if ($this->publish)
    {
      $label .= $this->publish.': ';
    }
	
	if ($this->classification)
    {
      $label .= $this->classification.': ';
    }
	
	if ($this->restriction)
    {
      $label .= $this->restriction.': ';
    }

    if ($this->restriction_condition)
    {
      $label .= $this->restriction_condition.': ';
    }
		
    $label .= $this->__toString();
	
  }

  public function delete($connection = null)
  {
    $this->deleteInformationObjectRelations();

    parent::delete($connection);
  }


  public function deleteInformationObjectRelations()
  {
    $informationObjectRelations = QubitRelation::getRelationsBySubjectId($this->id,
    array('typeId'=>QubitTaxonomy::ACCESS_TYPE_ID));

    foreach ($informationObjectRelations as $relation)
    {
      $relation->delete();
    }
  }

  public function getInformationObjects($options = array())
  {
    $criteria = new Criteria;
    $criteria->addJoin(QubitAccessObject::ID, QubitRelation::SUBJECT_ID);
    $criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
    $criteria->add(QubitAccessObject::ID, $this->id);
    $criteria->add(QubitRelation::TYPE_ID, QubitTaxonomy::ACCESS_TYPE_ID);

    return QubitQuery::createFromCriteria($criteria, 'QubitInformationObject', $options);
  }
  
  /**
   * Only find Access objects, not other actor types
   *
   * @param Criteria $criteria current search criteria
   * @return Criteria modified search critieria
   */
  public static function addGetOnlyAccessObjectCriteria($criteria)
  {
    $criteria->addJoin(QubitAccessObject::ID, QubitObject::ID);
    $criteria->add(QubitObject::CLASS_NAME, 'QubitAccessObject');

    return $criteria;
  }
  
}
