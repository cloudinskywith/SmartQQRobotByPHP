<?php
/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/13
 * Time: 23:34
 */
include_once "include.php";
$robot_id = $_GET['robot_id'];
/**
 * 即使客户端断开连接也继续执行脚本
 */
ignore_user_abort(true);

/**
 * 不限制脚本的执行时间
 */
set_time_limit(0);

if($robot_id == ""){

}else{
    echo "<pre>";
    $Robot = new Robot($robot_id,$dbClass);
    $RobotMsg = new RobotMsg($robot_id,$dbClass);
    if($Robot->error){

    }else{
        $QQ  = new SmartQQ(CurlUtil::CookiesToArray($Robot->cookie),$Robot->ptwebqq,$Robot->vfwebqq,$Robot->psessionid,$Robot->uin,$Robot->skey,$Robot->bkn);
        if($Robot->online_status != StatusUtil::ONLINE){
            if($Robot->online_status == StatusUtil::INIT){
                $QQ->Curl->fetch("http://127.0.0.1/DianQ/QRcode.php?robot_id=$robot_id");
                $Robot->online_status = StatusUtil::LOADING_VERIFY;
            }
            if($Robot->online_status == StatusUtil::LOADING_VERIFY){
                $RobotMsg->InsertMsg("请使用手机QQ扫描二维码");
                $status = StatusUtil::QRCode_ONLINE;
                while ($status == StatusUtil::QRCode_ONLINE || $status == StatusUtil::QRCode_LOADING) {
                    if($status == StatusUtil::QRCode_ONLINE){
                        $RobotMsg->InsertMsg("请使用手机QQ扫描二维码 二维码未失效~");
                    }elseif($status == StatusUtil::QRCode_LOADING){
                        $RobotMsg->InsertMsg("二维码已扫描成功 请手机确认登陆~");
                    }
                    $status = $QQ->scanQRcode();

                    if($status == StatusUtil::QRCode_FAIL){
                        $RobotMsg->InsertMsg("二维码已失效~ 请重新使用手机QQ扫描二维码");
                        $Robot->setOnlineStatus(StatusUtil::INIT);
                        $QQ->Curl->fetch("http://127.0.0.1/DianQ/QRcode.php?robot_id=$robot_id");
                        $status = StatusUtil::LOADING_VERIFY;
                        break;
                    }elseif($status == StatusUtil::ONLINE){
                        $Robot->setCookie(CurlUtil::CookiesToString($QQ->Curl->cookies));
                        $Robot->setBkn($QQ->bkn);
                        $Robot->setPsessionid($QQ->psessionid);
                        $Robot->setPtwebqq($QQ->ptwebqq);
                        $Robot->setSkey($QQ->skey);
                        $Robot->setVfwebqq($QQ->vfwebqq);
                        $Robot->setOnlineStatus(StatusUtil::ONLINE);
                        $RobotMsg->InsertMsg("登陆成功");
                        break;
                    }
                    sleep(1);
                }

            }

        }
        if($Robot->online_status == StatusUtil::ONLINE){
            $start_time = time();
            while ($Robot->online_status == StatusUtil::ONLINE) {
                $poll = $QQ->poll();

                $poll = json_decode($poll,true);
                if (@array_key_exists('retcode', $poll) && $poll['retcode'] == 0) {
                    $RobotMsg->InsertMsg( '收到' . count($poll['result']) . "条新消息" );
                    if (isset($poll['result']) && count($poll['result']) < 20) {
                        $poll = $QQ->dealMessage($poll);
                        $RobotMsg->InsertMsg($poll['msg'],$poll['senderQQ'],$poll['from_uin']);
                        $orders = $Robot->getPluginOrders();
                        $Plugin = null;
                        $poll['msg'] = isset($poll['msg']) ? $poll['msg'] : "";
                        foreach ($orders AS $order){
                            $pro = explode($order['order_name'],$poll['msg']);
                            if(count($pro) >= 2  && $order['status']){
                                $Plugin = Robot::runPlugin($order['plugin_class'],$poll,$RobotFriend,$RobotGroup,$RobotDiscuss);
                                if($Plugin->MsgCount == 0){
                                    $Plugin = null;
                                    continue;
                                }else{
                                    break;
                                }
                            }
                        }
                        if($Plugin == null){
                            $Plugin = Robot::runPlugin("YiBaoPlugin",$poll,$RobotFriend,$RobotGroup,$RobotDiscuss);
                        }
                        foreach ($Plugin->replyMsg  as $item){
                            switch ($item['type']){
                                case "personal":
                                    if($Robot->is_reply && !$Robot->is_personal_speech){
                                        $QQ->sendMsg($item['uin'],$item['msg']);
                                    }
                                    break;
                                case "group":
                                    if($Robot->is_reply && !$Robot->is_group_speech){
                                        $QQ->sendMsg($item['uin'],$item['msg'],true);
                                    }
                                    break;
                                default:

                                    break;
                            }
                        }


                    }
                } elseif (@array_key_exists('retcode', $poll) && ($poll['retcode'] == 103 || $poll['retcode'] == 100012)) {
                    $RobotMsg->InsertMsg( '身份验证失效，请重新登录 如多次出现 请去官网登陆后注销等即可~' . (isset($poll['errmsg']) ? $poll['errmsg'] : ''));
                    $Robot->setOnlineStatus(StatusUtil::INIT);
                    break;
                } elseif (@array_key_exists('retcode', $poll)) {
                    if (isset($poll['errmsg']) && !isset($poll['errmsg'])) {
                        $RobotMsg->InsertMsg( "Error:".$poll['retcode'].$poll['errmsg']);

                        break;
                    } elseif ($poll['retcode'] == 116) {
                        $RobotMsg->InsertMsg( "例行安全检测");
                    } elseif ($poll['retcode'] == 121) {
                        $is_success = false;
                        if ($robot['is_reconnection'] == 1) {
                            $RobotMsg->InsertMsg( "账号异常,重新连接中");
                            $QQ = new SmartQQ(CurlUtil::CookiesToArray($Robot->cookie),$Robot->ptwebqq,$Robot->vfwebqq,$Robot->psessionid,$Robot->uin,$Robot->skey,$Robot->bkn);
                            $is_success = $QQ->loginSecond(true);
                        }
                        if (!$is_success) {
                            $RobotMsg->InsertMsg( "身份验证失效，请重新登录");
                            $Robot->setOnlineStatus(StatusUtil::INIT);
                        }
                    } else {
                        $RobotMsg->InsertMsg( "CODE:[" . $poll['retcode'] . "]");
                    }
                } elseif (!$poll && (time() - $start_time >= 60)) {
                    //心跳包执行超时则表明没有接收到新消息
                    $RobotMsg->InsertMsg( '暂无新消息');
                }else{
                    $RobotMsg->InsertMsg(json_encode($poll,JSON_UNESCAPED_UNICODE));
                }

                $start_time = time();
            }

        }
    }
}