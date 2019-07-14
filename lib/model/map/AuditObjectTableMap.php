<?php
/**
 * This class defines the structure of the 'audit_object' table.
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class AuditObjectTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.AuditObjectTableMap';

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
		$this->setName('audit');
		$this->setPhpName('auditObject');
		$this->setClassname('QubitAuditObject');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'id', 'INTEGER', true, null, null);
		$this->addForeignPrimaryKey('ID', 'id', 'INTEGER' , 'object', 'ID', true, null, null);
//		$this->addForeignKey('RECORD_ID', 'recordId', 'INTEGER', 'term', 'ID', false, null, null);

		$this->addColumn('USER_ACTION', 'user_action', 'VARCHAR', true, 20, null);
		$this->addColumn('DB_QUERY', 'db_query', 'VARCHAR', true, 1024, null);
		$this->addColumn('USER', 'user', 'VARCHAR', true, 20, null);
		$this->addColumn('ACTION_DATE_TIME', 'Action_date_time', 'VARCHAR', false, 50, null);
		$this->addColumn('DB_TABLE', 'Db_table', 'VARCHAR', false, 50, null);
	} 

	
	public function buildRelations()
	{
    //$this->addRelation('auditObjectRelatedByparentId', 'auditObject', RelationMap::MANY_TO_ONE, array('record_id' => 'id', ), null, null);
    //$this->addRelation('auditObjectRelatedByparentId', 'auditObject', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), null, null);
	} // buildRelations()

} 
