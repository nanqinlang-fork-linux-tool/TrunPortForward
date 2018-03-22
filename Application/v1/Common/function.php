<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 2017/10/22
 * Time: 下午11:36
 */

/**
 * 通过阅卷状态代码获取阅卷状态
 * @param $code
 * @return mixed
 */
function getVmStatusFromStatus($code)
{
    $rel = array(
        "online" => "在线",
        "offline" => "不在线",
        "disabled" => "不可用",
    );
    return $rel[$code];
}

/**
 * 和上面那个函数一块用的 返回html里label的颜色
 * @param $code
 * @return string
 */
function getVmStatusLabel($code)
{
    $rel = array(
        "online" => "success",
        "offline" => "danger",
        "disabled" => "warning",
    );
    return $rel[$code];
}

function getPortStatusFromStatus($code)
{
    $rel = array(
        "NORMAL" => "正常",
        "CANCELLED" => "已取消",
    );
    return $rel[$code];
}

function getPortApplyStatusFromStatus($code)
{
    $rel = array(
        "UNUSED" => "未占用",
        "APPLIED" => "已提出申请",
        "REJECTED" => "申请被驳回",
        "USED" => "使用中",
        "RELEASED" => "已释放",
    );
    return $rel[$code];
}
