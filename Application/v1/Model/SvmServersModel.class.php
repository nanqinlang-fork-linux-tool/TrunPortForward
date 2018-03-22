<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 15/02/2018
 * Time: 3:15 PM
 */
namespace v1\Model;
use Think\Model;
class SvmServersModel extends Model{
    public function getSvmApiAddressById($svm_id)
    {
        return $this->where(array("id"=>$svm_id))->select()[0]['api_address'];
    }

    public function getSvmApiInfoById($svm_server_id)
    {
        return $this->where(array("id"=>$svm_server_id))->select()[0];
    }
}