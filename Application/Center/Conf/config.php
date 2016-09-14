<?php
return array(
	//'閰嶇疆椤�'=>'閰嶇疆鍊�'
        'DB_HOST' => 'localhost',
	'DB_TYPE' => 'mysql',
	'DB_NAME' => 'logitics',
	'DB_USER' => 'root',
	'DB_PWD' => 'root',
        'DB_PORT' => '3306',
        'DB_PREFIX'=>'an_',
	'DEFAULT_MODULE'     => 'Home', //榛樿妯″潡
        'URL_MODEL'          => '0', //URL妯″紡
        'SESSION_AUTO_START' => true, //鏄惁寮�鍚痵ession
        'URL_ROUTER_ON'	=> true,
        // 'DB_PARAMS'    =>    array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
    
        'URL_ROUTE_RULES'=>array(
            'notify'          => 'Center/index/notify',
            'callback'          => 'Payment/index/callback',
            'returnurl'          => 'Payment/index/returnurl',
        ),
	'URL_MODEL'=>0,
    'kaka-games-sub' => 'admin.kaka-games.com/site/return-msg',
    'surprizeboxx-sub' => 'admin.surprizeboxx.com/site/return-msg',
    'kaka-games-static' => 'admin.kaka-games.com/site/return-static',
    'kaka-games-id' => 1,
    'surprizeboxx-id' => 2
 	//'LOG_RECORD' => true, //
);