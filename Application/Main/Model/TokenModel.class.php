<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 14/02/2018
 * Time: 9:02 PM
 */
namespace Main\Model;
use Think\Model;
class TokenModel extends Model{
    /**
     * 生成token
     * @param $uid
     * @return string
     */
    public function createToken($uid)
    {
        $token = date('YmdH').GetRandString(48);
        $this->data(array(
            "token" => $token,
            "uid" => $uid,
            "create_at" => getDateTime(),
        ))->add();
        return $token;
    }

    /**
     * 验证token
     * @param $token
     * @param $uid
     * @return bool true为成功 false为超时或不存在
     */
    public function validateToken($token,$uid)
    {
        $q = $this->where(array(
            "uid" => $uid,
            "token" => $token,
        ))->where('`create_at`>="'.date('Y-m-d H:i:s',time()-600).'"')->select();
        if (!$q) {
            return false;
        } else {
            return true;
        }
    }

    public function getTokenUid($token)
    {
        $q = $this->where(array(
            "token" => $token,
        ))->where('`create_at`>="'.date('Y-m-d H:i:s',time()-600).'"')->select();
        if (!$q) {
            return false;
        } else {
            return $q[0]['uid'];
        }
    }
}