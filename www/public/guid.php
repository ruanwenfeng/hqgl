<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/26
 * Time: 17:03
 */

function create_guid($namespace = '') {
    $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
//    $guid = '{' .
//        substr($hash, 0, 8) .
//        '-' .
//        substr($hash, 8, 4) .
//        '-' .
//        substr($hash, 12, 4) .
//        '-' .
//        substr($hash, 16, 4) .
//        '-' .
//        substr($hash, 20, 12) .
//        '}';
    return $guid = $hash;
}
$i =100;
while ($i--){
    echo create_guid().'<br />';
}
