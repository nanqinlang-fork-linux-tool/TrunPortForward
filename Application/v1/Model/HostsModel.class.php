<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 15/02/2018
 * Time: 3:08 PM
 */
namespace v1\Model;
use phpseclib\Net\SSH2;
use Think\Model;
class HostsModel extends Model{
    public function getHostInfoById($host_id)
    {
        return $this->where(array("id"=>$host_id))->select()[0];
    }

    public function openPortForwardTCP($host_id,$listen_address,$listen_port,$connect_port,$connect_address,$port_id)
    {
        $host_info = $this->where(array("id"=>$host_id))->select()[0];
        $SSH = new SSH2($host_info['ssh_address'],$host_info['ssh_port']);
        if (!$SSH->login('root', $host_info['ssh_password'])) {
            exit('Login Failed');
        }
        $cmd2 = '
cat << EOT > /home/rinetd/conf/'.$port_id.'.conf
logfile /home/rinetd/log/'.$port_id.'.log
logcommon
'.$listen_address.' '.$listen_port.' '.$connect_address.' '.$connect_port.'
EOT

rinetd -c /home/rinetd/conf/' . $port_id . '.conf


';

            $result1 = $SSH->exec($cmd2);

        return true;
    }

    public function closePortForward($host_id,$port_id)
    {
        $host_info = $this->where(array("id"=>$host_id))->select()[0];
        $SSH = new SSH2($host_info['ssh_address'],$host_info['ssh_port']);
        if (!$SSH->login('root', $host_info['ssh_password'])) {
            exit('Login Failed');
        }
        $cmd = 'ps -ef | grep "rinetd -c /home/rinetd/conf/'.$port_id.'.conf" | grep -v grep | cut -c 9-15 | xargs kill -s 9';
        $result = $SSH->exec($cmd);
        return true;
    }
}