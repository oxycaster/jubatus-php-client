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

class Jubatus_Accessor
{
    protected $_name;
    protected $_servers;
    
    public function __construct($hosts, $name)
    {
        $this->_servers = array_map(function($x){ return explode(':', $x); }, explode(',', $hosts));
        $this->_name = $name;
    }

    public function choose_one()
    {
        $i = mt_rand( 0, (count($this->_servers) - 1) );
        return $this->_servers[$i];
    }
    
    public function save($id)
    {
        $f = new Jubatus_MPClientFunc($this->choose_one(), 'save');
        try {
            list($success, $retval, $error) = $f->classifier($this->_name, $id);
            if(!$success) {
                throw Jubatus_Exception($error);
            }
            return $success;
        }catch(Jubatus_Exception $e){
            return $e;
        }
    }
    
    public function load($id)
    {
        $f = new Jubatus_MPClientFunc($this->choose_one(), 'load');
        try {
            list($success, $retval, $error) = $f->classifier($this->_name, $id);
            if(!$success) {
                throw Jubatus_Exception($error);
            }
            return $success;
        } catch (Jubatus_Exception $e) {
            return $e;
        }
    }
}