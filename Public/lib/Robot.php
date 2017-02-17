<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/14
 * Time: 0:10
 */
class Robot
{

    public $id;
    public $user_id;
    public $uin;
    public $name;
    public $online_status;
    public $is_run;
    public $is_reply;
    public $is_group_speech;
    public $is_personal_speech;
    public $cookie;
    public $ptwebqq;
    public $vfwebqq;
    public $psessionid;
    public $qrcode;
    public $skey;
    public $bkn;
    public $run_last_time;
    public $limitdate;
    public $error;


    public function __construct($robot_id,$dbClass){
        $this->id = trim($robot_id);
        $this->dbClass = $dbClass;
        $sql = "SELECT * FROM `dianq_robot` WHERE `id` = '$robot_id' ";
        $rs  = $this->dbClass->query($sql);
        if(mysqli_num_rows($rs) <= 0){
            $this->error = true;
        }else{
            $rs  = $this->dbClass->getone($rs);
            $this->user_id = $rs['user_id'];
            $this->uin = $rs['uin'];
            $this->name = $rs['name'];
            $this->online_status = $rs['online_status'];
            $this->is_run = $rs['is_run'];
            $this->is_personal_speech = $rs['is_personal_speech'];
            $this->is_group_speech = $rs['is_group_speech'];
            $this->is_reply = $rs['is_reply'];
            $this->cookie = $rs['cookie'];
            $this->ptwebqq = $rs['ptwebqq'];
            $this->vfwebqq = $rs['vfwebqq'];
            $this->psessionid = $rs['psessionid'];
            $this->qrcode = $rs['qrcode'];
            $this->skey = $rs['skey'];
            $this->bkn = $rs['bkn'];
            $this->run_last_time = $rs['run_last_time'];
            $this->limitdate = $rs['limitdate'];
            $this->error = false;
        }
    }


    /**
     * @param $plugin_class_name
     * @return null
     */
    public static function runPlugin($plugin_class_name,$getMsg,$RobotFriend,$RobotGroup,$RobotDiscuss) {
        $RobotPlugin = null;
        include_once  "Plugin/$plugin_class_name/".$plugin_class_name.".php";
        eval("@\$RobotPlugin = new " . $plugin_class_name . "(\$getMsg,\$RobotFriend,\$RobotGroup,\$RobotDiscuss);");
        if ($RobotPlugin == null) return RobotMsg::noReply();
        @$RobotPlugin->Start();
        return @$RobotPlugin;
    }


    public function getPluginOrders(){
        $sql = "SELECT * FROM `dianq_plugin_orders` WHERE  1  ";
        $rs  = $this->dbClass->query($sql);
        $array = array();
        while ($r = $this->dbClass->getone($rs)){
            $array[] = $r;
        }
        return $array;
    }


    public function setUserId($user_id){
        $user_id = trim($user_id);
        $sql = "UPDATE `dianq_robot` SET `user_id` = '$user_id' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->user_id = $user_id;
    }

    public function setName($name){
        $name = trim($name);
        $sql = "UPDATE `dianq_robot` SET `name` = '$name' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->name = $name;
    }

    public function setOnlineStatus($OnlineStatus){
        $OnlineStatus = trim($OnlineStatus);
        $sql = "UPDATE `dianq_robot` SET `online_status` = '$OnlineStatus' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->online_status = $OnlineStatus;
    }

    public function setIsRun($is_run){
        $is_run = trim($is_run);
        $sql = "UPDATE `dianq_robot` SET `is_run` = '$is_run' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->is_run = $is_run;
    }


    /**
     * @param mixed $is_group_speech
     */
    public function setIsGroupSpeech($is_group_speech){
        $is_group_speech = trim($is_group_speech);
        $sql = "UPDATE `dianq_robot` SET `is_group_speech` = '$is_group_speech' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->is_group_speech = $is_group_speech;
    }


    /**
     * @param mixed $is_personal_speech
     */
    public function setIsPersonalSpeech($is_personal_speech){
        $is_personal_speech = trim($is_personal_speech);
        $sql = "UPDATE `dianq_robot` SET `is_personal_speech` = '$is_personal_speech' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->is_personal_speech = $is_personal_speech;
    }


    /**
     * @param mixed $is_reply
     */
    public function setIsReply($is_reply){
        $is_reply = trim($is_reply);
        $sql = "UPDATE `dianq_robot` SET `is_reply` = '$is_reply' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->is_reply = $is_reply;
    }

    public function setCreateUin($create_uin){
        $create_uin = trim($create_uin);
        $sql = "UPDATE `dianq_robot` SET `create_uin` = '$create_uin' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->create_uin = $create_uin;
    }

    public function setCookie($cookie){
        $cookie = trim($cookie);
        $sql = "UPDATE `dianq_robot` SET `cookie` = '$cookie' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->cookie = $cookie;
    }

    public function setPtwebqq($ptwebqq){
        $ptwebqq = trim($ptwebqq);
        $sql = "UPDATE `dianq_robot` SET `ptwebqq` = '$ptwebqq' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->ptwebqq = $ptwebqq;
    }

    public function setVfwebqq($vfwebqq){
        $vfwebqq = trim($vfwebqq);
        $sql = "UPDATE `dianq_robot` SET `vfwebqq` = '$vfwebqq' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->vfwebqq = $vfwebqq;
    }

    public function setPsessionid($psessionid){
        $psessionid = trim($psessionid);
        $sql = "UPDATE `dianq_robot` SET `psessionid` = '$psessionid' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->psessionid = $psessionid;
    }

    public function setQRcode($qrcode){
        $qrcode = trim($qrcode);
        $sql = "UPDATE `dianq_robot` SET `qrcode` = '$qrcode' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->qrcode = $qrcode;
    }

    public function setSkey($skey){
        $skey = trim($skey);
        $sql = "UPDATE `dianq_robot` SET `skey` = '$skey' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->skey = $skey;
    }

    public function setBkn($bkn){
        $bkn = trim($bkn);
        $sql = "UPDATE `dianq_robot` SET `bkn` = '$bkn' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->bkn = $bkn;
    }

    public function setRunLastTime($run_last_time){
        $run_last_time = trim($run_last_time);
        $sql = "UPDATE `dianq_robot` SET `run_last_time` = '$run_last_time' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->run_last_time = $run_last_time;
    }

    public function setLimitdate($limitdate){
        $limitdate = trim($limitdate);
        $sql = "UPDATE `dianq_robot` SET `limitdate` = '$limitdate' WHERE `id` = '$this->id' ";
        $this->dbClass->query($sql);
        $this->limitdate = $limitdate;
    }





}