<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/16
 * Time: 2:28
 */
define('MUSICCOUNT',2);
class MusicPlugin extends RobotPlugin
{

    public function Start(){
        $msg    = $this->getMsg['msg'];
        $type   = $this->getMsg['type'];
        $userId = $this->getMsg['senderQQ'];
        $pro    = explode("网易音乐#",$msg);
        if(count($pro) >= 2){
            $name   = $pro[1];
            $url    = "http://www.kilingzhang.com/Music/api/search.php?name=$name&source=163";
            $json   = file_get_contents($url);
            $data   = json_decode($json,true);
            $replyMsg    = "";
            if($data['code'] == 0){
                $data = $data['data'];
                $i = 0;
                foreach ($data as $item){
                    $url  = "http://www.kilingzhang.com/Music/api/url.php?id={$item['song_id']}&source=163";
                    $json   = file_get_contents($url);
                    $data   = json_decode($json,true);
                    $url    = $data['data']['url'];
                    $replyMsg .= "歌名：{$item['song_name']}  ({$item['singer_name']})\n";
                    $replyMsg .= "下载地址:\n";
                    $replyMsg .= $url . "\n";
                    $i ++;
                    if($i >= MUSICCOUNT){
                        break;
                    }
                }
            }
        }
        $pro    = explode("QQ音乐#",$msg);
        if(count($pro) >= 2){
            $name   = $pro[1];
            $url    = "http://www.kilingzhang.com/Music/api/search.php?name=$name&source=Tencent";
            $json   = file_get_contents($url);
            $data   = json_decode($json,true);
            $replyMsg    = "";
            if($data['code'] == 0){
                $data = $data['data'];
                $i = 0;
                foreach ($data as $item){
                    $url  = "http://www.kilingzhang.com/Music/api/url.php?id={$item['song_id']}&source=Tencent";
                    $json   = file_get_contents($url);
                    $data   = json_decode($json,true);
                    $url    = $data['data']['url']['320mp3'];
                    $dwz  = "http://dwz.wailian.work/api.php?url=$url&site=sina";
                    $url  = file_get_contents($dwz);
                    $url  = json_decode($url,true);
                    var_dump($url);
                    $url  = $url['data']['short_url'];
                    $replyMsg .= "歌名：{$item['song_name']}  ({$item['singer_name']})\n";
                    $replyMsg .= "下载地址:\n";
                    $replyMsg .= $url . "\n";
                    $i ++;
                    if($i >= MUSICCOUNT){
                        break;
                    }
                }
            }
        }
        $pro    = explode("虾米音乐#",$msg);
        if(count($pro) >= 2){
            $name   = $pro[1];
            $replyMsg    = "";
            if($data['code'] == 0){
                $url    = "http://www.kilingzhang.com/Music/api/search.php?name=$name&source=xiami";
                $json   = file_get_contents($url);
                $data   = json_decode($json,true);
                $data = $data['data'];
                $i = 0;
                foreach ($data as $item){
                    $url    = $item['url'];
                    $replyMsg .= "歌名：{$item['song_name']}  ({$item['singer_name']})\n";
                    $replyMsg .= "下载地址:\n";
                    $replyMsg .= $url . "\n";
                    $i ++;
                    if($i >= MUSICCOUNT){
                        break;
                    }
                }
            }
        }

        switch ($type){
            case "personalMsg":
                $this->setMsgCount(1);
                $replyMsg = "亲爱的您搜索的{$name}\n已为您搜索，结果如下:\n". $replyMsg ."       --学长大大";
                $this->setReplyMsg($replyMsg,$this->getMsg['from_uin'],"personal");
                break;
            case "groupMsg":
                $this->setMsgCount(2);
                $replyMsg = "亲爱的您搜索的{$name}\n已为您搜索，结果如下:\n".  $replyMsg ."       --学长大大";
                $this->setReplyMsg($replyMsg,$this->getMsg['send_uin'],"personal");
                $this->setReplyMsg($replyMsg,$this->getMsg['from_uin'],"group");
                break;
            case "sysGroupMsg":


                break;
            case "didMsg":


                break;
        }

    }

}