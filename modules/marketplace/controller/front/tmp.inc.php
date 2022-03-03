<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Controller\Front;

class Tmp extends \RS\Controller\Front
{

	
    function actionCreate()
    {
        //create new private and public key
        $private_key_res = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);
        $details = openssl_pkey_get_details($private_key_res);

        openssl_pkey_export_to_file($private_key_res, 'rs_private.pem', 'myWeakPass');
        file_put_contents('rs_public.pem', $details['key']);


        print "<pre>";
        print_r($details);
        echo 123;
        die;
    }


    function actionTest()
    {
        $pub  = file_get_contents('rs_public.pem');
        $priv = openssl_pkey_get_private(file_get_contents('rs_private.pem'), 'myWeakPass');
        $signature = "";

        $ok = openssl_sign("2mydata one two tree", $signature, $priv);

        if(!$ok) echo openssl_error_string();

        echo base64_encode($signature);
        echo "<br>";

        $si_ok = openssl_verify("2mydata one two tree", $signature, $pub);
        if($si_ok == 1){
            echo "Signature correct!";
        }
        else{
            echo $si_ok;
        }
        die;
    }
}

