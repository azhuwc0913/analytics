<?php
namespace Center\Controller;
use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;
 header("Content-Type: text/html; charset=UTF-8");
class IndexController extends Controller {
//    const USER_AUTH_URL          =   'http://shop.pingmin8.com/index.php?g=Api&m=PMApi&a=MemberConfig';
    public function index(){
//        $aes = new \Think\AES;
//        echo base64_encode($aes->encode('abcd123!@#/34504'));
//        print_r($_REQUEST);
//        $a = M('users','center_')->select();
//        var_dump($a);
    }
    public function createMenu(){
        $http = new \Think\Http;
        $token = $this->getToken();
        $array = array('button'=>array(
//            array('type'=>'click',
//            'name'=>'微商城',
//            'key'=>'WESHOP'),
            
            array('type'=>'click',
            'name'=>'通告中心',
            'key'=>'ANNOUNCEMENT'),
            
            array('type'=>'click',
            'name'=>'个人风采',
            'key'=>'ARTIST')
            )
        );
//        echo ;
        $post = $http->httpPost('https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$token,json_encode($array,JSON_UNESCAPED_UNICODE));
        var_dump($post);
    }
    
    
    public function deposit() {
        $user = M('users','center_')->where('`openid` =  \''.  session('openid').'\' and `level` =1')->find();
//        var_dump($user);
        if (IS_POST) {
            if (I('post.money')<20 || I('post.money')>200 || I('post.money')> $user['deposit']) {
                echo json_encode(array('result'=>FALSE));
            }  else {
                if (true) {
                    $dec = M('users','center_')->where('`openid` =  \''.  session('openid').'\' and `level` =1')->setDec('deposit',I('post.money'));
                    if ($dec) {
                        $userafter = M('users','center_')->where('`openid` =  \''.  session('openid').'\' and `level` =1')->find();
                        $dec = M('deposit_order')->add(array('uid'=>$user['id'],'timeline'=>time(),'money'=>I('post.money'),'before'=>$user['deposit'],'after'=>$userafter['deposit']));
                        echo json_encode(array('result'=>TRUE));
                    }else{
                        echo json_encode(array('result'=>FALSE));
                    }
                }
                
            }
        }
//        var_dump($_REQUEST);
    }
    public function help() {
        $this->display();
    }
    public function getQRcode() {
        echo $this->findQRcode();
    }
    
    function findQRcode() {
        $path = './QRcode/';
        $file = $path.I('get.openid').'.jpg';
//        echo S('wx_info'.session('openid'))['headimgurl'];
//        return $this->ticket($file);
        if (!file_exists($path.I('get.openid').'_p.jpg')) {
            return $this->ticket($file);
        }  else {
            return TRUE;
        }
    }
    function face(){
        
    }
    
    function ticket($file){
        $ticket = '';
        $http = new \Think\Http;
        $user = M('users','center_')->where('`openid` =  \''.I('get.openid').'\' and `ticket` IS NOT NULL')->find();
        if ($user) {
            $ticket = $user['ticket'];
        }else{
            $user = M('users','center_')->where(array('openid'=>I('get.openid')));
            $data = $user->find();
            $token = $this->getToken();
            $post = $http->httpPost('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$token,'{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$data['id'].'}}}');
            $json = json_decode($post);

            $ticket = $json->ticket;
            $user->save(array('ticket'=>$json->ticket));
        }
        
//        echo $ticket;
//        exit();
        
        $get = $http->httpGetDownload('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket));
        echo $filename = $file;
        $local_file = fopen($filename, 'w');
        if (FALSE !== $local_file) {
            if (FALSE !== fwrite($local_file, $get['body'])) {
                fclose($local_file);
            }
        }
        //
        //
        $t = $http->httpGetDownload(S('wx_info'.session('openid'))['headimgurl']);
//        print_r($t['header']['content_type']);
        
        
        $tfilename;
        if ($t['header']['content_type']=='image/jpg') {
            $tfilename = './QRcode/'.session('openid').'_f.jpg';
        }else if ($t['header']['content_type']=='image/jpeg') {
            $tfilename = './QRcode/'.session('openid').'_f.jpg';
        }else if ($t['header']['content_type']=='image/png') {
            $tfilename = './QRcode/'.session('openid').'_f.png';
        }else if ($t['header']['content_type']=='image/gif') {
            $tfilename = './QRcode/'.session('openid').'_f.gif';
        }
//        echo 'abc';
//        exit();
//        $tfilename = './QRcode/'.session('openid').'_f.png';
        $local_file = fopen($tfilename, 'w');
        if (FALSE !== $local_file) {
            if (FALSE !== fwrite($local_file, $t['body'])) {
                fclose($local_file);
            }
        }
        //
        //
        
//        print_r(pathinfo($tfilename));
//        exit();
        //文件名如a.php，本例适应显示方式动态合并，须GD库支持
        $dest = imagecreatefromjpeg("./QRcode/qrbg.jpg");    //底图
        $src = imagecreatefromjpeg($filename);      //透明图
        $extension = pathinfo($tfilename)['extension'] ;
        $face;
        if ($extension=='jpg'||$extension=='jpeg') {
            $face = imagecreatefromjpeg($tfilename);  
        }else if ($extension=='png') {
            $face = imagecreatefrompng($tfilename);  
        }else if ($extension=='gif') {
            $face = imagecreatefromgif($tfilename);  
        }
            
        //
        //缩小二维码
        $qrimg = imagecreate(183, 183); 
        imagecopyresampled($qrimg, $src, 0, 0, 0, 0, 183,183, imagesx($src), imagesy($src)); //关键函数，后面解释 
        //
        $fimg = imagecreate(50, 50); 
        imagecopyresampled($fimg, $face, 0, 0, 0, 0, 50,50, imagesx($face), imagesy($face));
        
        imagecopy($qrimg, $fimg, 183/2-25, 183/2-25, 0, 0, 50, 50);    //合并，注意大小和座标
        //
        imagecopy($dest, $qrimg, 55, 39, 0, 0, 183, 183);    //合并，注意大小和座标
//        header('Content-Type:image/jpeg');    //声明格式
        imagejpeg($dest,"./QRcode/".session('openid')."_p.jpg",40);    //输出图片，如果需要保存的话，imagepng($dest, $file); 
        @imagedestroy($dest);    //释放内存
        @imagedestroy($src);    //释放内存
        //
        return TRUE;
//        echo '<img src="'.$get['body'].'"/>';
    }
        
    //S('token')
    var $tryTime = 0;
    public function saveUserInfoWhenSubscribe(){
        $info = $this->getUserInfoFromWC(I('post.openid'));
        $userdata = M('users','center_')->where('`openid` = \''.I('post.openid').'\'')->find();
        $infoArr = json_decode($info);
        
        if (array_key_exists('errcode',$infoArr) && $tryTime < 3) {
            S('token',NULL);
            saveUserInfoWhenSubscribe();
            
            \Think\Log::write('出错了,重试'.$info);
            $tryTime++;
            exit();
        }else if($tryTime >=3){
            \Think\Log::write('出错了,重试了3次'.$info);
            $tryTime = 0;
            exit();
        }else{
            \Think\Log::write('关注成功');
            $tryTime = 0;
        }
        
        if (!$userdata) {
            $arr['openid'] = I('post.openid');
            $arr['userinfo'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',addslashes($info));
            $arr['subscribeData'] = time();
            $arr['platform'] = 'wechat';
            if (isset($_POST['agent'])) {
                $arr['agent'] = explode('_', I('post.agent'))[1];
            }
            M('users','center_')->add($arr);
        }else{
            M('users','center_')->where('`openid` = \''.I('post.openid').'\'')->save(array('userinfo'=>  preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($info)),'subscribeData'=>  time()));
        }
    }
    //
    public function saveUserInfoFromWeibo(){
//        $info = $this->getUserInfoFromWC(I('post.openid'));
//        $info = json_decode(I('post.data'));
//        var_dump($_POST);
        $userdata = M('users','center_')->where('`openid` = \''.$_POST['access_token'].'\'')->find();
        if (!$userdata) {
            $arr['openid'] = $_POST['access_token'];
            $arr['userinfo'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',addslashes(json_encode($_POST)));
            $arr['subscribeData'] = time();
            $arr['platform'] = 'weibo';
            if (isset($_POST['agent'])) {
                $arr['agent'] = explode('_', I('post.agent'))[1];
            }
            M('users','center_')->add($arr);
        }else{
            M('users','center_')->where('`openid` = \''.$info['access_token'].'\'')->save(array('updataTime'=>  time()));
        }
    }
    //
    function getUserInfoFromWC($openid){
        $token = $this->getToken();
        $http = new \Think\Http;
        $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$openid.'&lang=zh_CN');
        return $json;
    }
    
    function levelTitle() {
        return array(0=>'普通会员',1=>'高级会员');
    }


    public function getUserInfo(){
        $levelTitle = $this->levelTitle();
        
        $AES = new \Think\AES();
        $arr = $AES->decode($_REQUEST['data']);
        $data = decryptData($arr);
        //
        $userdata = M('users','center_')->where('`openid` = \''.$data->openid.'\'')->find();
        $userInfo = '';
        $arrJson = array();
        if ($userdata) {
            $userInfo = $userdata['userinfo'];
            $arrJson['code'] = 0;
            $arrJson['data'] = array('wx_info'=>$userInfo,'level'=>$userdata['level'],'isban'=>$userdata['isban'],'levelTitle'=>$levelTitle[$userdata['level']],'time'=>  time(),'ismob'=>TRUE);
        }else{
//            print_r('sbv');
            $userInfo = $this->getUserInfoFromWC($data->openid);
            $json = json_decode($userInfo);
            
        
            if (array_key_exists('errcode',$json) && $tryTime < 3) {
                S('token',NULL);
                getUserInfo();

                \Think\Log::write('已关注 出错了,重试'.$info);
                $tryTime++;
                exit();
            }else if($tryTime >=3){
                \Think\Log::write('已关注 出错了,重试了3次'.$info);
                $tryTime = 0;
                exit();
            }else{
                \Think\Log::write('关注成功');
                $tryTime = 0;
            }
        
        
            if (array_key_exists("errcode",$json)) {
                \Think\Log::write('这里走了就出错了');
                $arrJson['code'] = $userInfo;
            }else{
                \Think\Log::write('这里走了吗');
                M('users','center_')->add(array('openid'=>$data->openid,'userinfo'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($userInfo)),'updataTime'=>time(),'platform'=>'wechat','subscribeData'=>  time()));
                $arrJson['code'] = 0;
                $arrJson['data'] = array('wx_info'=>addslashes($userInfo),'level'=>0,'isban'=>0,'levelTitle'=>$levelTitle[0],'time'=>  time(),'ismob'=>TRUE);
            }
            
        }
        //
        
//        $arrJson = array('code'=>0,'data'=>array('wx_info'=>$userInfo,'time'=>  time(),'ismob'=>TRUE));
        $arrJson = json_encode($arrJson);
        echo $AES->encode($arrJson) ;
        
    }
    
    public function getUserInfoInSite(){
//        session('openid',NULL);
        if (session('openid')==null) {
            session('openid',I('get.openid'));
        }
        $openid = session('openid')==null?I('get.openid'):session('openid');
        //已经有
        $levelTitle = $this->levelTitle();
//        S('wx_info'.I('get.openid'),null);
//        var_dump(S('wx_info'.$openid));
        if (S('wx_info'.$openid)!=NULL) {
//            return object_array(json_decode(S('wx_info')));
        }
        $userdata = M('users','center_')->where('`openid` = \''.$openid.'\'')->find();
        if ($userdata) {
            //已经有
            $arr = object_array(json_decode(stripslashes($userdata['userinfo'])));
            $arr['level']=$userdata['level'];
            $arr['id']=$userdata['id'];
            $arr['levelTitle']=  $levelTitle[$userdata['level']];
            S('wx_info'.$openid,$arr,3600);
        }else{
            //没有
            $userInfo = $this->getUserInfoFromWC($openid);
            $json = json_decode($userInfo);
            if (array_key_exists("errcode",$json)) {
                S('wx_info'.$openid,null);
                return FALSE;
            }else{
                $add = M('users','center_')->add(array('openid'=>$openid,'userinfo'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($userInfo)),'updataTime'=>time(),'platform'=>'wechat'));
                $arr = object_array(json_decode($userInfo));
                $arr['level']=0;
                $arr['id']=$add;
                $arr['levelTitle']=  $levelTitle[0];
                S('wx_info'.$openid,$arr,3600);
            }
        }
    }
    
    public function checkOrder() {
        
        $AES = new \Think\AES();
        $arr = $AES->decode($_REQUEST['data']);
        
        $data = decryptData($arr);
//        print_r($data);
//        exit();
        if ($data == FALSE) {
            $this->error('超时','/pay',5);
            exit();
        }
        $sql = M('orders','center_')->where(array('orderid'=>$data->paid))->find();
//        print_r($sql);
        if ($sql['paid']==1) {
            $array = array('code'=>0,'data'=>array('paid'=>TRUE,'paidtime'=>$sql['paidtime'],'time'=>  time()));
        }else{
            $array = array('code'=>0,'data'=>array('paid'=>FALSE,'paidtime'=>$sql['paidtime'],'time'=>  time()));
        }
        
        $arrJson = json_encode($array);
        echo $AES->encode($arrJson) ;
    }
    
    public function pay(){
        $AES = new \Think\AES();
        
        $arr = $AES->decode(I('get.data'));
        $data = decryptData($arr);
//        var_dump($arr);
//        exit();
        if ($data == FALSE) {
            $this->error('超时','/pay',5);
            exit();
        }
        
        $sqlData = M('orders','center_')->where(array('orderid'=>$data->orderid,'paid'=>0))->find();
        if ($sqlData) {
            $orderid = $sqlData['id'];
            $arr = array();
            $arr['addtime']=time();
            $arr['price']=$data->price;
            if (array_key_exists('level', $data)) {
                $arr['level']=$data->level;
            }
            M('orders','center_')->where(array('orderid'=>$data->orderid))->save($arr);
        }else{
            $arr = array();
            $arr['iid']=$data->iid;
            $arr['openid']=$data->openid;
            $arr['price']=$data->price;
            $arr['title']=$data->title;
            $arr['type']=$data->type;
            $arr['addtime']=time();
            $arr['returnUrl']=$data->returnUrl;
            if (array_key_exists('level', $data)) {
                $arr['level']=$data->level;
            }
            $saveData = M('orders','center_')->add($arr);
            $orderid = $saveData;
        }
        $this->redirect('pay_wx',array('orderid'=>$orderid));
        //支付
        
//        require_once "WxPay.JsApiPay.php";
//        require_once 'log.php';
    }
    
    public function pay_wx(){
        ini_set('date.timezone','Asia/Shanghai');
        
        $tools= new \Think\Wxpay\lib\JsApiPay();
        $openId = $tools->GetOpenid(I('get.orderid'));
        //
        $sqlData = M('orders','center_')->where(array('id'=>I('get.orderid')))->find();
//        $this->display();
//        print_r($sqlData);
//        exit();
        //下单
        $input = new \Think\Wxpay\lib\WxPayUnifiedOrder();
        $input->SetBody($sqlData['title']);
        $input->SetAttach($sqlData['openid']);
//        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetOut_trade_no(date("YmdHi").$sqlData['id']);
        $input->SetTotal_fee($sqlData['price']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 3600));
        $input->SetGoods_tag(I('get.orderid'));
        $input->SetNotify_url("http://wechatcenter.witheasy.com/index.php/notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
//        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//        print_r($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        
//        print_r($sqlData);
        $this->assign('sqlData', $sqlData);
        $this->assign('jsApiParameters', $jsApiParameters);
        $this->display();
    }
          
    function notify(){
        //初始化日志
        
        $notify = new \Think\Wxpay\lib\Notify();
        $notify->Handle(false);
    }
            
    function getToken($getJsapi_ticket=FALSE){
//        S('token',null);
        $value = S('token');
        if ($value == NULL || $value->access_token == NULL) {
            $http = new \Think\Http;
            $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->findConfWithKey('AppID').'&secret='.$this->findConfWithKey('AppSecret'));
            $json = json_decode($json);
            S('token',$json,7200);
            $value = S('token');
            if ($getJsapi_ticket==TRUE) {
                $this->getJsapi_ticket();
            }
            return $value->access_token;
        }  else {
            $value = S('token');
            if ($getJsapi_ticket==TRUE) {
                $this->getJsapi_ticket();
            }
            return $value->access_token;
        }
    }
    
    
    function getJsapi_ticket(){
//        S('jsapi_ticket',null);
//        echo 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.S('token')->access_token.'&type=jsapi';
        $value = S('jsapi_ticket');
//        var_dump($value);
        if ($value == NULL || $value->ticket == NULL || $value->errcode != 0) {
            $http = new \Think\Http;
            $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.S('token')->access_token.'&type=jsapi');
//            var_dump($json);
            $json = json_decode($json);
            S('jsapi_ticket',$json,7200);
            if ($value->errcode != 0) {
                $this->getToken(TRUE);
                exit();
            }
            $value = S('jsapi_ticket');
            return $value->ticket;
        }  else {
            $value = S('jsapi_ticket');
            return $value->ticket;
        }
    }
    
    function findConfWithKey($key){
        $conf = M('config','center_')->where('`key` = \''.$key.'\'')->find();
        return $conf['value'];
    }
    
    
    public function udid($openid){
        $user = M('users','center_')->where(array('openid'=>$openid))->find();
        $udid = substr(hexdec($openid), 0,4).$user['id'];
        S('udid'.$openid,$udid,21600);
    }
}