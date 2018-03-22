<?php
namespace Main\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        redirect('/auth/login');
    }
}