<?php
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