<?php
class AES {

    /**
     *
     * @param string $string 需要加密的字符串
     * @param string $key 密钥
     * @return string
     */
    public static function encrypt($string, $key,$method='AES-128-CBC')
    {

        $data = openssl_encrypt($string, $method, $key, OPENSSL_RAW_DATA,$key);
        $data = base64_encode($data);
        return $data;
    }


    /**
     * @param string $string 需要解密的字符串
     * @param string $key 密钥
     * @return string
     */
    public static function decrypt($string, $key,$method='AES-128-CBC')
    {
        $encrypted = base64_decode($string);
        $decrypted = openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA,$key);

        return $decrypted;
    }
}