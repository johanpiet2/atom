<?php
/*
**** Author: JJP  ******
**** Module: Preservation Object component *****
**** Date  :01-04-2013   ******
**** Email  :  *****
*/

class AccessObjectI18nTableMap extends TableMap {

	const CLASS_NAME = 'lib.model.map.AccessObjectI18nTableMap';

	public function initialize()
	{	 
		$this->setName('access_object_i18n');
		$this->setPhpName('accessObjectI18n');
		$this->setClassname('QubitAccessObjectI18n');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addColumn('NAME', 'name', 'VARCHAR', false, 1024, null);		
		$this->addForeignPrimaryKey('ID', 'id', 'INTEGER' , 'access_object', 'ID', true, null, null);
		$this->addForeignKey('REFUSAL_ID', 'refusalId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('SENSITIVITY_ID', 'sensitivityId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('PUBLISH_ID', 'publishId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('CLASSIFICATION_ID', 'classificationId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('RESTRICTION_ID', 'restrictionId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addColumn('RESTRICTION_CONDITION', 'restriction_condition', 'VARCHAR', false, 1024, null);		
		$this->addColumn('PUBLISHED', 'published', 'BOOLEAN', false, null, true);
		$this->addColumn('OBJECT_ID', 'object_id', 'VARCHAR', false, 20, null);		
		$this->addPrimaryKey('CULTURE', 'culture', 'VARCHAR', true, 7, null);
	} 

	public function buildRelations()
	{
		$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('refusal_id' => 'id', ), 'SET NULL', null);
		$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('sensitivity_id' => 'id', ), 'SET NULL', null);
		$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('publish_id' => 'id', ), 'SET NULL', null);
		$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('classification_id' => 'id', ), 'SET NULL', null);
		$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('restriction_id' => 'id', ), 'SET NULL', null);
	
		$this->addRelation('accessObject', 'accessObject', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
	} 

} 
