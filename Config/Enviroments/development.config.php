<?php

/**
 * 0 = tắt
 * 1 = PHP, Database trừ trên service
 * 10 = tất cả
 */
$exports['debugMode'] = 0;

//kết nối database
$exports['db'] = array(
    'type' => 'mysqli',
    'host' => '127.0.0.1',
    'name' => 'ris',
    'user' => 'ehr',
    'pass' => 'ehr'
);

$exports['cryptSecrect'] = 'abM)(*2312';

date_default_timezone_set('Asia/Bangkok');
