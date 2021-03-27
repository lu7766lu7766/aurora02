<?php
class Crypt{

//    static private $key1 ='$1$#$%543gTt-TLt(+).==';
//    static private $key2 ='$5$QtMy%Pu@5$$5gsT0-==';
//
//
//    static public function encrypt($input){
//        //return md5(self::$key2.md5(self::$key1.$input));
//        return md5(self::$key2.crypt($input,self::$key1));
//    }
//
//    static public function decrypt($input){
//        //return md5(self::$key2.md5(self::$key1.$input));
//        return md5(self::$key2.crypt($input,self::$key1));
//    }

    static private $key1 ='$1$#$%543gTt-TLt(+)../g';
    public static function encrypt($input) {

        return empty($input)? "":Crypt::encrypt1(Crypt::encrypt1(Crypt::encrypt1($input)));
    }

    public static function encrypt1($input) {
        $key = Crypt::$key1;
        $size = mcrypt_get_block_size('des', 'ecb');
        $string = mb_convert_encoding($input, 'GBK', 'UTF-8');
        $pad = $size - (strlen($string) % $size);
        $string = $string . str_repeat(chr($pad), $pad);
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    public static function decrypt($input) {
        return empty($input)? "": Crypt::decrypt1(Crypt::decrypt1(Crypt::decrypt1($input)));
    }

    private static function decrypt1($input) {
        $key = Crypt::$key1;
        $string = base64_decode($input);
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $pad = ord($decrypted{strlen($decrypted) - 1});
        if($pad > strlen($decrypted)) {
            return false;
        }
        if(strspn($decrypted, chr($pad), strlen($decrypted) - $pad) != $pad) {
            return false;
        }
        $result = substr($decrypted, 0, -1 * $pad);
        $result = mb_convert_encoding($result, 'UTF-8', 'GBK');
        return $result;
    }


}
?>