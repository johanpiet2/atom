<?php

/*
**** Author: JJP  SITA ******
**** Module: Access Object component *****
**** Date  :01-04-2013   ******
**** Email  :  *****
/**
 * This class defines the structure of the 'access_object' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */

class AccessObjectTableMap extends TableMap {


	const CLASS_NAME = 'lib.model.map.AccessObjectTableMap';


	public function initialize()
	{
	  // attributes
		$this->setName('access_object');
		$this->setPhpName('accessObject');
		$this->setClassname('QubitAccessObject');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID', 'id', 'INTEGER' , 'object', 'ID', true, null, null);
		$this->addForeignKey('PARENT_ID', 'parentId', 'INTEGER', 'access_object', 'ID', false, null, null);
		$this->addColumn('LFT', 'lft', 'INTEGER', true, null, null);
		$this->addColumn('RGT', 'rgt', 'INTEGER', true, null, null);
		$this->addColumn('SOURCE_CULTURE', 'sourceCulture', 'VARCHAR', true, 7, null);		
	} 

	public function buildRelations()
	{
    $this->addRelation('object', 'object', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
    $this->addRelation('accessObjectRelatedByparentId', 'accessObject', RelationMap::MANY_TO_ONE, array('parent_id' => 'id', ), null, null);
    $this->addRelation('accessObjectRelatedByparentId', 'accessObject', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), null, null);
    $this->addRelation('accessObjectI18n', 'accessObjectI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null);
	} 
} 
