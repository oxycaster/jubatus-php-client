<?php
/**
 * jubatus-php-client: Jubatus PHP Client Library
 * Copyright (C) 2011 Hironao Sekine
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

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