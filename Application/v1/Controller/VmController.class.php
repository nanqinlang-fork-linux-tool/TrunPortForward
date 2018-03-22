<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 14/02/2018
 * Time: 11:36 PM
 */
namespace v1\Controller;

use v1\Model\PortsModel;
use v1\Model\SvmServersModel;
use v1\Model\VmsModel;

class VmController extends BaseController{
    public function vmList()
    {
        $Vms = new VmsModel();
        $vms_info = $Vms->where(array("uid"=>getUID()))->select();

        for ($i=0;$i<count($vms_info);$i++)
        {
            $svm_api_info = (new SvmServersModel())->getSvmApiInfoById($vms_info[$i]['svm_server_id']);
            $SolusVMInstance = new \Solus($svm_api_info['api_address'],$svm_api_info['api_id'],$svm_api_info['api_key']);
            $this_vm_info = $SolusVMInstance->getServerInfo(intval($vms_info[$i]['svm_vm_id']));
            $this_vm_state = $SolusVMInstance->getServerState(intval($vms_info[$i]['svm_vm_id']),false,true);
            //更新hostname
            if ($vms_info[$i]['hostname'] != $this_vm_info['hostname'])
            {
                $Vms->where(array("id"=>$vms_info[$i]['id']))->data(array("hostname"=>$this_vm_info['hostname']))->save();
            }

            $vms_info[$i]['port_usages'] = (new PortController())->port_usages($vms_info[$i]['id']);
            $vms_info[$i]['main_ipaddress'] = $this_vm_info['ipaddress'];
            $vms_info[$i]['hostname'] = $this_vm_info['hostname'];
            $vms_info[$i]['memory'] = $this_vm_info['memory'];
            $vms_info[$i]['cpus'] = $this_vm_info['cpus'];
            $vms_info[$i]['state'] = $this_vm_state['state'];
        }

        $this->assign('vms_info',$vms_info);
        $this->assign('SideBar_Selected','VM_vmList');
        $this->meta_title = '虚拟机';
        $this->display();
    }

    public function addVm()
    {
        $this->assign('SideBar_Selected','VM_vmAdd');
        $this->meta_title = '添加虚拟机';
        $this->display();
    }
}