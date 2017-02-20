<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/14
 * Time: 13:37
 */
class RobotPlugin
{
    public $Robot;
    public $RobotFriend;
    public $RobotGroup;
    public $RobotDiscuss;
    public $replyMsg;
    public $MsgCount;
    public $getMsg;


    public function __construct($getMsg,$Robot,$RobotFriend,$RobotGroup,$RobotDiscuss){
        $this->RobotFriend  = $RobotFriend;
        $this->RobotGroup   = $RobotGroup;
        $this->RobotDiscuss = $RobotDiscuss;
        $this->Robot = $Robot;
        $this->MsgCount     = 0;
        $this->getMsg       = $getMsg;
        $this->replyMsg     = array(

        );
    }

    /**
     * @param int $MsgCount
     */
    public function setMsgCount($MsgCount){
        $this->MsgCount = $MsgCount;
    }

    /**
     * @param $msg 回复的消息
     * @param $uin 回复者uin 群为group_code 讨论组为did
     * @param $type personal|group|sysGroup|did
     * @param string $other
     */
    public function setReplyMsg($msg , $uin , $type ,$other = ""){
        $msg = array(
            'type' =>$type,
            'uin'  =>$uin,
            'msg'  =>$msg,
        );
        array_push($this->replyMsg,$msg);
    }

    public function isManager($QQ){
        return $this->Robot->create_uin == $QQ ? true : false;
    }

}