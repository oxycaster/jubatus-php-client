<?php
class Jubatus_Config
{
    public function __construct($dict_config)
    {
        $this->_method = $dict_config['method'];
        $converter_config = $dict_config['converter'];
        $this->_converter = array(
            Jubatus_Config::pack_string_filter_types($converter_config),
            Jubatus_Config::pack_string_filter_rules($converter_config),
            Jubatus_Config::pack_num_filter_types($converter_config),
            Jubatus_Config::pack_num_filter_rules($converter_config),
            Jubatus_Config::pack_string_types($converter_config),
            Jubatus_Config::pack_string_rules($converter_config),
            Jubatus_Config::pack_num_types($converter_config),
            Jubatus_Config::pack_num_rules($converter_config),
        );
    }

    public function pack()
    {
        return array($this->_method, $this->_converter);
    }

    public static function pack_string_filter_types(&$c_config)
    {
        return $c_config['string_filter_types'];
    }
    public static function pack_num_filter_types(&$c_config)
    {
        return $c_config['num_filter_types'];
    }

    public static function unpack_string_filter_types($config, &$c_config)
    {
        return $c_config['string_filter_types'] = $config;
    }
    public static function unpack_num_filter_types($config, &$c_config)
    {
        return $c_config['num_filter_types'] = $config;
    }

    public static function pack_string_filter_rules(&$c_config)
    {
        return array_map(
            function($rule){
                return array(
                    $rule['key'],
                    $rule['type'],
                    $rule['suffix'],
                );
            }, $c_config['string_filter_rules']);
    }
    public static function pack_num_filter_rules(&$c_config)
    {
        return array_map(
            function($rule){
                return array(
                    $rule['key'],
                    $rule['type'],
                    $rule['suffix'],
                );
            }, $c_config['num_filter_rules']);
    }

    public static function unpack_string_filter_rules($config, &$c_config)
    {
        $c_config['string_filter_rules'] = array_map(
            function($rule) {
                return array(
                    'key' => $rule[0],
                    'type' => $rule[1],
                    'suffix' => $rule[2]
                );
            }, $config
        );
    }
    
    public static function unpack_num_filter_rules($config, &$c_config)
    {
        $c_config['num_filter_rules'] = array_map(
            function($rule) {
                return array(
                    'key' => $rule[0],
                    'type' => $rule[1],
                    'suffix' => $rule[2]
                );
            }, $config
        );
    }



    public static function pack_string_types(&$c_config)
    {
        return $c_config['string_types'];
    }

    public static function unpack_string_types($config, &$c_config)
    {
        $c_config['string_types'] = $config;
    }

    public static function pack_string_rules(&$c_config)
    {
        return array_map(
            function($rule) {
                return array($rule['key'], $rule['type'], $rule['sample_weight'], $rule['global_weight']);
            }, $c_config['string_rules']
        );
    }

    public static function unpack_string_rules($config, &$c_config)
    {
        $c_config['string_rules'] = array_map(
            function($rule) {
                return array(
                    'key' => $rule[0],
                    'type' => $rule[1],
                    'sample_weight' => $rule[2],
                    'global_weight' => $rule[3],
                );
            }, $config
        );
    }

    public static function pack_num_types(&$c_config)
    {
        return $c_config['num_types'];
    }

    public static function unpack_num_types($config, &$c_config)
    {
        $c_config['num_types'] = $config;
    }

    public static function pack_num_rules(&$c_config)
    {
        return array_map(
            function($rule) {
                return array($rule['key'], $rule['type']);
            }, $c_config['num_rules']
        );
    }

    public static function unpack_num_rules($config, &$c_config)
    {
        $c_config['num_rules'] = array_map(
            function($rule) {
                return array('key' => $rule[0], 'type' => $rule[1]);
            }, $config
        );
    }
}