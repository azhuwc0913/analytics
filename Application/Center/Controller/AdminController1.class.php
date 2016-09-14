<?php
namespace Center\Controller;
use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;
 header("Content-Type: text/html; charset=UTF-8");
class AdminController extends Controller {
//    const USER_AUTH_URL          =   'http://shop.pingmin8.com/index.php?g=Api&m=PMApi&a=MemberConfig';
    const SITE_URL = 'http://payment.kaka-games.com/';
    Public function _initialize()
    {
        

    }

    public function index()
    {

        $AES = new \Think\AES();
        if (session('login') == TRUE) {
            $this->redirect('main');
        }        
        if (isset($_POST['action']) && I('post.action') == 'login') {
            $data = M('users')->where(array('username' => I('post.mob'), 'password' => sha1(md5(I('post.password'))), 'level' => 10))->find();
            if ($data) {
                session('login', TRUE);
                session('id', $data['id']);
                exit(json_encode(array('error' => 0)));
            } else {
                exit(json_encode(array('error' => 1, 'msg' => '登录失败')));
            }
        }
        $this->display();
    }
    
    public function getReportData($url){ //获取游戏平台数据
      
       $MemberLogTime = M("member")->order('created_time desc')->find();
       $RenewLogTime = M("renew_log")->order('created_time desc')->find();
       $DueLogTime = M("due_log")->order('created_time desc')->find();       
       
       $StarttimeArray =array($MemberLogTime["created_time"],$RenewLogTime["created_time"],$DueLogTime["created_time"]);
      // print_r($StarttimeArray);
       $start_time = max($StarttimeArray);
       
       if($start_time == ""){
           $start_time = "2016-1-1";
       }

       $curl = new \Think\Http;
       $start_time = strtotime($start_time);
       $end_time = time();
       //admin.kaka-games.com/site/return-msg
       $url = $url.'?start_time='.$start_time.'&end_time='.$end_time;
       $data = $curl->http($url, $params,"GET");
       $aes = new \Think\AES;
       $data =$aes->decode($data);
       
            if($data != ""){ //如果返回的数据不为空，就CURL取数据

                 $data = json_decode($data);
                 $i=0;    
                 foreach ($data->user as $value) {      //把 user 传过来的数据，改时间戳，及改名对应字段  
                      $userArray[$i]["created_time"] = date("Y-m-d H:i:s",$value->created_at);
                      $userArray[$i]["user_id"] = $value->id;
                      $userArray[$i]["email"] = $value->email;
                      $userArray[$i]["tracking_code"] = $value->tracking_code;
                      $userArray[$i]["phone"] = $value->phone;
                      $userArray[$i]["extension_id"] = $value->extension_id;
                      $userArray[$i]["country"] = $value->country;
                      $i =$i + 1;
                 }

                 $i=0;
                 foreach ($data->renew as $value) {      //把 renew 传过来的数据，改时间戳，及改名对应字段  
                      $renewArray[$i]["user_id"] = $value->user_id;
                      $renewArray[$i]["created_time"] = date("Y-m-d H:i:s",$value->renew_time);
                      $renewArray[$i]["due_time"] = date("Y-m-d H:i:s",$value->out_time);
                      $i =$i + 1;
                 }

                 $i=0;
                 foreach ($data->close as $value) {      //把 due 传过来的数据，改时间戳，及改名对应字段  
                      $dueArray[$i]["user_id"] = $value->user_id;
                      $dueArray[$i]["created_time"] = date("Y-m-d H:i:s",$value->close_time);
                      $i =$i + 1;
                 }       

                 if(count($userArray)>0){
                    M('member')->addAll($userArray);
                 }

                 if(count($renewArray)>0){
                    M('renew_log')->addAll($renewArray);
                 }

                 if(count($dueArray)>0){
                    M('due_log')->addAll($dueArray);  
                 }

            }
                      
    }
    
    public function report()
    {    
        $time["start_time"] = date("Y-m-d 00:00:00");
        $time["end_time"] = date("Y-m-d 23:59:59");
        $this->assign('time',$time);
        $this->display();
    }
    
    public function reportData(){
        $url = "admin.kaka-games.com/site/return-msg";
        $this->getReportData($url); //取最新的数据

        if(I('get.start')&&I('get.end')){ //如果有时间范围
            $start_time = I('get.start');
            $end_time = I('get.end');   
            $where = "where created_time BETWEEN '$start_time' AND  '$end_time'";
        }else{  //无时间范围就拿一个月的数据
            $start_time = date("Y-m-01");
            $end_time = date("Y-m-d H:i:s");
            $where = "where created_time BETWEEN '$start_time' AND  '$end_time'";
        }

  
            $getType = I('get.date');
            if($getType=="renew_time"){
                $table = 'an_renew_log';
            }else if($getType=="close_time"){
                $table = 'an_due_log';
            }else if($getType=="create_time"){
                $table = 'an_member';
            }

            if($getType == "create_time"){
                $m=M();
                $resultDate = 
                         $m->query("select * from an_member $where");
            }else{
                $resultDate = M('member m')
                            ->join("Right join (select * from $table r $where )r  ON r.user_id = m.user_id")
                            ->field('m.*,r.created_time r_time')
                            ->select();
            }

        
        //   print_r($resultDate);
        if(count($resultDate)>0){
            foreach ($resultDate as $value) {
                 $messages[] = [
                     'user_id'=>$value["user_id"],
                     'email'=>$value["email"],
                     'tracking_code'=>$value["tracking_code"],
                     'phone'=>$value["phone"],
                     'extension_id'=>$value["extension_id"],
                     'country'=>$value["country"],
                     'created_time'=>$value["created_time"],
                     'r_time'=>$value["r_time"]
                     ];
            }
        }else{
            $messages[] ='NULL';
        }
        $data['messages'] = $messages;
        echo json_encode($data);   
        exit();
    }
    
