<?php

class InboxController extends ControllerBase {

    public function onConstruct() {
        parent::onConstruct();
    }

    /**
     * API get inbox
     */
    public function message() {
        // input
        $username   = $this->getPost('username');
        $page       = $this->getPost('page', 1);
        $limit      = $this->getPost('limit', 10);
        $filter     = $this->getPost('filter');

        // validate mandatory input
        $valid = $this->validMandatory([$username]);
        if( !$valid ) $this->respNOK('missing mandatory');

        $inbox = new Inbox;
        // get message
        $messages = $inbox->getAll($username, $page, $limit, $filter);
        if( !$messages ) $this->respNOK('result not found');

        $this->respOK($messages);
    }

    /**
     * API change status to read
     */
    public function read(){
        // input
        $username   = $this->getPost('username');
        $inboxId    = $this->getPost('id');
        
        // validate mandatory input
        $valid = $this->validMandatory([$username, $inboxId]);
        if( !$valid ) $this->respNOK('missing mandatory');

        $inbox = new Inbox;
        // get message
        $result = $inbox->read($inboxId, $username);
        if( !$result ) $this->respNOK('internal error');

        $this->respOK();
    }

    /**
     * API delete message
     */
    public function delete(){
        // input
        $username   = $this->getPost('username');
        $inboxId    = $this->getPost('id');
        
        // validate mandatory input
        $valid = $this->validMandatory([$username, $inboxId]);
        if( !$valid ) $this->respNOK('missing mandatory');

        $inbox = new Inbox;
        // get message
        $result = $inbox->delete($inboxId, $username);
        if( !$result ) $this->respNOK('internal error');

        $this->respOK();
    }
}
