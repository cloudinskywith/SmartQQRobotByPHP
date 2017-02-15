<?php
/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/13
 * Time: 23:09
 */
include_once "include.php";
header("Content-Type:image/png");
$robot_id = $_GET['robot_id'];
if($robot_id == ""){
    echo  file_get_contents("Public/images/ico.png");
}else{
    $Robot = new Robot($robot_id,$dbClass);
    if($Robot->error){
        echo  file_get_contents("Public/images/ico.png");
    }else{
        if($Robot->online_status  == StatusUtil::ONLINE){
            $img = file_get_contents("Public/images/ico.png");
            exit($img);
        }
        if($Robot->online_status  == StatusUtil::INIT){
            $QQ = new SmartQQ();
            $QQ->getQRcode();
            $cookie = CurlUtil::CookiesToString($QQ->cookies);
            $qrcode = $QQ->results;
            $Robot->setQRcode(addslashes($qrcode));
            $Robot->setOnlineStatus(StatusUtil::LOADING_VERIFY);
            $Robot->setCookie($cookie);
            $Robot->setVfwebqq("");
            $Robot->setPtwebqq("");
            $Robot->setSkey("");
            $Robot->setPsessionid("");
            $Robot->setBkn("");
            echo $qrcode;
        }else{
            echo $Robot->qrcode;
        }
    }
}