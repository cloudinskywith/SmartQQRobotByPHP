<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/16
 * Time: 19:02
 */
class GroupManagePlugin extends RobotPlugin
{
    public function Start(){
        $msg    = $this->getMsg['msg'];
        $type   = $this->getMsg['type'];
        $userId = $this->getMsg['senderQQ'];
        if($type != "groupMsg" && !$this->isManager($userId)){
            return ;
        }else{

            $groupCode = $this->RobotGroup->getGroupCode($this->getMsg['from_uin']);
            if($groupCode == null){
                $this->setReplyMsg("请初始化群资料后再使用群相关功能~~~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }


            $pro    = explode("全体禁言#",$msg);
            if(count($pro) >= 2){


            }


            $pro    = explode("全体解禁",$msg);
            if(count($pro) >= 2){
                    if($this->RobotGroup->banMemberSpeechAll($groupCode,0)){
                        $this->setReplyMsg("解除成功~",$this->getMsg['from_uin'],'group');
                    }else{
                        $this->setReplyMsg("解除失败~",$this->getMsg['from_uin'],'group');
                    }
                    $this->setMsgCount(1);
            }



        }


    }


}