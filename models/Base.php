<?php
class Base extends \Phalcon\Mvc\Model {

	protected $tablename = '';
	protected $db; // db instance
	protected $keys = [];

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}

	protected function assignField ($record, $excludeKeys = false)
	{
        $setSql = '';
        foreach ($record as $field => $value)               
        {
			if ($excludeKeys && in_array($field, $this->keys))
				continue;

            if ($setSql) $setSql .= ", ";
            $setSql .= "`$field`='$value'";
        }
		return $setSql;
	}

	protected function setConditionByKey ($record)
	{
		$condition = '';
		foreach ($this->keys as $key)
		{
			$value = property_exists($record, $key) ? 
			$record->{$key} : FALSE;

			if ($value !== FALSE)
			{
				if ($condition) $condition .= " AND ";
				$condition .=  "`$key`='$value'";
			}
		}

		return $condition?:'0';
	}

	protected function setCondition ($aCondition = [])
	{
		return implode(' AND ', $aCondition);
	}

	public function getAll() 
	{
        $result = $this->db->fetchAll("SELECT 
                *
            FROM {$this->tablename}", 
            Phalcon\Db::FETCH_ASSOC
        );
        
        if ( !$result ) {
            return false;
        }
        
        return $result;
    }

	public function getRecordBy ($aCondition, $fetchOne = false)
	{
		$condition = $this->setCondition($aCondition);

		if ($fetchOne)
		{
			$result = $this->db->fetchOne(
				"SELECT * FROM {$this->tablename} WHERE $condition", 
				Phalcon\Db::FETCH_ASSOC
			);
		}
		else 
		{
			$result = $this->db->fetchAll(
				"SELECT * FROM {$this->tablename} WHERE $condition", 
				Phalcon\Db::FETCH_ASSOC
			);
		}
        
        return $result?:[];
	}
	
	public function getById ($id)
	{
		return $this->getRecordBy(["id='$id'"], true);
	}

	public function addRecord ($record)
	{
        $setSql = $this->assignField ($record);
        $sql = sprintf("INSERT INTO %s SET %s", $this->tablename, $setSql);

        $this->db->execute($sql);

        if ($this->db->affectedRows() == 0) {
            return false;
        }
        return true;
	}

	public function updateRecord ($record, $excludeKeys = true)
	{
		$setSql = $this->assignField ($record, $excludeKeys);
		$whereSql = $this->setConditionByKey ($record);
		$sql = sprintf("UPDATE %s SET %s WHERE %s", $this->tablename, $setSql, $whereSql);
		
        return $this->db->execute($sql);
	}	

	public function updateRecordBy ($record, $aCondition, $excludeKeys =true)
	{
		$setSql = $this->assignField ($record, $excludeKeys);
		$sql = sprintf("UPDATE %s SET %s WHERE %s", $this->tablename, $setSql, $this->setCondition($aCondition));
		
        return $this->db->execute($sql);
	}	

	public function deleteRecord($record)
	{
		$whereSql = $this->setConditionByKey ($record);
		$sql = sprintf("DELETE FROM %s WHERE %s", $this->tablename, $whereSql);

        $this->db->execute($sql);
        
        if ($this->db->affectedRows() == 0) {
            return false;
        }
        return true;
    }

	public function deleteRecordBy($aCondition)
	{
		$sql = sprintf("DELETE FROM %s WHERE %s", $this->tablename, $this->setCondition($aCondition));

        $this->db->execute($sql);
        
        if ($this->db->affectedRows() == 0) {
            return false;
        }
        return true;
    }

	public function deleteRecordById($id)
	{
		return $this->deleteRecordBy(["id='$id'"]);
    }

	public function getInsertId ()
	{
		return $this->db->lastInsertId();
	}
}