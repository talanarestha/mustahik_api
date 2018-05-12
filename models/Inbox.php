<?php
class Inbox extends \Phalcon\Mvc\Model {

	protected $tablename = 't_inbox';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
    }
    
    public function compose($message){
        $query = sprintf("INSERT INTO %s ( 
                inbox_from,
                inbox_to,
                inbox_subject,
                inbox_message,
                inbox_data,
                inbox_status,
                created
            ) VALUES 
            ( 
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                0,
                now()
            )",
            $this->tablename,
            $message['from'],
            $message['to'],
            $message['subject'],
            $message['body'],
            isset($message['data']) ? $message['data'] : '' 
        );

        $this->db->execute($query);

        if ($this->db->affectedRows() == 0) {
            return false;
        }

        return true;
    }
	
	public function getAll($username, $page, $limit=10, $filter=null){
        $sqlFilter = '';
        if( $filter!=null ){
            $sqlFilter = ' AND subject like "%'.$filter.'%" ';
        }

        $offset     = ($page-1) * $limit; 
        $sqlLimit   = " LIMIT $offset,$limit";

        $query = "SELECT * FROM {$this->tablename} WHERE inbox_to='$username' $sqlFilter ORDER BY inbox_id DESC $sqlLimit";
        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC);
        
        if ( !$result )
            return false;
        
        return $result;
    }

    public function getUnread($username){
        $query = "SELECT count(1) as unread FROM {$this->tablename} WHERE inbox_to='$username' AND inbox_status=0";
        $result = $this->db->fetchOne($query, \Phalcon\Db::FETCH_ASSOC);
        
        if ( !$result )
            return 0;
        
        return $result['unread'];
    }

    public function read($id, $username){
        $this->db->execute(sprintf("UPDATE %s SET inbox_status=1 WHERE inbox_id=%d AND inbox_to='%s'",
            $this->tablename, 
            $id,
            $username
        ));
        
        if ($this->db->affectedRows() == 0) {
            return false;
        }
        return true;
    }

    public function delete($id, $username){
        $this->db->execute(sprintf("DELETE FROM %s WHERE inbox_id='%s' AND inbox_to='%s'",
            $this->tablename, $id, $username
        ));
        
        if ($this->db->affectedRows() == 0) {
            return false;
        }
        return true;
    }
}