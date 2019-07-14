<?php

/*
**** Author: Ramaano Ndou  ******
**** Module: Preservation Object component *****
**** Date  :01-04-2013   ******
**** Email  :ramaanondou@sita.co.za  *****
*/

class PresevationObjectI18nTableMap extends TableMap {

	const CLASS_NAME = 'lib.model.map.PresevationObjectI18nTableMap';

	public function initialize()
	{	 
		$this->setName('presevation_object_i18n');
		$this->setPhpName('presevationObjectI18n');
		$this->setClassname('QubitPresevationObjectI18n');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addColumn('NAME', 'name', 'VARCHAR', false, 1024, null);		
				
		$this->addForeignPrimaryKey('ID', 'id', 'INTEGER' , 'presevation_object', 'ID', true, null, null);
		$this->addPrimaryKey('CULTURE', 'culture', 'VARCHAR', true, 7, null);
	} 

	public function buildRelations()
	{
    	$this->addRelation('presevationObject', 'presevationObject', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
	} 
}
