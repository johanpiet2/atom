<?php

class QubitBookoutObject extends BaseBookoutObject
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

    if ($this->requestor)
    {
      $label .= $this->requestor.': ';
    }
		
	if ($this->dispatcher)
    {
      $label .= $this->dispatcher.': ';
    }
	
		$label .= $this->__toString();

	if (0 == strlen($location = $this->getLocation()))
    {
      $location = $this->getLocation(array('sourceCulture' => true));
    }

    if (0 < strlen($location))
    {
      $label .= ' - '.$location;
    }
		return $label;
	
	if (0 == strlen($record_condition = $this->getRecord_condition()))
    {
      $record_condition = $this->getRecord_condition(array('sourceCulture' => true));
    }

    if (0 < strlen($record_condition))
    {
      $label .= ' - '.$record_condition;
    }
		return $label;

    if (0 == strlen($time_period = $this->getTime_period()))
    {
      $time_period = $this->getTime_period(array('sourceCulture' => true));
    }

    if (0 < strlen($time_period))
    {
      $label .= ' - '.$time_period;
    }
		return $label;

	if (0 == strlen($remarks = $this->getRemarks()))
    {
      $remarks = $this->getRemarks(array('sourceCulture' => true));
    }

    if (0 < strlen($remarks))
    {
      $label .= ' - '.$remarks;
    }
	 return $label;
 
	if (0 == strlen($unique_identifier = $this->getUnique_identifier()))
    {
      $unique_identifier = $this->getUnique_identifier(array('sourceCulture' => true));
    }

    if (0 < strlen($unique_identifier))
    {
      $label .= ' - '.$unique_identifier;
    }
		return $label;	 
	 
	if (0 == strlen($strong_room = $this->getStrong_room()))
    {
      $strong_room = $this->getStrong_room(array('sourceCulture' => true));
    }

    if (0 < strlen($strong_room))
    {
      $label .= ' - '.$strong_room;
    }
		return $label;	 
	 
	if (0 == strlen($row = $this->getRow()))
    {
      $row = $this->getRow(array('sourceCulture' => true));
    }

    if (0 < strlen($row))
    {
      $label .= ' - '.$row;
    }
	 return $label;	 
		
	 
	if (0 == strlen($shelf = $this->getShelf()))
    {
      $shelf = $this->getShelf(array('sourceCulture' => true));
    }

    if (0 < strlen($shelf))
    {
      $label .= ' - '.$shelf;
    }
	 return $label;	 
 
	if (0 == strlen($availability = $this->getAvailability()))
    {
      $availability = $this->getAvailability(array('sourceCulture' => true));
    }

    if (0 < strlen($availability))
    {
      $label .= ' - '.$availability;
    }
	 return $label;	 
 } 
 
 /**
   * Overwrite BasePhysicalObject::delete() method to add cascading delete
   * logic
   *
   * @param mixed $connection a database connection object
   */
  public function delete($connection = null)
  {
    $this->deleteInformationObjectRelations();

    parent::delete($connection);
  }

  /**
   * Delete relation records linking this bookout object to information objects
   */
  public function deleteInformationObjectRelations()
  {
    $informationObjectRelations = QubitRelation::getRelationsBySubjectId($this->id,
    array('requestorId'=>QubitTaxonomy::BOOKIN_TYPE_ID));

    foreach ($informationObjectRelations as $relation)
    {
      $relation->delete();
    }
  }

  /**
   * Get related information object via QubitRelation relationship
   *
   * @param array $options list of options to pass to QubitQuery
   * @return QubitQuery collection of Information Objects
   */
  public function getInformationObjects($options = array())
  {
    $criteria = new Criteria;
    $criteria->addJoin(QubitBookoutObject::ID, QubitRelation::SUBJECT_ID);
    $criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
    $criteria->add(QubitBookoutObject::ID, $this->id);

    return QubitQuery::createFromCriteria($criteria, 'QubitInformationObject', $options);
  }
  
  /**
   * Only find Bookout objects, not other actor types
   *
   * @param Criteria $criteria current search criteria
   * @return Criteria modified search critieria
   */
  public static function addGetOnlyBookoutObjectCriteria($criteria)
  {
    $criteria->addJoin(QubitBookoutObject::ID, QubitObject::ID);
    $criteria->add(QubitObject::CLASS_NAME, 'QubitBookoutObject');

    return $criteria;
  }
  
}
