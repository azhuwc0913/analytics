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
    
    public function getReportData($url, $web, $start){ //获取游戏平台数据
       $web_id = C($web.'-id');
        $where['web_id'] = $web_id;
       $MemberLogTime = M("member")->order('created_time desc')->where($where)->find();
       $RenewLogTime = M("renew_log")->order('created_time desc')->where($where)->find();
       $DueLogTime = M("due_log")->order('created_time desc')->where($where)->find();
       

     //  $StarttimeArray =array($MemberLogTime["created_time"],$RenewLogTime["created_time"],$DueLogTime["created_time"]);
        if($MemberLogTime["created_time"]==""){
            $MemberLogTime = strtotime(date('Y-m'));
        }else{
            if($start<strtotime($MemberLogTime["created_time"])){
                $MemberLogTime = $start;
            }
            $MemberLogTime = strtotime($MemberLogTime["created_time"]);
        }
        
         if($RenewLogTime["created_time"]==""){
            $RenewLogTime = strtotime(date('Y-m'));
        }else{
             if($start<strtotime($RenewLogTime["created_time"])){
                 $RenewLogTime = $start;
             }
            $RenewLogTime = strtotime($RenewLogTime["created_time"]);
        } 
        
        if($DueLogTime["created_time"]==""){
            $DueLogTime = strtotime(date('Y-m'));
        }else{
            if($start<strtotime($DueLogTime["created_time"])){
                $DueLogTime = $start;
            }
            $DueLogTime = strtotime($DueLogTime["created_time"]);
        }
              



       $start_time = $MemberLogTime."_".$RenewLogTime."_".$DueLogTime;
//        $start_time = '1458403200_1458403200_1458403200';
       $curl = new \Think\Http; 
       $end_time = time();
       //admin.kaka-games.com/site/return-msg
       $url = $url.'?start_time='.$start_time.'&end_time='.$end_time;

       $data = $curl->http($url, $params,"GET");

//       $aes = new \Think\AES;
//       $data =$aes->decode($data);


            if($data != ""){ //如果返回的数据不为空，就CURL取数据

                 $data = json_decode($data);
//                dd($data->user);

                if($web_id==2) {
                    $i = 0;
                    foreach ($data->user as $value) {      //把 user 传过来的数据，改时间戳，及改名对应字段
                        $userArray[$i]["created_time"] = date("Y-m-d H:i:s", $value->created_at);
                        $userArray[$i]["user_id"] = $value->user_id;
                        $userArray[$i]["email"] = $value->email;
                        $userArray[$i]["web_key_id"] = $value->id;
//                        $userArray[$i]["tracking_code"] = $value->tracking_code;
//                        $userArray[$i]["phone"] = $value->phone;
//                        $userArray[$i]["extension_id"] = $value->extension_id;
//                        $userArray[$i]["country"] = $value->country;
                        $userArray[$i]["web_id"] = C($web . '-id');
                        $i = $i + 1;

                    }
                }else{
                    $i=0;
                    foreach ($data->user as $value) {      //把 user 传过来的数据，改时间戳，及改名对应字段
                        $userArray[$i]["created_time"] = date("Y-m-d H:i:s",$value->created_at);
                        $userArray[$i]["user_id"] = $value->id;
                        $userArray[$i]["email"] = $value->email;
                        $userArray[$i]["tracking_code"] = $value->tracking_code;
                        $userArray[$i]["phone"] = $value->phone;
                        $userArray[$i]["extension_id"] = $value->extension_id;
                        $userArray[$i]["country"] = $value->country;
                        $userArray[$i]["web_id"] = C($web.'-id');
                        $i =$i + 1;
                    }
                }

                    if(!is_null($data->renew)) {
                        $i = 0;
                        foreach ($data->renew as $value) {      //把 renew 传过来的数据，改时间戳，及改名对应字段
                            $renewArray[$i]["user_id"] = $value->user_id;
                            $renewArray[$i]["created_time"] = date("Y-m-d H:i:s", $value->renew_time);
                            $renewArray[$i]["due_time"] = date("Y-m-d H:i:s", $value->out_time);
                            $renewArray[$i]["web_id"] = C($web . '-id');
                            $i = $i + 1;
                        }
                    }

                if($web_id==2) {
                    $i = 0;
                    foreach ($data->close as $value) {      //把 due 传过来的数据，改时间戳，及改名对应字段
                        $dueArray[$i]["user_id"] = $value->user_id;
                        $dueArray[$i]["created_time"] = date("Y-m-d H:i:s", $value->close_time);
                        $dueArray[$i]["web_id"] = C($web . '-id');
                        $dueArray[$i]["web_key_id"] = $value->id;
                        $i = $i + 1;
                    }
                }else{
                    $i = 0;
                    foreach ($data->close as $value) {      //把 due 传过来的数据，改时间戳，及改名对应字段
                        $dueArray[$i]["user_id"] = $value->user_id;
                        $dueArray[$i]["created_time"] = date("Y-m-d H:i:s", $value->close_time);
                        $dueArray[$i]["web_id"] = C($web . '-id');
                        $i = $i + 1;
                    }
                }

                 if(count($userArray)>0){
                    M('member')->addAll($userArray, array(), true);
                 }

                 if(count($renewArray)>0){
                    M('renew_log')->addAll($renewArray, array(), true);
                 }

                 if(count($dueArray)>0){
                    M('due_log')->addAll($dueArray, array(), true);
                 }

            }
                      
    }
    
    public function report()
    {
        $web = I('get.web');
        $time["start_time"] = date("Y-m-d 00:00:00");
        $time["end_time"] = date("Y-m-d 23:59:59");
        $this->assign('time',$time);
        $this->display($web.'_report');
    }
    
    public function reportData(){
        $web = I('get.web');

        //这里到时候会传一个网站名称过来，然后进行相应的判断
        $url = C($web.'-sub');

        $web_id = C($web.'-id');

        $start = I('get.start');

        $this->getReportData($url, $web, $start); //取最新的数据

        if(I('get.start')&&I('get.end')){ //如果有时间范围
            $start_time = I('get.start');
            $end_time = I('get.end');   
            $where = "where created_time BETWEEN '$start_time' AND  '$end_time' AND web_id='$web_id'";
        }else{  //无时间范围就拿一个月的数据
            $start_time = date("Y-m-01");
            $end_time = date("Y-m-d H:i:s");
            $where = "where created_time BETWEEN '$start_time' AND  '$end_time' AND web_id='$web_id'";
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
//                dd($resultDate);
            }else{
                if($web_id==2){
                    $where = "$table.created_time BETWEEN '$start_time' AND  '$end_time' AND $table.web_id='$web_id'";

                    $resultDate = M()
                        ->table($table)
                        ->where($where)
                        ->join("left join `an_member` r  ON r.web_key_id = $table.web_key_id")
                        ->field("r.*,$table.created_time r_time")
                        ->select();
                }else{
                    $resultDate = M('member m')
                        ->join("Right join (select * from $table r $where )r  ON r.user_id = m.user_id")
                        ->field('m.*,r.created_time r_time')
                        ->select();
                }
            }

//        dd($resultDate);
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
        $web = I('get.web');
        $url = C($web.'-sub');
        $web_id = C($web.'-id');
        $this->getReportData($url, $web); //取最新的数据
        $extension_id = isset($_POST['extension_id'])?$_POST['extension_id']:'';

        if($extension_id){
            $month_where = " and extension_id='$extension_id' and web_id='$web_id'";
            $where = " where extension_id='$extension_id' and m.web_id='$web_id'";
        }else{
            $month_where = " and web_id='$web_id'";
            $where = " where m.web_id='$web_id'";
        }
        $renewMothSql = "SELECT DATE_FORMAT(created_time,'%Y-%m') month,count(user_id) count FROM an_member Where created_time > DATE_FORMAT(Now(),'%Y') $month_where GROUP BY DATE_FORMAT(created_time,'%Y-%m')";


        $m = M();
        $renewMonth = $m->query($renewMothSql);

        //echo '<pre>';print_r($renewMonth);
        //取出用户表中的所有extension_id
        $ext_ids_sql = "SELECT distinct extension_id FROM `an_member`";

        $ext_ids = M()->query($ext_ids_sql);
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


//        echo '<pre>';print_r($data);

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
//        print_r($renewData);
        
        
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

    public function statics(){

        $web = I('get.web');

        $web_id = C($web.'-id');
        //在表中找出所有的extension_id和landing_id和支付状态
        $landing_data = M($web.'-static')->distinct(true)->field('landing_id')->where("web_id='%s'", $web_id)->select();

        $extension_id = M($web.'-static')->distinct(true)->field('extension_id')->where("web_id='%s'", $web_id)->select();

        $pay_status = M($web.'-static')->distinct(true)->field('payment_status')->where("web_id='%s'", $web_id)->select();

        $this->assign('landing_data', $landing_data);
        $this->assign('pay_status', $pay_status);
        $this->assign('extension_id', $extension_id);
        $this->assign('start_time', time()-(7 * 24 * 60 * 60));
        $this->assign('end_time', time());
        $this->display($web);

//




        //取出近一个星期的数据，然后展现在页面中
    }


    public function dataList(){
        ini_set('memory_limit', '512M');

        set_time_limit (300);

        $web = I('get.web');

        $web_id = C($web.'-id');

        $where['web_id'] = $web_id;

        $extension_id = I('get.extension_id');

        $landing_id = I('get.landing_id');

        $pay_status = I('get.pay_status');
        if($extension_id){
           $where['extension_id'] = $extension_id;
        }

        if($landing_id){
            $where['landing_id'] = $landing_id;
        }

        if($pay_status==-1){
            $where['payment_status'] = array('exp','is NULL');
        }else{
            if($pay_status) {
                $where['payment_status'] = $pay_status;
            }
        }

        $url = "admin.kaka-games.com/site/return-static";

        $start_time = strtotime(I('get.start_time'));//开始查找的时间

        $end_time = empty(strtotime(I('get.end'))) ? time():strtotime(I('get.end'));//如果为空就获取当前时间戳

        //数据库中存在的最早记录
        $min_create_time = M($web.'-static')->min('create_time');

        //数据库中存在的最晚记录
        $max_create_time = M($web.'-static')->max('create_time');

        if(!$min_create_time){
            //表明数据库为空，去平台取数据，然后取出最近一个星期的数据
            $start_time = time()-7*24*60*60;
            $end_time = time();

            $res = $this->getStaticData($url, $web, $start_time, $end_time);

            $static_data = M($web.'-static')
                ->where("create_time>='%s' and create_time<='%f'",$start_time,$end_time)
                ->where($where)
                ->select();
        }else{

            //这里要判断一下，end_time和数据库中最大的create_time的关系
            //如果end_time比最大的create_time大的话要取出这一段的数据
            if((int)$end_time>(int)$max_create_time){
                $_start_time = $max_create_time;
                $this->getStaticData($url, $web, $_start_time, $end_time);
            }
            //如果没有设置开始时间，就取数据库中最小创建时间到end_time之间的数据
            if(empty($start_time)){

                $static_data = M($web.'-static')
                    ->where("create_time>='%s' and create_time<='%f'",$min_create_time,$end_time)
                    ->where($where)
                    ->select();

            }else{
                //设置了开始时间，要和数据库中的数据进行比较
                //将查询的start_time和数据库中的最小的创建时间进行比较，如果数据库中的最小创建时间比查询时间大
                //就查询这一段的时间的数据
                if($min_create_time > $start_time){

                 $_end_time = $min_create_time;

                 $this->getStaticData($url, $web, $start_time, $_end_time);
                }


                //数据已经补齐，按正常程序进行数据的选取
                $static_data = M($web.'-static')
                    ->where("create_time>='%s' and create_time<='%f'",$start_time,$end_time)
                    ->where($where)
                    ->select();
            }
        }



        if(empty($static_data)){
            $messages[] = [''];
        }else{
            foreach($static_data as $k=>$v){
                $messages[] = [
                    'id' => $v['id'],
                    'extension_id' => $v['extension_id'],
                    'subext_id'  => $v['subext_id'],
                    'landing_id' => $v['landing_id'],
                    'payment_status' => $v['payment_status'],
                    'create_time'    => date('Y-m-d H:i:s', $v['create_time']),
                    'user_real_click' => $v['user_real_click'],
                    'gad_id' => $v['gad_id']
                ];
            }
        }

//        $messages['count'] = count($messages);
        if($extension_id){
            $data['condition']['extension_id'] = $extension_id;
        }

        if($landing_id){
            $data['condition']['landing_id'] = $landing_id;
        }

        if($pay_status!=0){
            $data['condition']['pay_status'] = $pay_status;
        }

//        dd($data);
        $data['messages'] = $messages;
        $data['count'] = count($messages);


        echo json_encode($data);
    }


    /*
     * 该函数的目的是从平台取数据，然后存入数据库中便于本地查询
     * $params $url 取数据的地址
     * $params $web web名称
     * $params $start_time 取数据的起始时间
     * $params $end_time 取数据的结束时间
     */
    public function getStaticData($url, $web, $start_time, $end_time){
        $tableName = $web.'-static';

        $model = M($tableName);

        $curl = new \Think\Http;

        $url = $url.'?start_time='.$start_time.'&end_time='.$end_time;

        $data = $curl->http($url, $params, "GET");

        $aes = new \Think\AES;

        $data =$aes->decode($data);

        $data = json_decode($data);

//            var_dump($data);
            $dataList = [];
            //将数据存入本地数据库
            if($data){
                foreach($data as $k=>$v){
                    $dataList[] = array(
                        'extension_id' => $v->extension_id,
                        'subext_id' => $v->subext_id,
                        'landing_id' => $v->landing_id,
                        'payment_status' => $v->payment_status,
                        'create_time' => $v->create_time,
                        'user_real_click' => $v->user_real_click,
                        'gad_id' => $v->id,
                        'web_id' => C($web.'-id'),
                    );
                }
            }

            $count = count($dataList);

            $size = 800;

            $length = ceil($count/$size);

            static $k = 0;

            for($i=0; $i<$length; $i++){
                $tem_data = $dataList;
                $data = array_splice($tem_data, $k, $size);
                $model->addAll($data, array(), true);
                $k+=$size;
                sleep(1);
            }

        }

}