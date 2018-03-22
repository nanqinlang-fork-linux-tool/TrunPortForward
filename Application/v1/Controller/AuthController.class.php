<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 14/02/2018
 * Time: 9:50 PM
 */
namespace v1\Controller;
use Main\Model\SessionsModel;
use Main\Model\TokenModel;
use v1\Model\UsersModel;

class AuthController extends BaseController{
    public function login()
    {
        if (!empty(I('get.token')))
        {
            //若有token
            $token = I('get.token');
            $imsDbConfig = array(
                'db_type'    =>   'mysql',
                'db_host'    =>   'localhost',
                'db_user'    =>   '',
                'db_pwd'     =>   '',
                'db_port'    =>    3306,
                'db_name'    =>    '',
                'db_charset' =>    'utf8',
            );
            $Token = new TokenModel('token','ims_',$imsDbConfig);
            $uid = $Token->getTokenUid($token);
            if (!$uid) {//若token无效或过期则登录
                redirect(C('SSO_URL').'?next='.urlencode(getRootURL().'/auth/login'));
            }
            $Users = new UsersModel('users','ims_',$imsDbConfig);
            $Sessions = new SessionsModel();
            if (logon()) {
                session('uid',$uid);
                cookie('uid',$uid);
                $Sessions->checkSession($uid);
            } else {
                session('uid',$uid);
                cookie('uid',$uid);
                $Sessions->createSession($uid);
            }
            redirect('/vms');
        } else {
            //若无token
            redirect(C('SSO_URL').'?next='.urlencode(getRootURL().'/auth/login'));
        }
    }

    public function logout(){
        session(null);
        cookie(null);
        $_COOKIE['uid'] = null;
        $_SESSION['uid'] = null;
        redirect(C('SSO_RootURL').'/auth/logout');
    }
}