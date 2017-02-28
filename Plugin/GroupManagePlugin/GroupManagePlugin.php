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
        if($type != "groupMsg" || !$this->isManager($userId)  ){
            return ;
        }else{
            /**
             * 判断是否初始化群资料  获取gcode
             */


            $groupCode = $this->RobotGroup->getGroupCode($this->getMsg['from_uin']);
            if($groupCode == null){
                $this->setReplyMsg("请初始化群资料后再使用群相关功能~~~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }

            /**
             * 更新群成员
             */

            $pro    = explode("更新群成员",$msg);
            if(count($pro) >= 2){
                $this->RobotGroup->updateGroupInfo();
                $this->setReplyMsg("群成员信息更新成功~~~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }
            /**
             * 禁言
             */

            $pro    = explode("禁言",$msg);
            if(count($pro) >= 2 && strpos($msg,"@")){
                $pro    = explode("禁言#",$msg);
                if(count($pro) >= 2){
                    preg_match_all('/#(\w*)@/',$msg,$min);
                    $min = isset($min[1]) ? $min[1][0] : "";
                }else{
                    $min = 60*24*30;
                }

                if($min == ""){
                    $this->setReplyMsg("请确认格式是否正确~!\n禁言@xxxxx \n禁言@1353693508\n禁言#10@xxxxx \n禁言#10@1353693508",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                preg_match_all('/@(.*)/',$msg,$nick);
                $nick = isset($nick[1]) ? $nick[1][0] : "";
                if($nick == ""){
                    $this->setReplyMsg("请确认格式是否正确~!\n禁言@xxxxx \n禁言@1353693508\n禁言#10@xxxxx \n禁言#10@1353693508",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                preg_match_all('/@(\w*)/',$msg,$QQ);
                $QQ = isset($QQ[1]) ? $QQ[1][0] : "";
                if($QQ == ""){
                    $QQ  = $this->RobotGroup->getInfoByNick($groupCode,$nick);
                }
                if($QQ == null ){
                    $this->setReplyMsg("禁言失败 不存在此昵称{$nick}QQ  请更新群消息或使用禁言@1353693508方式禁言~",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                $this->RobotGroup->banMemberSpeech($groupCode,$QQ,$min*60);
                $this->setReplyMsg("已禁言QQ:{$QQ}    {$min}分钟~~ \n请不要违反群规哦~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }


            /**
             * 解禁
             */

            $pro    = explode("解禁",$msg);
            if(count($pro) >= 2 && strpos($msg,"@")){
                $pro    = explode("解禁#",$msg);
                if(count($pro) >= 2){
                    preg_match_all('/#(\w*)@/',$msg,$min);
                    $min = isset($min[1]) ? $min[1][0] : "";
                }else{
                    $min = 60*24*30;
                }

                if($min == ""){
                    $this->setReplyMsg("请确认格式是否正确~!\n解禁@xxxxx \n解禁@1353693508\n",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                preg_match_all('/@(.*)/',$msg,$nick);
                $nick = isset($nick[1]) ? $nick[1][0] : "";
                if($nick == ""){
                    $this->setReplyMsg("请确认格式是否正确~!\n解禁@xxxxx \n解禁@1353693508\n",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                preg_match_all('/@(\w*)/',$msg,$QQ);
                $QQ = isset($QQ[1]) ? $QQ[1][0] : "";
                if($QQ == ""){
                    $QQ  = $this->RobotGroup->getInfoByNick($groupCode,$nick);
                }
                if($QQ == null ){
                    $this->setReplyMsg("解禁失败 不存在此昵称{$nick}QQ  请更新群消息或使用解禁@1353693508方式解禁~",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                $this->RobotGroup->banMemberSpeech($groupCode,$QQ,0);
                $this->setReplyMsg("已解禁QQ:{$QQ}  ~~ \n请不要违反群规哦~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }


            /**
             * 全体禁言
             */

            $pro    = explode("全体禁言",$msg);
            if(count($pro) >= 2){
                $pro    = explode("全体禁言#",$msg);
                if(count($pro) >= 2){
                    $min = $pro[1];
                }else{
                    $min = 60*24*30;
                }
                $array = array(
                    $userId
                );
                $this->RobotGroup->banMemberSpeechAll($groupCode,$min*60,$array);
                $this->setReplyMsg("已全体禁言{$min}分钟~~ \n 大家注意 貌似要有大事件发生~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }


            /**
             * 全体解禁
             */

            $pro    = explode("全体解禁",$msg);
            if(count($pro) >= 2){
                    if($this->RobotGroup->banMemberSpeechAll($groupCode,0)){
                        $this->setReplyMsg("全体成员解除禁言啦~  \n 大家又可以愉快的水群了~",$this->getMsg['from_uin'],'group');
                        $this->setMsgCount(1);
                        return ;
                    }else{
                        $this->setReplyMsg("解除失败~",$this->getMsg['from_uin'],'group');
                        $this->setMsgCount(1);
                        return ;
                    }
            }


            /**
             * 踢人
             */

            $pro    = explode("踢人",$msg);
            if(count($pro) >= 2 && strpos($msg,"@")){

                preg_match_all('/@(.*)/',$msg,$nick);
                $nick = isset($nick[1]) ? $nick[1][0] : "";
                if($nick == ""){
                    $this->setReplyMsg("请确认格式是否正确~!\n踢人@xxxxx \n踢人@1353693508\n",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                preg_match_all('/@(\w*)/',$msg,$QQ);
                $QQ = isset($QQ[1]) ? $QQ[1][0] : "";
                if($QQ == ""){
                    $QQ  = $this->RobotGroup->getInfoByNick($groupCode,$nick);
                }
                if($QQ == null ){
                    $this->setReplyMsg("踢人失败 不存在此昵称{$nick}QQ  请更新群消息或使用踢人@1353693508方式踢人~",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                $this->RobotGroup->removeMember($groupCode,$QQ);
                $this->setReplyMsg("已将QQ:{$QQ}移除本群  ~~ \n请不要违反群规哦~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }


            /**
             * 加人
             */

            $pro    = explode("加人",$msg);
            if(count($pro) >= 2 && strpos($msg,"@")){

                preg_match_all('/@(\w*)/',$msg,$QQ);
                $QQ = isset($QQ[1]) ? $QQ[1][0] : "";
                if($QQ == null ){
                    $this->setReplyMsg("邀请失败  \n请确定格式为邀请@1353693508",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                $this->RobotGroup->addMember($groupCode,$QQ);
                $this->setReplyMsg("已将加入邀请发送之QQ:{$QQ}  ~~ \n貌似要来新的小伙伴了~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }




            /**
             * 设置管理员
             */

            $pro    = explode("设置管理员",$msg);
            if(count($pro) >= 2 && strpos($msg,"@")){

                preg_match_all('/@(.*)/',$msg,$nick);
                $nick = isset($nick[1]) ? $nick[1][0] : "";
                if($nick == ""){
                    $this->setReplyMsg("请确认格式是否正确~!\n设置管理员@xxxxx \n设置管理员@1353693508\n",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                preg_match_all('/@(\w*)/',$msg,$QQ);
                $QQ = isset($QQ[1]) ? $QQ[1][0] : "";
                if($QQ == ""){
                    $QQ  = $this->RobotGroup->getInfoByNick($groupCode,$nick);
                }
                if($QQ == null ){
                    $this->setReplyMsg("设置管理员失败 不存在此昵称{$nick}QQ  请更新群消息或使用设置管理员@1353693508方式设置管理员~",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                $this->RobotGroup->setGroupAdmin($groupCode,$QQ,1);
                $this->setReplyMsg("已将QQ:{$QQ}设置管理员  ~~ \n请不要违反群规哦~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }


            /**
             * 取消管理员
             */

            $pro    = explode("取消管理员",$msg);
            if(count($pro) >= 2 && strpos($msg,"@")){

                preg_match_all('/@(.*)/',$msg,$nick);
                $nick = isset($nick[1]) ? $nick[1][0] : "";
                if($nick == ""){
                    $this->setReplyMsg("请确认格式是否正确~!\n取消管理员@xxxxx \n取消管理员@1353693508\n",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                preg_match_all('/@(\w*)/',$msg,$QQ);
                $QQ = isset($QQ[1]) ? $QQ[1][0] : "";
                if($QQ == ""){
                    $QQ  = $this->RobotGroup->getInfoByNick($groupCode,$nick);
                }
                if($QQ == null ){
                    $this->setReplyMsg("取消管理员失败 不存在此昵称{$nick}QQ  请更新群消息或使用取消管理员@1353693508方式取消管理员~",$this->getMsg['from_uin'],'group');
                    $this->setMsgCount(1);
                    return ;
                }
                $this->RobotGroup->setGroupAdmin($groupCode,$QQ,0);
                $this->setReplyMsg("已将QQ:{$QQ}取消管理员  ~~ \n请不要违反群规哦~",$this->getMsg['from_uin'],'group');
                $this->setMsgCount(1);
                return ;
            }



        }


    }


}