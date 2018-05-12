<?php
use Phalcon\Mvc\Controller;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;

class ControllerBase extends Controller {
    protected $log;
    protected $starttime;
    protected $param;

    public function onConstruct() {
        $this->starttime = microtime(1);
        // init log
        $this->log = new FileAdapter($this->config->log->core->path . $this->config->log->core->prefix . date('Ymd'));

        $this->log->info( abs( substr(microtime(1)-$this->starttime,0,8) ).' '.$_SERVER['REQUEST_METHOD'].' - PARAM - ' . file_get_contents("php://input") );

        $raw = file_get_contents("php://input");
        if( !empty($raw) ){
            $this->param = json_decode(trim($raw),1);
            if( $this->param==false ){
                $this->respNOK('missing mandatory');
            }    
        }
    }

    /**
     * check input parameter as mandatory
     */
    public function validMandatory($param) {
        if( is_array($param) ){
            $value = true;
            foreach($param as $x) {
                $r = $this->validMandatory($x);
                if( $r === false ) $value = false;
            }
            return $value;
        }
        
        if( $param === NULL || $param === FALSE || $param == '' ){
            return false;
        }
        
        return true;
    }

    protected function respNOK($msg='') {
        $this->log->error( abs( substr(microtime(1)-$this->starttime,0,8) ).' '.$_SERVER['REQUEST_METHOD'].' - NOK - ' . $msg);
        $this->resp(9, $msg);
    }

    protected function respOK($data=null) {
        $this->log->error( abs( substr(microtime(1)-$this->starttime,0,8) ).' '.$_SERVER['REQUEST_METHOD'].' - OK - SUCCESS');
        $this->resp(1, 'success', $data);
    }

    private function resp($code, $descr, $data=null) {
        $response = array(
            'code'      => $code,
            'message'   => $descr
        );
        if( $data!=null ) $response['data'] = $data;
        echo json_encode($response); exit;
    }

    protected function getPost($key, $default=null){
        return isset($this->param[$key]) ? $this->param[$key] : $default;
    }
}