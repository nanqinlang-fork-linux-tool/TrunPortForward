<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 17/02/2018
 * Time: 12:32 PM
 */
namespace v1\Controller;
use function Couchbase\defaultDecoder;
use v1\Model\HostsModel;
use v1\Model\IpsModel;
use v1\Model\PortsModel;
use v1\Model\VmsModel;

class PortController extends BaseController{
    public function port_usages($vm_id)
    {
        $all_count = (new VmsModel())->where(array("id"=>$vm_id))->select()[0]['port_allocated_count'];
        $used_count = (new PortsModel())->where(array("apply_status"=>"UNUSED","vm_id"=>$vm_id,"status"=>"NORMAL"))->count()+(new PortsModel())->where(array("apply_status"=>"APPLIED","vm_id"=>$vm_id,"status"=>"NORMAL"))->count();
        return array("allocated"=>$all_count,"used"=>$used_count);
    }

    public function applies_view()
    {
        $Vms = new VmsModel();
        $user_vms = $Vms->where(array("uid"=>getUID()))->order('id desc')->select();
        $Ports = new PortsModel();
        $user_ports = [];
        $j = 0;
        switch (I('get.type'))
        {
            case 'approved':
                $type = 'USED';
                break;
            case 'applied':
                $type = 'APPLIED';
                break;
            case 'rejected':
                $type = 'REJECTED';
                break;
            default:
                $this->error('Invalid Type!');
        }
        //TODO:用户部分数量统计
        for ($i=0;$i<count($user_vms);$i++)
        {
            $this_vm_ports_info = $Ports->where(array("vm_id"=>$user_vms[$i]['id'],"apply_status"=>$type))->select();
            for ($m=0;$m<count($this_vm_ports_info);$m++)
            {
                $user_ports[$j] = $this_vm_ports_info[$m];
                $j += 1;
            }
        }
        $this->assign('ports_info',$user_ports);
        $this->assign('SideBar_Selected','Port_Applies');
        $this->meta_title = '申请列表';
        $this->display();
    }

    public function apply()
    {
        $vmid = I('get.vm');
        $Vms = new VmsModel();
        $Ips = new IpsModel();
        $Ports = new PortsModel();
        $vm_info = $Vms->where(array("id"=>$vmid,"uid"=>getUID()))->select()[0];
        if (!$vm_info)//若信息不合法
        {
            $this->error('请在"全部虚拟机"部分选择要申请端口的虚拟机，然后选择右侧操作的"申请新端口"','/vms',4);
            exit;
        }

        $ips_info = $Ips->where(array("host_id"=>$vm_info['host_id']))->select();
        for ($i=0;$i<count($ips_info);$i++)
        {
            $first_ports_info = $Ports->where(array("ip_id"=>$ips_info[$i]['id'],"apply_status"=>"UNUSED","status"=>"NORMAL"))->order('port asc')->select();

            /*$J = 0;
            $bigJ = 0;
            foreach ($first_ports_info as $key => $value)
            {
                $second_ports_info[$bigJ][$J] = $first_ports_info[$J];
                $J += 1;
                if ($bigJ+1 % 8 == 0 ) $bigJ ++;
            }*/


            $ports_info[$ips_info[$i]['ip_address']] = $first_ports_info;
        }


        $this->assign('port_usages',$this->port_usages($vmid));
        $this->assign('ips_info',$ips_info);
        $this->assign('ports_info',$ports_info);
        $this->assign('vm_info',$vm_info);
        $this->assign('SideBar_Selected','Port_Apply');
        $this->meta_title = '提交申请';
        $this->display();
    }

    public function Action_releasePort()
    {
        ApiResponseHeader();
        $Ports = new PortsModel();
        $port_id = I('post.port_id');
        $port_info = $Ports->where(array("id"=>$port_id))->select()[0];
        $Vms = new VmsModel();
        $vm_info = $Vms->where(array("id"=>$port_info['vm_id']))->select()[0];
        if ($vm_info['uid'] != getUID() or $port_info['apply_status'] == 'RELEASED')
        {
            echo json_encode(array(
                "error" => true,
                "msg" => "Access Denied"
            ));exit;
        } else {
            $Hosts = new HostsModel();
            $port_info = $Ports->where(array("id"=>$port_id))->select()[0];
            $vm_info = (new VmsModel())->where(array("id"=>$port_info['vm_id']))->select()[0];
            $host_info = $Hosts->where(array("id"=>$vm_info['host_id']))->select()[0];
            $Hosts->closePortForward($host_info['id'],$port_id);

            $Ports->where(array("id"=>$port_id))->data(array("apply_status"=>"RELEASED","released_at"=>getDateTime()))->save();
            $Ports->data(array(
                "range_id" => $port_info['range_id'],
                "port" => $port_info['port'],
                "ip_id" => $port_info['ip_id'],
                "create_at" => $port_info['create_at'],
                "status" => $port_info['status'],
                "apply_status" => "UNUSED",
            ))->add();
            LOGGER('port_id:'.$port_id.' Released',"PORT_RELEASE");
            echo json_encode(array(
                "success" => true,
                "msg" => "成功释放端口"
            ));
        }
    }

    public function Action_submitPort()
    {
        //TODO:验证VM身份(Solved)
        //TODO:限制申请数量(Solved)
        ApiResponseHeader();
        $port_id = I('post.port_id');
        $connect_port = I('post.connect_port');
        $vm_id = I('post.vm_id');
        $Ports = new PortsModel();
        $Vms = new VmsModel();
        $port_info = $Ports->where(array("id"=>$port_id,"apply_status"=>"UNUSED","status"=>"NORMAL"))->select()[0];
        $vm_info = $Vms->where(array("id"=>$vm_id,"uid"=>getUID()))->select()[0];
        $port_usage = $this->port_usages($vm_id);
        if (empty($port_info) or empty($vm_info) or !is_integer(intval($connect_port)) or $port_usage['allocated']<=$port_usage['used'])
        {
            echo json_encode(array(
                "error" => true,
                "msg" => "发生错误，请刷新后重试。"
            ));exit;
        } else {
            $Ports->where(array("id"=>$port_id))->data(array(
                "connect_port" => $connect_port,
                "port" => $port_info['port'],
                "vm_id" => $vm_id,
                "apply_status" => "APPLIED",
            ))->save();
            LOGGER('Applied port_id '.$port_id,"APPLY_PORT");
            echo json_encode(array(
                "success" => true,
                "msg" => "已成功提出申请，请耐心等待管理员审核！"
            ));
        }
    }
}