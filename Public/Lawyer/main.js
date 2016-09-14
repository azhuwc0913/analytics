            var url = 'https://a1.easemob.com/jeiry/witheasy/';
            var groupid = '166784306155356636';
            
        $.ajax({
            type: "POST",
            url: url+"token",
            data:'{"grant_type":"client_credentials","client_id":"YXA6660D0JZREeW-seG1ZqObeQ","client_secret":"YXA6Lnf5_hbm6t-SyniX1nUkr0BNnZo"}',
            dataType:"json",
            beforeSend: function(request) {
                
            },
            headers: {
                "Content-Type":"application/json"
            },
            success: function(result) {
                token = result.access_token;
                addtochatgroup();
                
            }
        });
        
        
        function reg(openid,nickname){
            $.ajax({
                type: "POST",
                url: url+"users",
                data:'{"username":"'+openid+'","password":"'+hex_md5(openid)+'","nickname":"'+nickname+'"}',
                dataType:"json",
                beforeSend: function(request) {

                },
                headers: {
                    "Content-Type":"application/json"
                },
                success: function(result) {
                    console.log('注册成功');
                    login(myid);
                    
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    if (XMLHttpRequest.status == 400) {
                        console.log('已经注册了');
                        login(myid);
                    }
                },
            });
        }
        
        function login(openid){
            conn.open({
                user : openid,
                pwd : hex_md5(openid),
                appKey : 'jeiry#witheasy'
            });
    
        } 
        
        //加入到群
        function addtochatgroup(){
            console.log('进来了');
            $.ajax({
                type: "POST",
                url: url+"chatgroups/"+groupid+"/users/"+myid,
                dataType:"html",
                beforeSend: function(request) {
                    console.log('开始加群');
                },
                headers: {
                    "Authorization":"Bearer "+token,
                    "Content-Type":"application/json"
                },
                success: function(result) {
                    console.log(result);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(XMLHttpRequest.status);
                },
            });
        }