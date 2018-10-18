<?php
class Users extends Base {

    protected $tablename = 'users';
    protected $keys = ['user_id'];
    
    public function getByEmail ($email)
    {
        return $this->getRecordBy(["user_email='$email'"], true);
    }

    public function getById ($id)
    {
        return $this->getRecordBy(["user_id='$id'"], true);
    }

}