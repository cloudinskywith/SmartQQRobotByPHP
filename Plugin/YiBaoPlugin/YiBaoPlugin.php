<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/15
 * Time: 23:49
 */
class YiBaoPlugin extends RobotPlugin
{

    public function Start(){
        $msg    = $this->getMsg['msg'];
        $type   = $this->getMsg['type'];
        $userId = $this->getMsg['senderQQ'];
        $url   = "http://www.kilingzhang.com/YiBao/api.php?role=YiBao&hash=30e9719f4fd15c3f01eb87a6770ff60d&text=$msg&userId=$userId";
        switch ($type){
            case "personalMsg":
                $json = file_get_contents($url);
                $data = json_decode($json,true);
                $this->setMsgCount(1);
                $this->setReplyMsg($data['data'],$this->getMsg['from_uin'],"personal");
                break;
            case "groupMsg":
                $json = file_get_contents($url);
                $data = json_decode($json,true);
                $this->setMsgCount(2);
                $this->setReplyMsg($data['data'],$this->getMsg['send_uin'],"personal");
                $this->setReplyMsg("已经私聊你了傻子~\n      --学长大大",$this->getMsg['from_uin'],"group");
                break;
            case "sysGroupMsg":


                break;
            case "didMsg":


                break;
        }

    }


}