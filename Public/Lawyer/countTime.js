var s = 0;
function show_time(sessionTime,nowtime){ 
    
	var time_start = sessionTime; //设定当前时间
	var time_end =  nowtime; //设定目标时间
        //
	// 计算时间差 
	var time_distance = time_end - time_start; 
        if(time_distance<0){
            loop(0,0,0,0);
        }else{
            // 天
            var int_day = Math.floor(time_distance/86400000) 
            time_distance -= int_day * 86400000; 
            // 时
            var int_hour = Math.floor(time_distance/3600000) 
            time_distance -= int_hour * 3600000; 
            // 分
            var int_minute = Math.floor(time_distance/60000) 
            time_distance -= int_minute * 60000; 
            // 秒 
            var int_second = Math.floor(time_distance/1000) 
            // 时分秒为单数时、前面加零 
            if(int_day < 10){ 
                    int_day = "0" + int_day; 
            } 
            if(int_hour < 10){ 
                    int_hour = "0" + int_hour; 
            } 
            if(int_minute < 10){ 
                    int_minute = "0" + int_minute; 
            } 
            if(int_second < 10){
                    int_second = "0" + int_second; 
            } 
        }
	
	// 显示时间 
//	$("#time_d").val(int_day); 
//	$("#time_h").val(int_hour); 
//	$("#time_m").val(int_minute); 
//	$("#time_s").val(int_second); 
//console.log(int_day+'-'+int_hour+'--'+int_minute);
        loop(int_day,int_hour,int_minute,int_second);
        
	// 设置定时器
        s = time_end+1000;
	setTimeout("show_time("+time_start+","+s+")",1000); 
}