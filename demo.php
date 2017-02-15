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
//$QQ  = new SmartQQ();
$QQ  = new SmartQQ(CurlUtil::CookiesToArray($Robot->cookie),$Robot->ptwebqq,$Robot->vfwebqq,$Robot->psessionid,$Robot->uin,$Robot->skey,$Robot->bkn);
//
//$QQ->getFriendUinList();
$RobotGroup = new RobotGroup($robot_id,$dbClass,$QQ);
//print_r($RobotFriend->updateFriendInfo());
//print_r($RobotGroup->getGroupGCList());
print_r($RobotGroup->banMemberSpeechAll("194233857",10));

//$QQ->getGroupList();
//print_r($QQ->dealMessage(json_decode($QQ->results,true)));
//$QQ->getQRcode();
//$QQ->scanQRcode("XjBkuz3-8cyryP32DUMwQkp3y8jVL0xmq9-zaRFnPjnTGTAS6olD3PL*q01KFd2V");
//$QQ->setMemberSpeech("194233857","3544348672",60);
//echo  $QQ->results ;
