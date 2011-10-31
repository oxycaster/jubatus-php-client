<?php
require_once 'Jubatus/Accsessor.php';
require_once 'Jubatus/Config.php';
require_once 'Jubatus/MPClientFunc.php';
require_once 'Jubatus/Exception.php';

class Jubatus extends Jubatus_Accessor
{
    public function set_config($config)
    {
        $f = new Jubatus_MPClientFunc($this->choose_one(), 'set_config');
        try {
            $juba_config = new Jubatus_Config($config);
            list($success, $retval, $error) = $f->set_config($this->_name, $juba_config->pack());
            if(!$success) {
                throw new Jubatus_Exception($error);
            }
        } catch (Jubatus_Exception $e) {
           return $e;
        }
    }

    public function get_config()
    {
        $f = new Jubatus_MPClientFunc($this->choose_one(), 'get_config');
        list($success, $retval, $error) = $f->get_config();
        if(!$success){
            throw new Jubatus_Exception($error);
        }
        
        $c = array('converter' => array());
        $c['method'] = $retval[0];
        Jubatus_Config::unpack_string_filter_types($retval[1][0], $c['converter']);
        Jubatus_Config::unpack_string_filter_rules($retval[1][1], $c['converter']);
        Jubatus_Config::unpack_num_filter_types($retval[1][2], $c['converter']);
        Jubatus_Config::unpack_num_filter_rules($retval[1][3], $c['converter']);
        Jubatus_Config::unpack_string_types($retval[1][4], $c['converter']);
        Jubatus_Config::unpack_string_rules($retval[1][5], $c['converter']);
        Jubatus_Config::unpack_num_types($retval[1][6], $c['converter']);
        Jubatus_Config::unpack_num_rules($retval[1][7], $c['converter']);
        return $c;
    }

    public function train($label2data)
    {
        $f = new Jubatus_MPClientFunc($this->choose_one(), 'train');
        try {
            list($success, $retval, $error) = $f->train($this->_name, $label2data);
            if(!$success) {
                throw Jubatus_Exception($error);
            }
            return $retval;
        } catch(Jubatus_Exception $e) {
            return $e;
        }
    }

    public function classify($data)
    {
        $f = new Jubatus_MPClientFunc($this->choose_one(), 'classify');
        try {
            list($success, $retval, $error) = $f->classify($this->_name, $data);
            if(!$success) {
                throw Jubatus_Exception($error);
            }
            return $retval;
        } catch(Jubatus_Exception $e) {
            return $e;
        }
    }

    public function get_status()
    {
        $f = new Jubatus_MPClientFunc($this->choose_one(), 'get_status');
        try {
            list($success, $retval, $error) = $f->get_status();
            if(!$success) {
                throw new Jubatus_Exception($error);
            }
        } catch(Jubatus_Exception $e) {
            return $e;
        }
    }
}