<?php
namespace v1\Controller;

use Curl\Curl;

class IndexController extends BaseController {
    public function index(){
        if (logon()) {
            redirect('/vms');
        } else {
            redirect('/auth/login');
        }
    }

    public function t1()
    {
        $Curl = new Curl();
        $res = $Curl->post('',array(
            "identifier" => "",
            "secret" => "",
            "responsetype" => "json",
            "action" => "GetClientsProducts",
            "clientid" => 980,
        ));
        var_dump(json_decode($res->response,true)['products']['product']);

    }
}