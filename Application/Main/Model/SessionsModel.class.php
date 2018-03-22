<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 2017/11/11
 * Time: 下午5:53
 */
namespace Main\Model;
use Think\Model;
class SessionsModel extends Model{
    public function createSession($uid)
    {
        return $this->data(array(
            "uid" => $uid,
            "session_id" => session_id(),
            "create_at" => getDateTime(),
            "update_at" => getDateTime(),
        ))->add();
    }

    /**
     * 通过两个时间获取session状态
     * @param $create_at
     * @param $update_at
     * @return bool 返回true为可用 false为已过期
     */
    public function getSessionStateBySessionTime($update_at)
    {
        return time()<=strtotime($update_at)+C('session-maxtime');
    }

    public function checkSession($uid)
    {
        $session_info = $this->where(array("uid"=>$uid))->order('id desc')->select();
        if (!$session_info) return false;
        if ($this->getSessionStateBySessionTime($session_info[0]['update_at'])) {
            $this->where(array("id"=>$session_info[0]['id']))->data(array("update_at"=>getDateTime()))->save();
            return true;
        } else {
            return false;
        }
    }
}