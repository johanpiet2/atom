<?php

/*
**** Author: Ramaano Ndou  ******
**** Module: Preservation Object component *****
**** Date  :01-04-2013   ******
**** Email  :ramaanondou@sita.co.za  *****
*/

class PresevationObjectTableMap extends TableMap {

	const CLASS_NAME = 'lib.model.map.PresevationObjectTableMap';

	public function initialize()
	{
	  // attributes
		$this->setName('presevation_object');
		$this->setPhpName('presevationObject');
		$this->setClassname('QubitPresevationObject');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID', 'id', 'INTEGER' , 'object', 'ID', true, null, null);
		$this->addForeignKey('CONDITION_ID', 'conditionId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('USABILITY_ID', 'usabilityId', 'INTEGER', 'term', 'ID', false, null, null);
    	$this->addForeignKey('MEASURE_ID', 'measureId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('MEDIUM_ID', 'mediumId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('AVAILABILITY_ID', 'availabilityId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('HARD_ID', 'hardId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addColumn('HARD_REASON', 'hardReason', 'LONGVARCHAR', false, null, null);			
		$this->addForeignKey('DIGITAL_ID', 'digitalId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addColumn('DIGITAL_REASON', 'digitalReason', 'LONGVARCHAR', false, null, null);			
		$this->addForeignKey('REFUSAL_ID', 'refusalId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('RESTORATION_ID', 'restorationId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('CONSERVATION_ID', 'conservationId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('TYPE_ID', 'typeId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('SENSITIVITY_ID', 'sensitivityId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('PUBLISH_ID', 'publishId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('CLASSIFICATION_ID', 'classificationId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('RESTRICTION_ID', 'restrictionId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('PARENT_ID', 'parentId', 'INTEGER', 'presevation_object', 'ID', false, null, null);
		$this->addColumn('OBJECT_ID', 'object_id', 'VARCHAR', false, 20, null);		
		$this->addColumn('LFT', 'lft', 'INTEGER', true, null, null);
		$this->addColumn('RGT', 'rgt', 'INTEGER', true, null, null);
		$this->addColumn('SOURCE_CULTURE', 'sourceCulture', 'VARCHAR', true, 7, null);		
	} 

	public function buildRelations()
	{
    $this->addRelation('object', 'object', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
    $this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('condition_id' => 'id', ), 'SET NULL', null);	
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('usability_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('measure_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('medium_id' => 'id', ), 'SET NULL', null);
    $this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('availability_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('hard_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('digital_id' => 'id', ), 'SET NULL', null);
    $this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('refusal_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('restoration_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('conservation_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('type_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('sensitivity_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('publish_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('classification_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('restriction_id' => 'id', ), 'SET NULL', null);
	
    $this->addRelation('presevationObjectRelatedByparentId', 'presevationObject', RelationMap::MANY_TO_ONE, array('parent_id' => 'id', ), null, null);
    $this->addRelation('presevationObjectRelatedByparentId', 'presevationObject', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), null, null);
    $this->addRelation('presevationObjectI18n', 'presevationObjectI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null);
	} 
}
