<?php

class AccountController extends ControllerBase {

    /**
     * API Login
     */
    public function login() 
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // validate mandatory input
        $valid = $this->validMandatory([$username, $password]);
        if( !$valid ) 
            $this->respNOK('parameter tidak lengkap');

        $users = new Users;
        $userData = $users->getByEmail($username);
        if( !$userData ) 
            $this->respNOK('alamat email tidak ditemukan');

        // validate password
        if ( !$this->security->checkHash($password, $userData['user_password']) ) 
            $this->respNOK('alamat email atau password anda salah');

        unset($userData['user_password']);

        $act = new Activities;
        $act->setUserId($userData['user_id']);
        $act->log('login');

        $this->respOK($userData);
    }

    public function password () 
    {
        $user_id =  $this->request->getPost('user_id',['int'], 0);
        $newPassword = $this->request->getPost('new');
        $oldPassword = $this->request->getPost('old');

        $model = new Users;
        $userData = $model->getById($user_id);
        if( !$userData ) 
            $this->respNOK('user tidak ditemukan');

        if ( !$this->security->checkHash($oldPassword, $userData['user_password']) ) 
            $this->respNOK('password lama salah');
            

        $updateData = (object)[
            'user_id'       => $user_id,
            'user_password' => $this->security->hash($newPassword)
        ];

        if ($model->updateRecord($updateData))
        {
            $act = new Activities;
            $act->setUserId($user_id);
            $act->log('change_password');
    
            $this->respOK('Success');
        }

        $this->respNOK('Ganti password tidak berhasil.');
    }

}
