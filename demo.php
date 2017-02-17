<?php
/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/13
 * Time: 17:14
 */
include_once "include.php";

echo "<pre>";
$robot_id = 2;
$Robot = new Robot($robot_id,$dbClass);
$QQ  = new SmartQQ(CurlUtil::CookiesToArray($Robot->cookie),$Robot->ptwebqq,$Robot->vfwebqq,$Robot->psessionid,$Robot->uin,$Robot->skey,$Robot->bkn);
//
//print_r(SmartQQ::hashUin(3544348672,'e1ad6e32241fa4d631da686a59c38a18a0f7707f01d3d62acd537fcb41db025f'));
//
//
$RobotFriend = new RobotFriend($robot_id,$dbClass,$QQ);
//print_r($QQ->getFriendUinList());
//$RobotGroup = new RobotGroup($robot_id,$dbClass,$QQ);
//$RobotDiscuss = new RobotGroup($robot_id,$dbClass,$QQ);
//$poll = $QQ->poll();
//$poll = json_decode($poll,true);
//$poll = $QQ->dealMessage($poll);
//$orders = $Robot->getPluginOrders();
//$Plugin = null;
//$poll['msg'] = isset($poll['msg']) ? $poll['msg'] : "";
//foreach ($orders AS $order){
//    $pro = explode($order['order_name'],$poll['msg']);
//    if(count($pro) >= 2  && $order['status']){
//        $Plugin = Robot::runPlugin($order['plugin_class'],$poll,$RobotFriend,$RobotGroup,$RobotDiscuss);
//        if($Plugin->MsgCount == 0){
//            $Plugin = null;
//            continue;
//        }else{
//            break;
//        }
//    }
//}
//if($Plugin == null){
//    $Plugin = Robot::runPlugin("YiBaoPlugin",$poll,$RobotFriend,$RobotGroup,$RobotDiscuss);
//}
//foreach ($Plugin->replyMsg  as $item){
//    switch ($item['type']){
//        case "personal":
//            $QQ->sendMsg($item['uin'],$item['msg']);
//            break;
//        case "group":
//            $QQ->sendMsg($item['uin'],$item['msg'],true);
//            break;
//        default:
//
//            break;
//    }
//}



print_r($RobotFriend->updateFriendInfo());

//print_r($RobotGroup->updateGroupInfo());


//print_r($RobotGroup->addMember("194233857","1353693508"));

//$QQ->getGroupList();
//print_r($QQ->dealMessage(json_decode($QQ->results,true)));
//$QQ->getQRcode();
//$QQ->scanQRcode("XjBkuz3-8cyryP32DUMwQkp3y8jVL0xmq9-zaRFnPjnTGTAS6olD3PL*q01KFd2V");
//$QQ->setMemberSpeech("194233857","3544348672",60);
//echo  $QQ->results ;
