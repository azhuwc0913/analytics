<?php

function strToHex($string)//字符串转十六进制
{ 
    $hex="";
    for($i=0;$i<strlen($string);$i++)
    $hex.=dechex(ord($string[$i]));
    $hex=strtoupper($hex);
    return $hex;
}   
 
function hexToStr($hex)//十六进制转字符串
{   
    $string=""; 
    for($i=0;$i<strlen($hex)-1;$i+=2)
    $string.=chr(hexdec($hex[$i].$hex[$i+1]));
    return  $string;
}

function authData($action,$data=array(),$url){
//    echo $url;
        $http = new \Think\Http();
        $AES = new \Think\AES();
        $url = $url;
        switch ($action) {
            case 1:
                $url = $url;
                break;
            default:
                break;
        }
        $result = $http->http($url, array('data'=>$AES->encode(json_encode($data))));
        $arr = json_decode($AES->decode($result));
//        var_dump($arr->data->wx_info);
        
        if ($arr->code==0) {
            if ($arr->data->time + 300 > time()&&$arr->data->time - 300 < time()) {
//                echo '时间之内';
//                print_r(json_decode($arr->data->wx_info));
                return array('code'=>'0','data'=>json_decode(stripslashes($arr->data->wx_info)),'ismob'=>$arr->data->ismob,'isavailable'=>$arr->data->isavailable);
            }else{
                return array('code'=>'timeout');
            }
        }else{
            return array('code'=>$arr->code);
        }
//        print_r();
}
    
function decryptData($data){
    $data=json_decode($data);
    if ($data->time + 300 > time()&&$data->time - 300 < time()) {
        return $data;
    }else{
        return FALSE;
    }
}

function object_array($array) {  
    if(is_object($array)) {  
        $array = (array)$array;  
     } if(is_array($array)) {  
         foreach($array as $key=>$value) {  
             $array[$key] = object_array($value);  
             }  
     }  
     return $array;  
}


function dd($data){
    echo '<pre>';
    var_dump($data);
    die;
}
?>