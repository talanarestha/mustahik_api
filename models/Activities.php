<?php
class Activities extends Base {

    protected $tablename = 'log_activities';
    protected $userId;

    public function setUserID($userId){
        $this->userId = $userId;
    }

    public function log($action, $status='success', $data='', $userId=false) {
        $sql = sprintf("INSERT INTO {$this->tablename} (
                act_user_id,
                act_action,
                act_status,
                act_data,
                created
            ) 
            VALUES (
                %d,
                '%s',
                '%s',
                '%s',
                now())",
            $userId!=false ? $userId : $this->userId,
            $action,
            $status,
            $data
        );

        $this->db->execute($sql);

        if ($this->db->affectedRows() == 0) {
            return false;
        }
        return true;
    }
}