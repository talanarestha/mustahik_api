<?php

class AccountController extends ControllerBase {

    public function onConstruct() {
        parent::onConstruct();
    }

    /**
     * API Login
     */
    public function login() {
        // input
        $username = $this->getPost('username');
        $password = $this->getPost('password');

        // validate mandatory input
        $valid = $this->validMandatory([$username, $password]);
        if( !$valid ) $this->respNOK('missing mandatory');

        $users = new Users;
        // validate username
        $userData = $users->byUsername($username);
        if( !$userData ) $this->respNOK('username atau password anda salah');

        // validate password
        if( $userData['password'] != md5($password) ){
            $this->respNOK('username atau password anda salah');
        }

        // check status
        if( $userData['status']!=1 ) {
            $this->respNOK('akun anda sudah di-non-aktifkan');
        }
        
        // do not expose password
        unset($userData['password']);

        // get unread message
        $inbox = new Inbox;
        $userData['unread'] = $inbox->getUnread($username);

        $this->respOK($userData);
    }
}
