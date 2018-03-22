<?php
namespace Main\Model;
use Think\Model;

class UsersModel extends Model
{
    public function getUserInfoByUid($uid){
        $_q = $this->where(array("uid"=>$uid))->select();
        if (!$_q) {
            return false;
        }
        return $_q[0];
    }

    public function userExists($uid){}
}