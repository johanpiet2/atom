<?php

class QubitAuditObject extends BaseAuditObject
{
    
    public function __toString()
    {
        $string = $this->name;
        if (!isset($string)) {
            $string = $this->getName(array(
                'sourceCulture' => true
            ));
        }
        
        return (string) $string;
    }
    
    public function getLabel()
    {
        $label = '';
        
        if (0 == strlen($record_id = $this->getRecord_id())) {
            $record_id = $this->getRecord(array(
                'sourceCulture' => true
            ));
            
        }
        
        if (0 < strlen($record_id)) {
            $label .= ' - ' . $record_id;
        }
        return $label;
        
        if (0 == strlen($field_key = $this->getField_key())) {
            $field_key = $this->getField_key(array(
                'sourceCulture' => true
            ));
        }
        
        if (0 < strlen($field_key)) {
            $label .= ' - ' . $field_key;
        }
        return $label;
        
        if (0 == strlen($action = $this->getAction())) {
            $action = $this->getAction(array(
                'sourceCulture' => true
            ));
        }
        
        if (0 < strlen($time_period)) {
            $label .= ' - ' . $time_period;
        }
        return $label;
        
        if (0 == strlen($db_table = $this->getDb_table())) {
            $db_table = $this->getDb_table(array(
                'sourceCulture' => true
            ));
        }
        
        if (0 < strlen($db_table)) {
            $label .= ' - ' . $db_table;
        }
        return $label;
        
        if (0 == strlen($db_query = $this->getDb_query())) {
            $db_query = $this->getDb_query(array(
                'sourceCulture' => true
            ));
        }
        
        if (0 < strlen($db_query)) {
            $label .= ' - ' . $db_query;
        }
        return $label;
        
        if (0 == strlen($user = $this->getUser())) {
            $user = $this->getUser(array(
                'sourceCulture' => true
            ));
        }
        
        if (0 < strlen($user)) {
            $label .= ' - ' . $user;
        }
        return $label;
        
        if (0 == strlen($action_date_time = $this->getAction_date_time())) {
        }
        
        if (0 < strlen($action_date_time)) {
            $label .= ' - ' . $action_date_time;
        }
        return $label;
        
    }

    public static function setCriteriaDeleted($id, $type, array $options = array())
    {
		$criteria = new Criteria;
		$criteria->addSelectColumn(QubitAuditObject::ID);
		$criteria->addSelectColumn(QubitAuditObject::RECORD_ID);
		$criteria->addSelectColumn(QubitAuditObject::DB_TABLE);
		$criteria->add(QubitAuditObject::RECORD_ID, $id, Criteria::EQUAL);
   		$criteria->add(QubitAuditObject::DB_TABLE, "%".$type."%", Criteria::LIKE);
        return $criteria;
    }
       
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$dbMap = Propel::getDatabaseMap($criteria->getDbName());
		$db = Propel::getDB($criteria->getDbName());

		if ($con === null) {
			$con = Propel::getConnection($criteria->getDbName(), Propel::CONNECTION_READ);
		}

		$stmt = null;

		if ($criteria->isUseTransaction()) $con->beginTransaction();

		try {

			$params = array();
			$sql = BasePeer::createSelectSql($criteria, $params);
			$stmt = $con->prepare($sql);
			BasePeer::populateStmtValues($stmt, $params, $dbMap, $db);

			$stmt->execute();

			if ($criteria->isUseTransaction()) $con->commit();

		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			if ($criteria->isUseTransaction()) $con->rollBack();
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
		return $stmt;
	}
        
}
