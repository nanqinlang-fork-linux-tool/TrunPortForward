<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 14/02/2018
 * Time: 9:41 PM
 */
namespace v1\Model;
use Think\Model;
class UsersModel extends Model{
    public function getUserInfoByUid($uid){
        $_q = $this->where(array("uid"=>$uid))->select();
        if (!$_q) {
            return false;
        }
        return $_q[0];
    }
}