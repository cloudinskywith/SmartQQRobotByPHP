<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/14
 * Time: 1:09
 */
class RobotMsg
{

    public $robot_id;
    public $dbClass;
    public $QQ;
    public $replyMsg;
    public function __construct($robot_id,$dbClass,$QQ = ""){
        $this->robot_id = $robot_id;
        $this->dbClass  = $dbClass;
        $this->QQ       = $QQ;

    }

    public function InsertMsg($msg , $from = "" , $group_uin = ""){
        $time = time();
        $msg  = trim($msg);
        $from = trim($from);
        $group_uin   = trim($group_uin);
        $sql  = "INSERT INTO `dianq_robot_msg`( `robot_id`, `msg`, `from_uin`, `group_uin`, `createdate`) VALUES ('$this->robot_id','$msg','$from','$group_uin','$time')";
        $this->dbClass->query($sql);
    }


    public static function noReply(){
        return null;
    }


}