<?php
class Logging extends \Phalcon\Mvc\Model {

    protected $database             = 'tama_morpheus_logs';
    protected $tableClickTemplate   = 'log_click_template';
    protected $tableClickPrefix     = 'log_click_';
    protected $tableCallbackTemplate= ''; 
    protected $tableCallbackPrefix  = ''; 
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}
    
    private function getTableClickName($date){
        return $this->tableClickPrefix.date('Ymd', strtotime($date));
    }

    private function getTableCallbackName(){
        return $this->tableCallbackPrefix.date('Ymd', strtotime($date));
    }

    private function createTableClick( $tablename ){
        $this->db->execute('create table if not exists '.$this->database.'.'.$tablename.' like '.$this->database.'.'.$this->tableClickTemplate);
    }

	public function click($data){
        if( !isset($data['date']) ) $data['date'] = date('Y-m-d H:i:s');

        $query = sprintf("INSERT INTO %s ( 
                click_datetime,
                click_transaction_id,
                click_adnet_id,
                click_client_id,
                click_campaign_id,
                click_sub_id,
                click_referer,
                click_ua,
                click_ip,
                click_req_param,
                click_cost_adnet,
                click_cost_client,
                click_ratio,
                click_adnet_postback
            ) VALUES 
            ( 
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )",
            $this->database.'.'.$this->getTableClickName($data['date']), 
            $data['date'],
            $data['trxId'],
            $data['networkId'],
            $data['clientId'],
            $data['campaignId'],
            $data['subId'],
            $data['referer'],
            $data['ua'],
            $data['ip'],
            $data['param'],
            $data['cost_adnet'],
            $data['cost_client'],
            $data['ratio'],
            $data['postback']
        );

        try {
            $result = $this->db->execute($query);
        } catch(Exception $e) {
            if( stripos( $e->getMessage(), 'SQLSTATE[42S02]' ) !==false ){
                // table not exist
                $this->createTableClick( $this->getTableClickName($data['date']) );
                return $this->click($data);
            }
            elseif( stripos( $e->getMessage(), 'SQLSTATE[23000]' ) !==false ){
                // duplicate trxid
                return 'DUPLICATE';
            }
            else{
                // connection error or unknown error
                // write file buffer
                $config = $this->getDi()->getShared('config');

                file_put_contents(
                    $config->buffer->click . date('YmdHi'),
                    json_encode($data)."\n",
                    FILE_APPEND | LOCK_EX
                );
            }
        }

        return true;
    }
}