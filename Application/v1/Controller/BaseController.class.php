<?php
// +----------------------------------------------------------------------
// | Urox PayinOne Gateways:Handler
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2017 https://www.northme.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Victor.Chen <victor.chen@northme.com>
// +----------------------------------------------------------------------

namespace v1\Controller;
use Main\Model\SessionsModel;
use v1\Model\UsersModel;
use Think\Auth;
use Think\Controller;

class BaseController extends Controller
{
    protected $Auth;

    protected $user_info;

    public function _initialize()
    {
        $System = M('system')->getField('name,value');
        C($System);
        if (C('site-open')=="OFF")
        {
            $this->show(C('site-off-notice-html'),'utf8');exit;
        }
        //$User = new UsersModel();
        if (logon() and ACTION_NAME != 'login') {
            $Sessions = new SessionsModel();
            if (!$Sessions->checkSession(getUID())) {
                session(null);
                cookie(null);
                $this->error('你的登录已过期，请重新登录.','/auth/login',3);
            }
            //$this->assign('messages',M('messages')->field('title,create_at')->where(array("uid"=>$_SESSION['uid']))->select());
        }
        $this->user_info = (new UsersModel('users','ims_'))->getUserInfoByUid($_SESSION['uid']);
        $this->assign('user_info',$this->user_info);
        if (!logon() and ACTION_NAME != 'login' and ACTION_NAME != 'register' and ACTION_NAME != 'logout' and ACTION_NAME != 'index') {
            $this->error('未登录！正在跳转至登录页...','/auth/login');
        } else {
            /*if (ACTION_NAME != 'login' and ACTION_NAME != 'register' and ACTION_NAME != 'logout' and session('uid')!=1){
                $this->Auth = new Auth();
                if (!$this->Auth->check(CONTROLLER_NAME.'/'.ACTION_NAME,getUID()))
                {
                    $this->error('无权限访问此页面！','/');
                }
            }*/
            //TODO:Auth类认证
        }
    }
}