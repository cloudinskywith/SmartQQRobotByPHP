<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/14
 * Time: 21:02
 */
class RobotGroup
{

    public $robot_id;
    public $QQ;
    public $dbClass;
    public function __construct($robot_id,$dbClass,$QQ){
        $this->robot_id = $robot_id;
        $this->QQ       = $QQ;
        $this->dbClass  = $dbClass;
    }

    public function updateGroupInfo(){
        set_time_limit(0);
        $response = self::getGroupGCList();
        foreach ($response as $item){
            $result = $this->QQ->searchGroupMembers($item['gc'],0, 10000, 0, $key = "");
            $result = json_decode($result,true);
            $sql = "SELECT * FROM `dianq_groups_list` WHERE `robot_id` = '$this->robot_id' AND `gc` = '{$item['gc']}'  ";
            $rs  = $this->dbClass->query($sql);
            if(!isset($result['levelname'])){
                $result['levelname'] = array();
            }
            $levelname = json_encode($result['levelname'],JSON_UNESCAPED_UNICODE);
            if(mysqli_num_rows($rs) <= 0){
                $sql = "INSERT INTO `dianq_groups_list`(`robot_id`, `gc`, `gn`, `owner`, `adm_max`, `adm_num`, `count`, `levelname`, `max_count`, `search_count`, `svr_time`, `vecsize`)
                        VALUES ('{$this->robot_id}','{$item['gc']}','{$item['gn']}','{$item['owner']}','{$result['adm_max']}','{$result['adm_num']}','{$result['count']}','{$levelname}','{$result['max_count']}','{$result['search_count']}','{$result['svr_time']}','{$result['vecsize']}')";
            }else{
                $sql = "UPDATE `dianq_groups_list` SET `gn`='{$item['gn']}',`owner`='{$item['owner']}',`adm_max`='{$result['adm_max']}',`adm_num`='{$result['adm_num']}',`count`='{$result['count']}',`levelname`='{$levelname}',`max_count`='{$result['max_count']}',`search_count`='{$result['search_count']}',`svr_time`='{$result['svr_time']}',`vecsize`='{$result['vecsize']}'
                        WHERE `robot_id` = '$this->robot_id' AND `gc` = '{$item['gc']}' ";
            }
            $this->dbClass->query($sql);
            $mems = $result['mems'];
            foreach ($mems as $value){
                $sql = "SELECT * FROM `dianq_groups_users` WHERE `gc`='{$item['gc']}' AND `robot_id` = '$this->robot_id' AND `qq` = '{$value['uin']}'  ";
                $rs  = $this->dbClass->query($sql);
                if(mysqli_num_rows($rs) <= 0){
                    $sql = "INSERT INTO `dianq_groups_users`(`gn` , `gc`, `robot_id`, `card`, `flag`, `g`, `join_time`, `last_speak_time`, `level`, `point`, `nick`, `qage`, `role`, `tags`, `qq`) 
                                    VALUES ('{$item['gn']}','{$item['gc']}','{$this->robot_id}','{$value['card']}','{$value['flag']}','{$value['g']}','{$value['join_time']}','{$value['last_speak_time']}','{$value['lv']['level']}','{$value['lv']['point']}','{$value['nick']}','{$value['qage']}','{$value['role']}','{$value['tags']}','{$value['uin']}')";
                }else{
                    $sql = "UPDATE `dianq_groups_users` SET `gc`='{$item['gc']}', `card`='{$value['card']}',`flag`='{$value['flag']}',`g`='{$value['g']}',`join_time`='{$value['join_time']}',`last_speak_time`='{$value['last_speak_time']}',`level`='{$value['lv']['level']}',`point`='{$value['lv']['point']}',`nick`='{$value['nick']}',`qage`='{$value['qage']}',`role`='{$value['role']}',`tags`='{$value['tags']}',`qq`='{$value['uin']}'
                            WHERE `gc`='{$item['gc']}' AND `robot_id` = '$this->robot_id' AND `qq` = '{$value['uin']}'";
                }
                $this->dbClass->query($sql);
            }

        }
        return true;
    }

    public function getGroupGCList(){
        $result = $this->QQ->getGroupList();
        $result = json_decode($result,true);
        $array = array();
        @$re = $result['join'];
        foreach ($re as $item){
            array_push($array,$item);
        }
        @$re = $result['manage'];
        foreach ($re as $item){
            array_push($array,$item);
        }
        return $array;
    }

    public function getGroupInfo($gc){

    }

    /**
     *
     * 禁言
     * @param $group_uin
     * @param $QQ
     * @param $time
     * @return mixed
     *
     */
    public function banMemberSpeech($group_uin, $QQ, $time){
        return $this->QQ->setMemberSpeech($group_uin, $QQ, $time);
    }

    /**
     *
     * 全体禁言
     * @param $group_uin
     * @param $QQ
     * @param $time
     * @return mixed
     *
     */
    public function banMemberSpeechAll($group_uin,$time){
        $sql = "SELECT * FROM `dianq_groups_users` WHERE `robot_id` = '$this->robot_id' AND `gc`= '$group_uin'";
        $rs  = $this->dbClass->query($sql);
        if(mysqli_num_rows($rs) <= 0){

        }else{
            while ($r = $this->dbClass->getone($rs)){
                self::banMemberSpeech($group_uin, $r['qq'], $time);
            }
        }
        return true;
    }

    /**
     * 踢人
     * @param $group_uin
     * @param $QQ
     * @return mixed
     */
    public function removeMember($group_uin, $QQ){
        return $this->QQ->removeMember($group_uin, $QQ);
    }

    /**
     *  邀请别人加入QQ群，被邀请的人必须是自己的好友
     * @param $group_uin
     * @param $QQ
     * @return mixed
     */
    public function addMember($group_uin, $QQ){
        return $this->QQ->addGroupMember($group_uin, $QQ);
    }

    /**
     * 添加/取消群管理员，需要群主权限，也就是机器人必须是群主才行
     * @param $group_uin
     * @param $QQ
     * @param $option
     * @return mixed
     */
    public function setGroupAdmin($group_uin, $QQ, $option){
        return $this->QQ->setGroupAdmin($group_uin, $QQ ,$option);
    }


}