    public function monthReport()
    {    
        $url = "admin.kaka-games.com/site/return-msg";
        $this->getReportData($url); //取最新的数据
        $extension_id = isset($_POST['extension_id'])?$_POST['extension_id']:'';

        if($extension_id){
            $month_where = " and extension_id='$extension_id'";
           $where = " where extension_id='$extension_id'";
        }else{
            $month_where = '';
            $where = '';
        }
        //取出用户表中的所有extension_id
        $ext_ids_sql = "SELECT distinct extension_id FROM `an_member`";

        $ext_ids = M()->query($ext_ids_sql);

        $renewMothSql = "SELECT DATE_FORMAT(created_time,'%Y-%m') month,count(user_id) count FROM an_member Where created_time > DATE_FORMAT(Now(),'%Y') $month_where GROUP BY DATE_FORMAT(created_time,'%Y-%m')";
        $m = M();
        $renewMonth = $m->query($renewMothSql);
       
        foreach ($renewMonth as $value) {
          //  $renewMonth[] = $value;
            $jsmonth[strtotime($value["month"])] =$value;
            $newRenewMonth[] = strtotime($value["month"]);
        }

        
        $renewDateSql = "SELECT m.user_id,
                       m.email,
                       DATE_FORMAT(m.created_time,'%Y-%m') carete_month,
                       DATE_FORMAT(r.created_time,'%Y-%m') renew_time,
                       count(2) count
                FROM an_member m 
                    RIGHT JOIN(
                            SELECT * FROM an_renew_log 
                            where created_time > DATE_FORMAT(NOW(),'%Y') group by user_id,DATE_FORMAT(created_time,'%Y-%m')
                       )r 
                ON m.user_id = r.user_id $where
               
                GROUP BY 
                carete_month,renew_time";
        $renewData = $m->query($renewDateSql);   //统计 user_id 续费月份
       // print_r($renewData);
        foreach ($renewData as $value) {      //得出，创建用户的月份，该月份下的用户后期的续费情况。
            for($i=1;$i<13;$i++){
                $month = date("Y")."-".$i;
                if(strtotime($month) == strtotime($value["carete_month"])){    
                 //   $data[strtotime($month)] =                   
                    $data[strtotime($month)][strtotime($value["renew_time"])] = round($value["count"] / $jsmonth[strtotime($month)]["count"],2)*100 ;  
                }
            }
        }



        foreach ($data as $key => $value) { //添加没数据的月份
          
        // echo date('Y-m',1469980800)."<br>";
             for($a=1;$a<13;$a++){
                 $month_a = strtotime(date("Y")."-".$a);            
                // $value[strtotime($month_a)] = "0";
                 if($key <= $month_a){
                     $data[$key][$month_a] = $value[$month_a];
                     
                 }

             }
             ksort($data[$key]);
             
        }
       // print_r($data);
        foreach ($data as $key => $value) { //去除大于当月的月份 

            $month = array_keys($value);
            for($i=0;$i<count(array_keys($value));$i++){

             //  echo date("Y-m",$bbx[$i]) ."----" . date("Y-m")."<br>";
               if($month[$i] > strtotime(date("Y-m"))){
                  $month_key = $month[$i];
                  unset($data[$key][$month_key]);
               }
            }

        }
     
         
        $this->assign('renewMonth',$renewMonth);
        $this->assign('renewMonthKey',$newRenewMonth);
        $this->assign('report_data',$data);
        $this->assign('ext_ids', $ext_ids);
        $this->assign('extension_id', $extension_id);
       // print_r($renewMonth);
        $this->display();
    }
    
    public function GetMonthReport(){
         $m = M();
         $renewDateSql = "SELECT m.user_id,
                       m.email,
                       DATE_FORMAT(m.created_time,'%Y-%m') carete_month,
                       DATE_FORMAT(r.created_time,'%Y-%m') renew_time,
                       count(2) count
                FROM an_member m 
                    RIGHT JOIN(
                            SELECT * FROM an_renew_log 
                            where created_time > DATE_FORMAT(NOW(),'%Y')
                       )r 
                ON m.user_id = r.user_id 
                GROUP BY 
                carete_month,renew_time";
        $renewData = $m->query($renewDateSql);
      //  print_r($renewData);
        
        
        foreach ($renewData as $value) {     
            for($i=1;$i<13;$i++){
                $month = date("Y")."-".$i;
               //$data[$month][]="";
                if(strtotime($month) == strtotime($value["carete_month"])){                  
                    $data[$month][$value["renew_time"]] = $value["count"] ;  
                }
            }
        }
        
        return $data;
    }    
  
}