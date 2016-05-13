<?php
 class MCrypt 
{
     //how to use *** begin
     // ENCRYPT example: $encrypted = $mcrypt->encrypt(pkcs5_pad("Text to Encrypt")); 
     // DECRYPT example: $decrypted = pkcs5_unpad($mcrypt->decrypt($encrypted));
     //how to use *** end
    private $iv = '_mF1lm_vnNamViet'; #Same as in JAVA
    private $key = 'mF1lm.vn_NamViet'; #Same as in JAVA


    function  __construct()
    {
    }

    function  encrypt($str) {

      //$key = $this->hex2bin($key);    
      $iv = $this->iv;

      $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

      mcrypt_generic_init($td, $this->key, $iv);
      $encrypted = mcrypt_generic($td, $str);

      mcrypt_generic_deinit($td);
      mcrypt_module_close($td);

      return bin2hex($encrypted);
    }

    function  decrypt($code) {
      //$key = $this->hex2bin($key);
      $code = $this->hex2bin($code);
      $iv = $this->iv;

      $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

      mcrypt_generic_init($td, $this->key, $iv);
      $decrypted = mdecrypt_generic($td, $code);

      mcrypt_generic_deinit($td);
      mcrypt_module_close($td);

      return utf8_encode(trim($decrypted));
    }

    protected function  hex2bin($hexdata) {
      $bindata = '';

      for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
      }

      return $bindata;
    }

    function pkcs5_pad ($text) { 
        $blocksize = 16; 
        $pad = $blocksize - (strlen($text) % $blocksize); 
        return $text . str_repeat(chr($pad), $pad); 
    }

    function pkcs5_unpad($text) { 
        $pad = ord($text{strlen($text)-1}); 
        if ($pad > strlen($text)) return false; 
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false; 
        return substr($text, 0, -1 * $pad); 
    }
}
?>
