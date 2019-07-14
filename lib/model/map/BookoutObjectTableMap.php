<?php


/**
 * This class defines the structure of the 'bookout_object' table.
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
class BookoutObjectTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.BookoutObjectTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('bookout_object');
		$this->setPhpName('bookoutObject');
		$this->setClassname('QubitBookoutObject');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID', 'id', 'INTEGER' , 'object', 'ID', true, null, null);
		$this->addForeignKey('REQUESTOR_ID', 'requestorId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('DISPATCHER_ID', 'dispatcherId', 'INTEGER', 'term', 'ID', false, null, null);
		$this->addForeignKey('PARENT_ID', 'parentId', 'INTEGER', 'bookout_object', 'ID', false, null, null);
		$this->addColumn('LFT', 'lft', 'INTEGER', true, null, null);
		$this->addColumn('RGT', 'rgt', 'INTEGER', true, null, null);
		$this->addColumn('SOURCE_CULTURE', 'sourceCulture', 'VARCHAR', true, 7, null);
		
	} 

	
	public function buildRelations()
	{
    $this->addRelation('object', 'object', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
    $this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('requestor_id' => 'id', ), 'SET NULL', null);
	$this->addRelation('term', 'term', RelationMap::MANY_TO_ONE, array('dispatcher_id' => 'id', ), 'SET NULL', null);
    $this->addRelation('bookoutObjectRelatedByparentId', 'bookoutObject', RelationMap::MANY_TO_ONE, array('parent_id' => 'id', ), null, null);
    $this->addRelation('bookoutObjectRelatedByparentId', 'bookoutObject', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), null, null);
    $this->addRelation('bookoutObjectI18n', 'bookoutObjectI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} 
