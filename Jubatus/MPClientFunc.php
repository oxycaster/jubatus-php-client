<?php
class Jubatus_MPClientFunc
{
    protected $_host;
    protected $_port;
    protected $_method;
    protected $_timeout;
    protected $_unpacker;
    
    const MAX_CALL_ID = 65535;
    
    public function __construct($host, $method, $timeout=10)
    {
        $this->_host = $host[0];
        $this->_port = $host[1];
        $this->_method = $method;
        $this->_timeout = $timeout;
        $this->_unpacker = new MessagePackUnpacker();
    }

    public function __call($func_name, $argv)
    {
        
        $socket = fsockopen($this->_host, $this->_port,
            $errno, $errstr, $this->_timeout);
        
        if(!$socket) {
            throw new Exception($errstr);
        } else {
            $i = mt_rand(0, self::MAX_CALL_ID);
            // var_dump(array(0, $i, $this->_method, $argv));
            $send_msg = msgpack_pack(array(0, $i, $this->_method, $argv));
            
            fputs($socket, $send_msg . "\n");
            
            $recv = '';
            while(!feof($socket)) {
                $recv .= fgets($socket, 1024);
            }
            fclose($socket);
            $unpacked = msgpack_unpack($recv);
            
            if(count($unpacked) !== 4) {
                throw new Jubatus_BadRPC();
            } elseif($unpacked[0] !== 1) {
                throw new Jubatus_BadRPC();
            } elseif($unpacked[1] !== $i) {
                throw new Jubatus_BadRPC();
            } elseif(!empty($unpacked[2])){
                if($unpacked[2] == 1) {
                    throw new Jubatus_MethodNotFound();
                } elseif($unpacked[2] == 2) {
                    var_dump($unpacked);
                    throw new Jubatus_TypeMismatch();
                } else {
                    throw new Jubatus_Exception($unpacked[2]);
                }
            } else {
                return $unpacked[3];
            }
        }
    }
}