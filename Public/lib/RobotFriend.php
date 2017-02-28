<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/14
 * Time: 21:02
 */
class RobotFriend
{
    public $robot_id;
    public $QQ;
    public $dbClass;
    public $errorInfo;
    public $successInfo;
    public function __construct($robot_id,$dbClass,$QQ){
        $this->robot_id = $robot_id;
        $this->QQ       = $QQ;
        $this->dbClass  = $dbClass;
    }

    /**
     * 通过UIN查找QQ信息 存在直接从库里拿  不存在则在线查询
     * @param $uin send_uin
     * @return mixed
     */
    public function getFriendInfoByUin($uin){
        $qq  = $this->QQ->getFriendQQBySendUin($uin);
        $sql = "SELECT * FROM `dianq_friends_info` WHERE `robot_id` = '$this->robot_id' AND `qq` = '{$qq}'";
        $rs  = $this->dbClass->query($sql);
        if(mysqli_num_rows($rs) > 0){
            $result = $this->dbClass->getone($rs);
            return $result;
        }else{
            $result = $this->QQ->getFriendInfoByUin($uin);
            $result = json_decode($result,true);
            $result = $result['result'];
            $sql = "INSERT INTO `dianq_friends_info`(`robot_id`, `qq`, `face`, `birthday`, `birthday_y`, `birthday_m`, `birthday_d`, `occupation`, `phone`, `allow`, `college`, `constel`, `blood`, `homepage`, `stat`, `vip_info`, `country`, `city`, `personal`, `nick`, `shengxiao`, `email`, `province`, `gender`, `mobile`)
                                  VALUES ('{$this->robot_id}','{$qq}','{$result['face']}','{$result['birthday']['year']}{$result['birthday']['month']}{$result['birthday']['day']}','{$result['birthday']['year']}','{$result['birthday']['month']}','{$result['birthday']['day']}','{$result['occupation']}','{$result['phone']}','{$result['allow']}','{$result['college']}','{$result['constel']}','{$result['blood']}','{$result['homepage']}','{$result['stat']}','{$result['vip_info']}','{$result['country']}','{$result['city']}','{$result['personal']}','{$result['nick']}','{$result['shengxiao']}','{$result['email']}','{$result['province']}','{$result['gender']}','{$result['mobile']}')";
            $this->dbClass->query($sql);
        }
        $sql = "SELECT * FROM `dianq_friends_info` WHERE `robot_id` = '$this->robot_id' AND `qq` = '$qq'";
        $rs  = $this->dbClass->query($sql);
        $result = $this->dbClass->getone($rs);
        return $result;
    }

    /**
     * 更新当前好友资料 UIN不存在则更新全部好友资料
     * @param string $uin
     */

    public function updateFriendInfo($uin = ""){
        $this->errorInfo = 0;
        if($uin == ""){
            $result = $this->QQ->getFriendUinList();
            $result = json_decode($result,true);
            if($result['retcode'] != 0){
                return false;
            }
            $result = $result['result']['friends'];
            if(empty($result) || $result == null || !isset($result)){
                return false;
            }
            set_time_limit(0);
            $this->successInfo = count($result);
            foreach ($result as $item){
                self::updateFriendInfo($item['uin']);
            }
        }else{
            $result = $this->QQ->getFriendInfoByUin($uin);
            $result = json_decode($result,true);
            if(!key_exists('result',$result) || $result['result'] == ""){
                $this->errorInfo ++;
                return ;
            }
            $result = $result['result'];
            $qq  = $this->QQ->getFriendQQBySendUin($uin);
            $sql = "SELECT * FROM `dianq_friends_info` WHERE `robot_id` = '$this->robot_id' AND `qq` = '{$qq}'";
            $rs  = $this->dbClass->query($sql);
            if(mysqli_num_rows($rs) > 0){
                $sql = "UPDATE `dianq_friends_info` SET
                        `face`='{$result['face']}',`birthday`='{$result['birthday']['year']}{$result['birthday']['month']}{$result['birthday']['day']}',`birthday_y`='{$result['birthday']['year']}',`birthday_m`='{$result['birthday']['month']}',`birthday_d`='{$result['birthday']['day']}',`occupation`='{$result['occupation']}',`phone`='{$result['phone']}',`allow`='{$result['allow']}',`college`='{$result['college']}',`constel`='{$result['constel']}',`blood`='{$result['blood']}',`homepage`='{$result['homepage']}',`stat`='{$result['stat']}',`vip_info`='{$result['vip_info']}',`country`='{$result['country']}',`city`='{$result['city']}',`personal`='{$result['personal']}',`nick`='{$result['nick']}',`shengxiao`='{$result['shengxiao']}',`email`='{$result['email']}',`province`='{$result['province']}',`gender`='{$result['gender']}',`mobile`='{$result['mobile']}',`uin`='$uin'
                      WHERE `robot_id` = '$this->robot_id' AND `qq` = '$qq'  ";
                $this->dbClass->query($sql);
            }else{
                $sql = "INSERT INTO `dianq_friends_info`(`robot_id`, `qq`, `face`, `birthday`, `birthday_y`, `birthday_m`, `birthday_d`, `occupation`, `phone`, `allow`, `college`, `constel`, `blood`, `homepage`, `stat`, `vip_info`, `country`, `city`, `personal`, `nick`, `shengxiao`, `email`, `province`, `gender`, `mobile`,`uin`)
                                  VALUES ('{$this->robot_id}','{$qq}','{$result['face']}','{$result['birthday']['year']}{$result['birthday']['month']}{$result['birthday']['day']}','{$result['birthday']['year']}','{$result['birthday']['month']}','{$result['birthday']['day']}','{$result['occupation']}','{$result['phone']}','{$result['allow']}','{$result['college']}','{$result['constel']}','{$result['blood']}','{$result['homepage']}','{$result['stat']}','{$result['vip_info']}','{$result['country']}','{$result['city']}','{$result['personal']}','{$result['nick']}','{$result['shengxiao']}','{$result['email']}','{$result['province']}','{$result['gender']}','{$result['mobile']}','$uin')";
                $this->dbClass->query($sql);
            }

        }
        return true;
    }

    /**
     * 获取好友列表  返回UIN列表
     * @return mixed
     */
    public function getFriendUinList(){
        $result = $this->QQ->getFriendUinList();
        $result = json_decode($result,true);
        $result = $result['result'];
        return $result;
    }



}