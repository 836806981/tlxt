<?php
return array(
	//'配置项'=>'配置值'
    'DB_TYPE' =>'mysql',
    'DB_HOST' =>'127.0.0.1',
    'DB_NAME'   => 'xt2', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root', // 密码
    'DB_PORT' =>'3306',
    'DB_PREFIX' =>'',

    'SESSION_AUTO_START'=>true,
    'USER_AUTH_KEY'=>'authId',

    'TMPL_PARSE_STRING' => array(
        '__UPLOADS__'=>'/Uploads',
        '__PUBLIC__'=>'/Home/View/Public',
    ),
);