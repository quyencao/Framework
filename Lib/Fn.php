<?php

use Lib\Bootstrap;
use Lib\SQL\EntitySet;

function url($path = '', $params = array()) {
    $app = Bootstrap::getInstance();
    $url = ($app->rewriteBase == '/' ? '' : $app->rewriteBase) . $path;
    $sep = '?';
    foreach ($params as $k => $v) {
        $url .= "{$sep}{$k}={$v}";
        $sep = '&';
    }

    return $url;
}

function urlAbsolute($path = '', $params = array()) {
    return Bootstrap::getInstance()->fullSiteAddr . url($path, $params);
}

/**
 * 
 * @param array $arr
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function arrData($arr, $key, $default = null) {
    return isset($arr[$key]) ? $arr[$key] : $default;
}

/**
 * Chuyển mảng về tham số get
 * @param array $arr
 * @return string
 */
function encodeForm($arr) {
    $ret = '';
    foreach ($arr as $k => $v) {
        $ret .= $ret ? "&{$k}={$v}" : "{$k}={$v}";
    }
    return $ret;
}

define('XPATH_STRING', 1);
define('XPATH_ARRAY', 2);
define('XPATH_DOM', 3);

/**
 * 
 * @param SimpleXmlElement $dom
 * @param type $xpath
 * @param type $method
 * @return SimpleXmlElement
 */
function xpath($dom, $xpath, $method = 1) {
    if ($dom instanceof SimpleXMLElement == false) {
        switch ($method) {
            case XPATH_STRING:
                return '';
            case XPATH_ARRAY:
                return array();
            case XPATH_DOM:
                return null;
        }
    }

    $r = $dom->xpath($xpath);
    switch ($method) {
        case XPATH_ARRAY:
            return $r;
        case XPATH_DOM:
            return $r[0];
        case XPATH_STRING:
        default:
            return count($r) ? (string) $r[0] : null;
    }
}

/**
 * Lấy config từ thư mục Config/
 * @param string $fileName
 * @return mixed
 */
function getConfig($fileName) {
    $fullPath = BASE_DIR . '/Config/' . $fileName;
    if (strpos($fileName, '.config.php') !== false) {
        require $fullPath;
        if (!isset($exports)) {
            throw new Exception($fullPath . ' phải có biến $exports');
        }
        return $exports;
    } else if (strpos($fileName, '.xml') !== false) {
        $exports = array();
        $dom = simplexml_load_file($fullPath);
        if (!$dom) {
            return $exports;
        }
        foreach ($dom->settings->children() as $group) {
            foreach ($group->children() as $field) {
                $exports[strval($field->id)] = strval($field->value);
            }
        }

        return $exports;
    }

    throw new Exception($fullPath . ' không hợp lệ (chỉ hỗ trợ .config.php hoặc .xml)');
}

/**
 * Xử lý SQL inject, XSS bằng tay
 * @param type $str
 * @return type
 */
function escapeStr($str) {
    $str = stripslashes($str);
    $str = str_replace("&", '&amp;', $str);
    $str = str_replace('<', '&lt;', $str);
    $str = str_replace('>', '&gt;', $str);
    $str = str_replace('"', '&#34;', $str);
    $str = str_replace("'", '&#39;', $str);

    return $str;
}

function deEscapeStr($str) {
    $str = stripslashes($str);
    $str = str_ireplace('&amp;', '&', $str);
    $str = str_replace('&lt;', '<', $str);
    $str = str_replace('&gt;', '>', $str);
    $str = str_replace('&#34;', '"', $str);
    $str = str_replace('&#39;', "'", $str);

    return $str;
}

/*
 * Chuyen doi tieng viet co dau thanh khong dau
 * 
 */

function tiengVietKhongDau($str) {
// In thường
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
// In đậm
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);
    return $str; // Trả về chuỗi đã chuyển
}

/**
 * tính toán số giây của dateinterval
 * @param DateInterval $delta
 */
function dateIntervalToSec(DateInterval $delta) {
    return ($delta->s) + ($delta->i * 60) + ($delta->h * 60 * 60) + ($delta->d * 60 * 60 * 24) + ($delta->m * 60 * 60 * 24 * 30) + ($delta->y * 60 * 60 * 24 * 365);
}

function calculatorDuration($d1, $d2) {
    if ($d2 == '2100-01-01 23:59:59') {
        return 0;
    }

    $date1 = date_create($d1);
    $date2 = date_create($d2);
    $diff = date_diff($date1, $date2);
    return ($diff->days) + 1;
}

function checkStateTask($d1, $d2) {
    $date1 = date_create($d1);
    $date2 = date_create($d2);
    $diff = date_diff($date2, $date1);

    return $diff->format("%R%a") < 0 ? 'off' : 'on';
}

function countTimeIn($d1, $d2) {
    $date1 = date_create($d1);
    $date2 = date_create($d2);
    $diff = date_diff($date2, $date1);
    return $diff->format("%R%a");
}

function calculatorActiveEndDatedmY($ad_startdate, $ad_duration) {
    // TH lặp mãi mãi
    if (!$ad_duration) {
        return " mãi mãi";
    } else {
        $date = \DateTime::createFromFormat('Y-m-d h:i:s', $ad_startdate);
        $date->add(new \DateInterval('P' . ($ad_duration - 1) . 'D'));
        return $date->format('d/m/Y');
    }
}

function formatDateYmdFromdmY($ad_startdate) {
    $date = \DateTime::createFromFormat('d/m/Y', $ad_startdate);
    return $date->format('Y-m-d') . " 00:00:00";
}

function unicode_to_nosign($str) {
    $ret_str = Array();

    $unicode = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    $nosign = preg_split("/\,/", 'a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,e,e,e,e,e,e,e,e,e,e,e,i,i,i,i,i,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,u,u,u,u,u,u,u,u,u,u,u,y,y,y,y,y,d,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,E,E,E,E,E,E,E,E,E,E,E,I,I,I,I,I,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,U,U,U,U,U,U,U,U,U,U,U,Y,Y,Y,Y,Y,D');

    foreach ($unicode as $key => $val)
        $ret_str[$val] = $nosign[$key];

    return strtr($str, $ret_str);
}

/**
 * 
 * @param type $status
 * @param type $data
 * @return type
 */
function result($status, $data = null) {
    return [
        'status' => $status,
        'data' => $data
    ];
}

function uid() {
    return uniqid();
}

function composite_to_unicode($str) {
    ///unicode
    $unicode = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    //unicode to hop
    $composite = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    foreach ($composite as $key => $val)
        $ret_str[$val] = $unicode[$key];

    return strtr($str, $ret_str);
}
