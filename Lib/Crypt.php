<?php

namespace Lib;

class Crypt {

    private $Algo;

    public function __construct($Key = 'MyDefaultKey@2017', $Algo = MCRYPT_BLOWFISH) {
        $this->Key = substr($Key, 0, mcrypt_get_key_size($Algo, MCRYPT_MODE_ECB));
        $this->Algo = $Algo;
    }

    function encrypt($data) {
        $iv_size = mcrypt_get_iv_size($this->Algo, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $crypt = mcrypt_encrypt($this->Algo, $this->Key, $data, MCRYPT_MODE_ECB, $iv);
        return trim(base64_encode($crypt));
    }

    function decrypt($data) {
        $iv_size = mcrypt_get_iv_size($this->Algo, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $crypt = base64_decode($data);
        $decrypt = mcrypt_decrypt($this->Algo, $this->Key, $crypt, MCRYPT_MODE_ECB, $iv);
        return $decrypt;
    }

}
