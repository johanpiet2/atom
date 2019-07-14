<?php


/**
 * This class defines the structure of the 'physical_object_i18n' table.
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
class BookoutObjectI18nTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.BookoutObjectI18nTableMap';

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
		$this->setName('bookout_object_i18n');
		$this->setPhpName('bookoutObjectI18n');
		$this->setClassname('QubitBookoutObjectI18n');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addColumn('NAME', 'name', 'VARCHAR', false, 1024, null);
		$this->addColumn('TIME_PERIOD', 'time_period', 'LONGVARCHAR', false, null, null);
		$this->addColumn('REMARKS', 'remarks', 'LONGVARCHAR', false, null, null);	
        $this->addColumn('UNIQUE_IDENTIFIER', 'unique_identifier', 'LONGVARCHAR', false, 1024, null);			
		$this->addColumn('STRONG_ROOM', 'strong_room', 'LONGVARCHAR', false, 1024, null);
		$this->addColumn('ROW', 'row', 'LONGVARCHAR', false, 50, null);	
		$this->addColumn('SHELF', 'shelf', 'LONGVARCHAR', false, 50, null);	
		$this->addColumn('LOCATION', 'location', 'LONGVARCHAR', false, 1024, null);
		$this->addColumn('AVAILABILITY', 'availability', 'LONGVARCHAR', false, 1024, null);
		$this->addColumn('RECORD_CONDITION', 'record_condition', 'LONGVARCHAR', false, 1024, null);	
		$this->addForeignPrimaryKey('ID', 'id', 'INTEGER' , 'bookout_object', 'ID', true, null, null);
		$this->addColumn('REQUESTOR_TYPE', 'requestor_type', 'CHAR', false, 1, null);	
		$this->addColumn('SERVICE_PROVIDER', 'service_provider', 'INTEGER', true, null, 0);
//		$this->addForeignPrimaryKey('SERVICE_PROVIDER', 'service_provider', 'INTEGER' , 'actor', 'ID', true, null, null);
		$this->addColumn('OBJECT_ID', 'object_id', 'VARCHAR', false, 20, null);	
		$this->addPrimaryKey('CULTURE', 'culture', 'VARCHAR', true, 7, null);
		// validators
	} // initialize()

	
	public function buildRelations()
	{
    $this->addRelation('bookoutObject', 'bookoutObject', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
	} 

} 
