<?php
/**
 * jubatus-php-client: Jubatus PHP Client Library
 * Copyright (C) 2011 Preferred Infrastracture and Nippon Telegraph and Telephone Corporation.
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

require_once 'Jubatus/MPClientFunc/BadRPCException.php';
require_once 'Jubatus/MPClientFunc/MethodNotFoundException.php';
require_once 'Jubatus/MPClientFunc/TypeMismatchException.php';

class Jubatus_MPClientFunc
{
    protected $_host;
    protected $_port;
    protected $_method;
    protected $_timeout;
    
    const MAX_CALL_ID = 65535;
    
    public function __construct($host, $method, $timeout=10)
    {
        $this->_host = $host[0];
        $this->_port = $host[1];
        $this->_method = $method;
        $this->_timeout = $timeout;
    }

    public function __call($func_name, $argv)
    {
        
        $socket = fsockopen($this->_host, $this->_port,
            $errno, $errstr, $this->_timeout);
        
        if(!$socket) {
            throw new Jubatus_Exception($errstr);
        } else {
            $i = mt_rand(0, self::MAX_CALL_ID);
            $send_msg = msgpack_pack(array(0, $i, $this->_method, $argv));
            
            fputs($socket, $send_msg . "\n");
            $recv = '';
            while(!feof($socket)) {
                $recv .= fgets($socket, 1024);
            }
            fclose($socket);
            
            $unpacked = msgpack_unpack($recv);
            if(count($unpacked) !== 4) {
                throw new Jubatus_MPClientFunc_BadRPCException();
            } elseif($unpacked[0] !== 1) {
                throw new Jubatus_MPClientFunc_BadRPCException();
            } elseif($unpacked[1] !== $i) {
                throw new Jubatus_MPClientFunc_BadRPCException();
            } elseif(!empty($unpacked[2])){
                if($unpacked[2] == 1) {
                    throw new Jubatus_MPClientFunc_MethodNotFoundException();
                } elseif($unpacked[2] == 2) {
                    var_dump($unpacked);
                    throw new Jubatus_MPClientFunc_TypeMismatchException();
                } else {
                    throw new Jubatus_Exception($unpacked[2]);
                }
            } else {
                return $unpacked[3];
            }
        }
    }
}