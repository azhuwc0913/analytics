<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/18
 * Time: 11:22
 */
namespace Center\Model;
use Think\Crypt\Driver\Think;
use Think\Model;

class user extends Model
{
    /**
     * 自动验证
     */


    /**
     * 自动完成
     */

    protected $_auto = [
        ['password','md5',3,'function'],
        ['regdate','time',1,'function'],
        //['regip','get_client_ip',1,'function']
        ];


}