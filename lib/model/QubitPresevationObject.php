<?php

class QubitPresevationObject extends BasePresevationObject
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

    if ($this->condition)
    {
      $label .= $this->condition.': ';
    }
		
	if ($this->usability)
    {
      $label .= $this->usability.': ';
    }
	
	if ($this->measure)
    {
      $label .= $this->measure.': ';
    }
	
	if ($this->medium)
    {
      $label .= $this->medium.': ';
    }
	
	if ($this->availability)
    {
      $label .= $this->availability.': ';
    }
	
	if ($this->hard)
    {
      $label .= $this->hard.': ';
    }
	
	if ($this->hardReason)
    {
      $label .= $this->hardReason.': ';
    }
	
	if ($this->digital)
    {
      $label .= $this->digital.': ';
    }
	
	if ($this->digitalReason)
    {
      $label .= $this->digitalReason.': ';
    }
	
   if ($this->refusal)
    {
      $label .= $this->refusal.': ';
    }
	
	if ($this->restoration)
    {
      $label .= $this->restoration.': ';
    }
	
	if ($this->conservation)
    {
      $label .= $this->conservation.': ';
    }
	
	if ($this->type)
    {
      $label .= $this->type.': ';
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
    array('typeId'=>QubitTaxonomy::PRESERVATION_TYPE_ID));

    foreach ($informationObjectRelations as $relation)
    {
      $relation->delete();
    }
  }

  public function getInformationObjects($options = array())
  {
    $criteria = new Criteria;
    $criteria->addJoin(QubitPresevationObject::ID, QubitRelation::SUBJECT_ID);
    $criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
    $criteria->add(QubitPresevationObject::ID, $this->id);
    $criteria->add(QubitRelation::TYPE_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);

    return QubitQuery::createFromCriteria($criteria, 'QubitInformationObject', $options);
  }
  
  /**
   * Only find Preservation objects, not other actor types
   *
   * @param Criteria $criteria current search criteria
   * @return Criteria modified search critieria
   */
  public static function addGetOnlyPreservationObjectCriteria($criteria)
  {
    $criteria->addJoin(QubitPresevationObject::ID, QubitObject::ID);
    $criteria->add(QubitObject::CLASS_NAME, 'QubitPresevationObject');

    return $criteria;
  }
}
