<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 2017-06-15
 * Time: 21:39
 */
function getUID(){
    if( isset($_SESSION['uid']) ) {
        return $_SESSION['uid'];
    }else{
        return false;
    }
}

function logon(){
    if( !isset($_SESSION['uid'])) {
        return false;
    }else{
        return true;
    }
}

function getDateTime(){
    return date('Y-m-d H:i:s',time());
}

/**

 * @name 可逆加密

 * @param string $string 加密解密字符串

 * @param string $operation 加密(E)or解密(D)

 * @param string $key 密匙

 * @return string

 */
function encrypt2($string,$operation,$key='20020807hH'){
    $key=md5($key);
    $key_length=strlen($key);
    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
    $string_length=strlen($string);
    $rndkey=$box=array();
    $result='';
    for($i=0;$i<=255;$i++){
        $rndkey[$i]=ord($key[$i%$key_length]);
        $box[$i]=$i;
    }
    for($j=$i=0;$i<256;$i++){
        $j=($j+$box[$i]+$rndkey[$i])%256;
        $tmp=$box[$i];
        $box[$i]=$box[$j];
        $box[$j]=$tmp;
    }
    for($a=$j=$i=0;$i<$string_length;$i++){
        $a=($a+1)%256;
        $j=($j+$box[$a])%256;
        $tmp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$tmp;
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }
    if($operation=='D'){
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
            return substr($result,8);
        }else{
            return'';
        }
    }else{
        return str_replace('=','',base64_encode($result));
    }
}

/**

 * @name 随机生成一个字符串

 * @return string

 */
function GetRandString($length = 20,$type = ''){
    switch ($type) {
        case 'WORD':
            $seeds = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'BIGWORD':
            $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'WORDx':
            $seeds = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            break;
        case 'BIGWORDx':
            $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            break;
        case 'x':
            $seeds = '0123456789';
            break;
        default:
            $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            break;
    }
    $str = '';
    $seeds_count = strlen ( $seeds );
    for($i = 0; $length > $i; $i ++) {
        $str .= $seeds {mt_rand ( 0, $seeds_count - 1 )};
    }
    return $str;
}

function parseJqJSON2($original_arr){
    $t=0;
    foreach ($original_arr as $key=>$value) {
        $t ++;
        $p[$t]['key'] = $key;
        $p[$t]['value'] = $value;
        $arr[$value['name']] = $value['value'];
    }
    return $arr;
}

function parseDbObj2SimpleArr($original_arr)
{
    $new_arr = array();
    foreach ($original_arr as $key=>$value) {
        $new_arr[$value['gateway_id']] = true;
    }
    return $new_arr;
}

function ApiResponseHeader($type = 'json'){
    header('Content-Type: text/json');
}

function getRootURL(){
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return $http_type . $_SERVER['HTTP_HOST'];
}
/**
 * 格式化文件大小显示
 *
 * @param int $size
 * @return string
 */
function formatSize($size)
{
    $prec = 3;
    $size = round(abs($size));
    $units = array(
        0 => " B ",
        1 => " KB",
        2 => " MB",
        3 => " GB",
        4 => " TB"
    );
    if ($size == 0)
    {
        return str_repeat(" ", $prec) . "0$units[0]";
    }
    $unit = min(4, floor(log($size) / log(2) / 10));
    $size = $size * pow(2, -10 * $unit);
    $digi = $prec - 1 - floor(log($size) / log(10));
    $size = round($size * pow(10, $digi)) * pow(10, -$digi);

    return $size . $units[$unit];
}

function LOGGER($content,$type)
{
    $Log = M('log');
    $Log->data(array(
        "content" => $content,
        "type" => $type,
        "uid" => getUID(),
        "log_at" => getDateTime(),
        "log_ip" => get_client_ip(),
    ))->add();
};


function getCustomFieldValue($single_product_arr,$custom_field_name)
{
    $custom_fields = $single_product_arr['customfields']['customfield'];
    for ($i=0;$i<count($custom_fields);$i++)
    {
        if ($custom_fields[$i]['name'] == $custom_field_name)
        {
            return $custom_fields[$i]['value'];
        }
    }
    return false;
}

function checkCustomFieldExists($single_product_arr,$custom_field_name)
{
    $custom_fields = $single_product_arr['customfields']['customfield'];
    for ($i=0;$i<count($custom_fields);$i++)
    {
        if ($custom_fields[$i]['name'] == $custom_field_name)
        {
            return true;
        }
    }
    return false;
}