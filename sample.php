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

require_once 'Jubatus.php';

defined('LIB_PATH')
    || define('LIB_PATH', realpath(dirname(__FILE__) . '/../Jubatus'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIB_PATH),
    get_include_path(),
)));

function get_most_likely($estm)
{
    $ans = NULL;
    $prob = NULL;
    $result = array();
    $result[0] = '';
    $result[1] = 0;
    foreach($estm as $res) {
        if(empty($prob) || $res[1] > $prob) {
            $ans = $res[0];
            $prob = $res[1];
            $result[0] = $ans;
            $result[1] = $prob;
        }
    }
    return $result;
}

$servers = 'localhost:9199';
$name = 'tutorial';
$classify = new Jubatus($servers, $name);

$config = array(
    'converter' => array(
        'string_filter_types' => array(
            'detag' => array(
                'method' => 'regexp',
                'pattern' => '<[^>]*>',
                'replace' => '',
            ),
        ),
        'string_filter_rules' => array(
            array(
                'key' => 'message',
                'type'=>'detag',
                'suffix'=>'-detagged'
            ),
        ),
        'num_filter_types' => new stdClass(),
        'num_filter_rules' => array(),
        'string_types' => new stdClass(),
        'string_rules' => array(
            array(
                'key' => 'message-detagged',
                'type' => 'space',
                'sample_weight' => 'bin',
                'global_weight' => 'bin'
            ),
        ),
        'num_types' => new stdClass(),
        'num_rules' => array(),
    ),
    'method' => 'PA'
);
$classify->set_config($config);
// var_dump($classify->get_config());

foreach(file('train.dat') as $row) {
    list($label, $file) = explode(',', $row);
    $dat = file_get_contents(trim($file));
    $classify->train(
        array(
            array(
                $label,
                array(
                    array(
                        array(
                            'message',
                            $dat,
                        ),
                    ),
                )
            ),
        ));
    // var_dump($classify->get_status());  // not implemented.
}

foreach(file('test.dat') as $row) {
    list($label, $file) = explode(',', $row);
    $dat = file_get_contents(trim($file));
    $ans = $classify->classify(
        array(
            array(
                array(
                    array(
                        'message',
                        $dat,
                    )
                )
            )
        ));
    if(!empty($ans)) {
        $estm = get_most_likely($ans[0]);
        if($label == $estm[0]){
            $result = 'OK';
        }else{
            $result = 'NG';
        }
        echo $result . ',' . $label . ', ' . $estm[0] . ', ' . $estm[1] . "\n";
    }
}
