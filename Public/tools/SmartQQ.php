<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/13
 * Time: 16:44
 */

class SmartQQ
{
    /* 静态变量 */

    /**
     * 伪造发送请求时的来路地址
     * @var string
     */
    const REFERER_DEFAULT	= "http://qq.com";

    const REFERER_MEMBER	= "http://qun.qq.com/member.html";

    const REFERER_GETINFO	= "http://s.web2.qq.com/proxy.html?v=20130916001&callback=1&id=1";

    const REFERER_SEND		= "http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2";

    const REFERER_CHECK		= "https://ui.ptlogin2.qq.com/cgi-bin/login?daid=164&target=self&style=16&mibao_css=m_webqq&appid=501004106&enable_qlogin=0&no_verifyimg=1&s_url=http%3A%2F%2Fw.qq.com%2Fproxy.html&f_url=loginerroralert&strong_login=1&login_state=10&t=20131024001";

    /**
     * 公用COOKIE
     * @var string
     */
    const PUBLIC_COOKIE		= "pgv_pvid=9263172032;ts_uid=6928261796;RK=TFmCTsVc63;o_cookie=647891941;ptcz=436027493f2f70e022834cd6d0a44f75203f42cd8b268b6c0da05c226e99f240;logid=11471;ptisp=ctc;pt_clientip=fabe7f000001ecd8;pt_serverip=86b90abf0e2ff1dd;pgv_info=ssid%3Ds5262795216;";

    /**
     * 伪造用户发送请求的工具
     * @var string
     */
    const USERAGENT			= "Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2970.0 Mobile Safari/537.36";



    public $clientid = '53999199';
    public $cookies;
    public $ptwebqq;
    public $vfwebqq;
    public $psessionid;
    public $uin;
    public $skey;
    public $bkn;

    public $Curl;
    public $results;


    public $loginLink;
    public $loginStatus;
    public $loginType;



    public $errorInfo;
    public $rows;

    public function __construct($cookies = array() , $ptwebqq = "" , $vfwebqq = "" , $psessionid = "" ,$uin = "" , $skey = "" ,$bkn = "")
    {

        $this->cookies = array(
            'pgv_pvid'  =>'9263172032',
            'ts_uid'    =>'6928261796',
            'RK'        =>'TFmCTsVc63',
            'o_cookie'  =>'647891941',
            'ptcz'      =>'436027493f2f70e022834cd6d0a44f75203f42cd8b268b6c0da05c226e99f240',
            'logid'     =>'11471',
            'ptisp'     =>'ctc',
            'pt_clientip'=>'fabe7f000001ecd8',
            'pt_serverip'=>'86b90abf0e2ff1dd',
            'pgv_info'  =>'ssid%3Ds5262795216',
        );

        $this->Curl = new CurlUtil();
        $this->Curl->referer = self::REFERER_CHECK;

        if($cookies == null){
            $this->loginType = 0;
        }else{
            $this->cookies     = $cookies;
            $this->ptwebqq     = $ptwebqq;
            $this->vfwebqq     = $vfwebqq;
            $this->psessionid  = $psessionid;
            $this->uin         = $uin;
            $this->skey        = $skey;
            $this->bkn         = $bkn;
            $this->loginType   = 1;
        }
        $this->Curl->cookies   = $this->cookies;
    }


    public function getQRcode(){
        $rand = rand(100, 960);
        $this->Curl->fetch("https://ssl.ptlogin2.qq.com/ptqrshow?appid=501004106&e=0&l=M&s=5&d=72&v=4&t=0.{$rand}61858167465{$rand}");
        $this->results = $this->Curl->results;
        $this->cookies = $this->Curl->cookies;
        return $this->results;
    }

    public function scanQRcode($qrsig = ""){
        if($qrsig != ""){
            $this->Curl->cookies['qrsig'] = $qrsig;
        }
        $token = self::hash33($this->Curl->cookies['qrsig']);
        $action_index = rand(20000, 80000);
        $action_number = 0;
        $action = $action_index + $action_number * rand(1234, 1243);
        $this->Curl->fetch("https://ssl.ptlogin2.qq.com/ptqrlogin?ptqrtoken={$token}&webqq_type=10&remember_uin=1&login2qq=1&aid=501004106&u1=http%3A%2F%2Fw.qq.com%2Fproxy.html%3Flogin2qq%3D1%26webqq_type%3D10&ptredirect=0&ptlang=2052&daid=164&from_ui=1&pttype=1&dumy=&fp=loginerroralert&action=0-0-{$action}&mibao_css=m_webqq&t=undefined&g=1&js_type=0&js_ver=10194&login_sig=&pt_randsalt=0");
        $this->results = $this->Curl->results;
        preg_match("/ptuiCB\('(.*)','(.*)','(.*)','(.*)','(.*)',\s'(.*)'\);/U", $this->results, $array);
        if(!isset($array[1])){
            return StatusUtil::INIT;
        }
        $result['status'] = $array[1];
        $result['msg']    = $array[5];
        $result['link']   = $array[3];
        if($result['status'] == StatusUtil::QRCode_SUCCES){
            $this->ptwebqq = $this->Curl->cookies['ptwebqq'];
            $this->results = $this->Curl->fetch($result['link']);
            $this->cookies = $this->Curl->cookies;
            $this->skey = $this->cookies['skey'];
            $this->bkn  = $this->get_bkn($this->skey);
            $url = "http://s.web2.qq.com/api/getvfwebqq?ptwebqq={$this->ptwebqq}&clientid={$this->clientid}&psessionid=&t=" . time() . "496";
            $this->Curl->referer = self::REFERER_GETINFO;
            $this->Curl->fetch($url);
            $this->results = $this->Curl->results;
            $arr = @json_decode($this->results, true);
            $this->vfwebqq = $arr['result']['vfwebqq'];
            if(self::loginSecond()){
                $this->loginStatus = 1;
                return StatusUtil::ONLINE;
            }else{
                return StatusUtil::ACOUNT_ERROR;
            }
        }elseif ($result['status'] == StatusUtil::QRCode_LOADING) {
            $this->errorInfo = "正在验证二维码";
            return StatusUtil::QRCode_LOADING;
        } elseif ($result['status'] == StatusUtil::QRCode_ONLINE) {
            $this->errorInfo = "二维码未失效";
            return StatusUtil::QRCode_ONLINE;
        } elseif ($result['status'] == StatusUtil::QRCode_FAIL) {
            $this->errorInfo = "二维码已失效，请等待生成新二维码";
            return StatusUtil::QRCode_FAIL;
        } else {
            $this->errorInfo = "未知错误" . $this->results ;
            return StatusUtil::UNKNOWERROR;
        }

    }

    public function loginSecond($is_reconnection = false){
        $url = "http://d1.web2.qq.com/channel/login2";
        $param = "r=%7B%22ptwebqq%22%3A%22{$this->ptwebqq}%22%2C%22clientid%22%3A{$this->clientid}%2C%22psessionid%22%3A%22%22%2C%22status%22%3A%22online%22%7D";
        $this->Curl->referer = self::REFERER_SEND;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        preg_match_all("/\"vfwebqq\":\"(.*?)\"/",$this->results,$a);
        @$result['vfwebqq'] = $a[1][0];
        preg_match_all("/\"retcode\":\"(.*?)\"/",$this->results,$a);
        @$result['retcode'] = $a[1][0];
        preg_match_all("/\"psessionid\":\"(.*?)\"/",$this->results,$a);
        @$result['psessionid'] = $a[1][0];
        preg_match_all("/\"uin\":(.*?)\,/",$this->results,$a);
        @$result['uin'] = $a[1][0];
        preg_match_all("/\"errmsg\":\"(.*?)\"/",$this->results,$a);
        @$result['errmsg'] = $a[1][0];
        $this->errorInfo = $is_reconnection ? "机器人重连" : "第二次登录";
        if (array_key_exists('retcode', $result) && $result['retcode'] == 0) {
            $this->psessionid        = $result['psessionid'];
            $this->uin               = $result['uin'];
            $this->cookies           = $this->Curl->cookies;
            $this->rows['cookie']    = $this->cookies;
            $this->rows['ptwebqq']   = $this->ptwebqq;
            $this->rows['vfwebqq']   = $this->vfwebqq;
            $this->rows['psessionid']= $this->psessionid;
            $this->rows['clientid']  = $this->clientid;
            $this->rows['uin']       = $this->uin;
            $this->cookies = $this->Curl->cookies;
            /**
             *
             *
             *
             */

            self::getSelfInfo();
            self::getOnlineBuddies();
            self::getRecentList();

            return true;
        } elseif (array_key_exists('retcode', $result) && $result['retcode'] == 108) {
            $this->errorInfo = $this->errorInfo . '失败，请在安全中心检查QQ是否开启了登录限制';
            return false;
        } elseif (array_key_exists('retcode', $result)) {
            $this->errorInfo = $this->errorInfo . "失败，错误信息:" . $result['retcode'] . $result['errmsg'];
            return false;
        } else {
            $this->errorInfo =  $this->errorInfo . '失败，接口请求错误';
            return false;
        }
        return false;
    }


    public function getSelfInfo(){
        $url = "http://s.web2.qq.com/api/get_self_info2?t=" . time() . "292";
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->fetch($url);
        $this->results = $this->Curl->results;
        return $this->results;

    }


    public function getOnlineBuddies(){
        $url = "http://d1.web2.qq.com/channel/get_online_buddies2?vfwebqq={$this->vfwebqq}&clientid={$this->clientid}&psessionid={$this->psessionid}&t=" . time() . "277";
        $this->Curl->referer = self::REFERER_SEND;
        $this->Curl->fetch($url);
        $this->results = $this->Curl->results;
        preg_match_all("/({.*})/",$this->results,$result);
        @$this->results = $result[1][0];
        return $this->results;
    }

    public function getRecentList(){
        $url = "http://d1.web2.qq.com/channel/get_recent_list2";
        $param = "r=%7B%22vfwebqq%22%3A%22{$this->vfwebqq}%22%2C%22clientid%22%3A{$this->clientid}%2C%22psessionid%22%3A%22{$this->psessionid}%22%7D";
        $this->Curl->referer = self::REFERER_SEND;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        preg_match_all("/({.*})/",$this->results,$result);
        @$this->results = $result[1][0];
        return $this->results;
    }

    public function poll(){
        $url= "http://d1.web2.qq.com/channel/poll2";
        $param = "r=%7B%22ptwebqq%22%3A%22{$this->ptwebqq}%22%2C%22clientid%22%3A{$this->clientid}%2C%22psessionid%22%3A%22{$this->psessionid}%22%2C%22key%22%3A%22%22%7D";
        $this->Curl->referer = self::REFERER_SEND;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        preg_match_all("/({.*})/",$this->results,$result);
        @$this->results = $result[1][0];
        return $this->results;


    }

    /**
     * 处理机器人收到的消息，并进行分类处理
     * @param array $arr
     */
    public function dealMessage($poll) {
        foreach ($poll['result'] as $news) {
            if ($news['poll_type'] == 'message') {
                //好友消息
                return self::personalMsg($news);
            } elseif ($news['poll_type'] == 'group_message') {
                //群消息
                return self::groupMsg($news);
            } elseif ($news['poll_type'] == 'sys_g_msg') {
                //加群验证消息
                return self::sysGroupMsg($news);
            } elseif ($news['poll_type'] == 'sess_message') {
                //临时会话消息
            } elseif ($news['poll_type'] == 'discu_message') {
                //讨论组消息
                return self::didMsg($news);
            } elseif ($news['poll_type'] == 'buddies_status_change') {
                //好友状态改变提醒
            } else {
                return array(
                    'code'      =>-1,
                    'poll_type' =>$news['poll_type'],
                    'msg_type'  =>$news['value']['msg_type'],
                );
            }
        }
    }


    /**
     * 好友消息
     */
    public function personalMsg($news) {

        $from_uin = $news['value']['from_uin'];
        $contentArray = $news['value']['content'];
        $msg = @MsgUtil::dealMsgArray($contentArray);
        $send_uin = self::getFriendQQBySendUin($from_uin);
        return array(
            'code'      =>0,
            'type'      =>'personalMsg',
            'msg'       =>$msg,
            'from_uin'  =>$from_uin,
            'senderQQ'  =>$send_uin,
        );
    }


    /**
     * 群消息
     * @param unknown $news
     * @param unknown $rows
     */
    public function groupMsg($news) {

        $contentArray = $news['value']['content'];
        $msg = @MsgUtil::dealMsgArray($contentArray);
        $group_code = $news['value']['group_code'];
        $send_uin = $news['value']['send_uin'];
        $from_uin = $news['value']['from_uin'];
        $member_uin = self::getFriendQQBySendUin($send_uin);
        return array(
            'code'      =>0,
            'type'      =>'groupMsg',
            'msg'       =>$msg,
            'send_uin'  =>$send_uin,
            'from_uin'  =>$from_uin,
            'senderQQ'  =>$member_uin,
            'group_code'=>$group_code,

        );


    }


    /**
     * 讨论组消息
     * @param unknown $news
     * @param unknown $rows
     */
    public function didMsg($news) {

        $contentArray = $news['value']['content'];
        $msg = @MsgUtil::dealMsgArray($contentArray);
        $did = $news['value']['did'];
        $send_uin = $news['value']['send_uin'];
        $from_uin = $news['value']['from_uin'];
        $member_uin = self::getFriendQQBySendUin($send_uin);
        return array(
            'code'      =>0,
            'type'      =>'didMsg',
            'msg'       =>$msg,
            'send_uin'  =>$send_uin,
            'from_uin'  =>$from_uin,
            'senderQQ'  =>$member_uin,
            'did'       =>$did,
        );

    }


    /**
     * 加群验证
     */
    public function sysGroupMsg($news) {
        if ($news['value']['type'] == 'group_request_join') {
            $from_uin = $news['value']['from_uin'];
            $to_uin = $news['value']['to_uin'];
            $request_uin = $news['value']['request_uin'];
            $ver_msg = $news['value']['msg'];
            $type = $news['value']['type'];
            $gcode = $news['value']['gcode'];
            $t_gcode = $news['value']['t_gcode'];
            $nick = self::getUinVerProfile($request_uin, $type, $from_uin);
            $qunm = self::getQunVerProfile($gcode);
            $nick = !$nick ? "神秘人" : $nick;
            $qunm = !$qunm ? "本群" : $qunm;
            return array(
                'code'          =>0,
                'msg'           =>'',
                'nick'          =>$nick,
                'qunm'          =>$qunm,
                'to_uin'        =>$to_uin,
                'request_uin'   =>$request_uin,
                'from_uin'      =>$from_uin,
                'ver_msg'       =>$ver_msg,
                'type'          =>$type,
                'gcode'         =>$gcode,
                't_gcode'       =>$t_gcode,
            );
        }
    }



    /**
     * 发送消息
     * @param $rows
     * @param $from_uin  消息接收着/群uin
     * @param $reply
     * @param int $group_uin
     * @return array
     */
    public function sendMsg( $to_uin, $reply, $group = false) {
        $reply = MsgUtil::chuliMsg($reply);
        $msgid = rand(5000000, 5999999);
        $this->Curl->referer =  self::REFERER_SEND;

        if ($group == true ) {
                /**
                 * 发送群消息
                 */
                $url   = "http://d1.web2.qq.com/channel/send_qun_msg2";
                $param = "r=%7B%22group_uin%22%3A{$to_uin}%2C%22content%22%3A%22%5B%5C%22{$reply}%5C%22%2C%5B%5C%22font%5C%22%2C%7B%5C%22name%5C%22%3A%5C%22%E5%AE%8B%E4%BD%93%5C%22%2C%5C%22size%5C%22%3A10%2C%5C%22style%5C%22%3A%5B0%2C0%2C0%5D%2C%5C%22color%5C%22%3A%5C%22000000%5C%22%7D%5D%5D%22%2C%22face%22%3A522%2C%22clientid%22%3A" . $this->clientid . "%2C%22msg_id%22%3A" . $msgid . "%2C%22psessionid%22%3A%22" .  $this->psessionid  . "%22%7D";
        } else {
                /**
                 * 发送私人消息
                 */
                $url = "http://d1.web2.qq.com/channel/send_buddy_msg2";
                $param = "r=%7B%22to%22%3A{$to_uin}%2C%22content%22%3A%22%5B%5C%22{$reply}%5C%22%2C%5B%5C%22font%5C%22%2C%7B%5C%22name%5C%22%3A%5C%22%E5%AE%8B%E4%BD%93%5C%22%2C%5C%22size%5C%22%3A10%2C%5C%22style%5C%22%3A%5B0%2C0%2C0%5D%2C%5C%22color%5C%22%3A%5C%22000000%5C%22%7D%5D%5D%22%2C%22face%22%3A540%2C%22clientid%22%3A" . $this->clientid . "%2C%22msg_id%22%3A" . $msgid . "%2C%22psessionid%22%3A%22" . $this->psessionid . "%22%7D";

        }
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        preg_match_all("/({.*})/",$this->results,$result);
        @$this->results = $result[1][0];
        @$this->results = json_decode($this->results,true);
        if (@array_key_exists('errCode', $this->results) && $this->results['errCode'] == 0) {
            return array(
                'code'      =>0,
                'msg'       =>"回复成功:" . $reply,
                'to_uin' =>$to_uin,
            );
        } else {
            return array(
                'code'      =>-1,
                'msg'       =>"回复失败:" . $reply,
                'to_uin' =>$to_uin,
            );
        }
    }


    /**
     * 获取好友列表
     *  返回用户名和QQ号
     *
     * @return unknown
     */
    public function getFriendQQList() {
        $url = "http://qun.qq.com/cgi-bin/qun_mgr/get_friend_list";
        $param = "bkn=" . $this->bkn;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        $this->results = preg_replace("/&#92;/","",$this->results);
        $results = $this->results;
        return $results;
    }


    /**
     * @return mixed
     */
    public function getFriendUinList(){
        $url = "http://s.web2.qq.com/api/get_user_friends2";
        $hash = self::hashUin($this->uin,$this->ptwebqq);
        $param = "r=%7B%22vfwebqq%22%3A%22{$this->vfwebqq}%22%2C%22hash%22%3A%22{$hash}%22%7D";
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        return $this->results;
    }


    public function getGroupUinList(){
        $url = "http://s.web2.qq.com/api/get_group_name_list_mask2";
        $hash = self::hashUin($this->uin,$this->ptwebqq);
        $param = "r=%7B%22vfwebqq%22%3A%22{$this->vfwebqq}%22%2C%22hash%22%3A%22{$hash}%22%7D";
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        return $this->results;
    }


    public function getDiscusDidlist(){
        $url = "http://s.web2.qq.com/api/get_discus_list?clientid={$this->clientid}&psessionid={$this->psessionid}&vfwebqq={$this->vfwebqq}&t=" . time();
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->fetch($url);
        $this->results = $this->Curl->results;
        return $this->results;
    }


    public  function getSingleLongNick($send_uin) {
        $url = "http://s.web2.qq.com/api/get_single_long_nick2?tuin=" . $send_uin . "&vfwebqq=" . $this->vfwebqq . "&t=" . time() . "239";
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->fetch($url);
        $this->results = $this->Curl->results;
        @$this->results = json_decode($this->results,true)['result'][0]['lnick'];
        return $this->results;
    }


    public function getFriendQQBySendUin($send_uin) {
        $url = "http://s.web2.qq.com/api/get_friend_uin2?tuin=" . $send_uin . "&type=1&vfwebqq=" . $this->vfwebqq . "&t=" . time() . "239";
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->fetch($url);
        $this->results = $this->Curl->results;
        preg_match_all("/({.*})/",$this->results,$result);
        @$this->results = $result[1][0];
        @$this->results = json_decode($this->results,true);
        return ($this->results && isset($this->results['result']['account'])) ? $this->results['result']['account'] : false;
    }



    public function getFriendInfoByUin($send_uin) {
        $url = "http://s.web2.qq.com/api/get_friend_info2?tuin=" . $send_uin . "&vfwebqq=" . $this->vfwebqq . "&clientid=" . $this->clientid . "&psessionid=" . $this->psessionid . "&t=" . time() . "905";
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->fetch($url);
        $this->results = $this->Curl->results;
        preg_match_all("/({.*})/",$this->results,$result);
        @$this->results = $result[1][0];
//        @$this->results = json_decode($this->results,true)['result'];
//        @$this->results =  json_encode($this->results,JSON_UNESCAPED_UNICODE);
        return $this->results;
    }



    /**
     * 获取群列表
     * @param string $bkn
     * @param string $cookie
     * @return unknown
     */
    public function getGroupList() {
        $url = "http://qun.qq.com/cgi-bin/qun_mgr/get_group_list";
        $param = "bkn=" . $this->bkn;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        $this->results = preg_replace("/&#92;/","",$this->results);
        $results = $this->results;
        return $results;
    }


    /**
     * 查询群成员的信息
     * @param int $group_uin
     * @param int $start
     * @param int $end
     * @param int $sort
     * @param string $bkn
     * @param string $cookie
     * @param string $key
     * @return unknown
     */
    public function searchGroupMembers($group_uin, $start, $end, $sort, $key = "") {
        $url = "http://qun.qq.com/cgi-bin/qun_mgr/search_group_members";
        $key = $key == "" ? "" : ("&key=" . $key);
        $param = "gc=" . $group_uin . "&st=" . $start . "&end=" . $end . "&sort=" . $sort . $key . "&bkn=" . $this->bkn;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        $this->results = preg_replace("/&#92;/","",$this->results);
        $results = $this->results;
        return $results;
    }


    /**
     * 禁止某个人发言，也就是禁言
     * @param int $group_uin
     * @param int $member_uin
     * @param string $time
     * @param string $bkn
     * @param string $cookie
     * @return boolean
     */
    public function setMemberSpeech($group_uin, $QQ, $time) {
        $url = "http://qinfo.clt.qq.com/cgi-bin/qun_info/set_group_shutup";
        $param = "gc=" . $group_uin . "&shutup_list=%5B%7B%22uin%22%3A" . $QQ . "%2C%22t%22%3A" . $time . "%7D%5D&bkn=" . $this->bkn . "&src=qinfo_v2";
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        $this->results = preg_replace("/&#92;/","",$this->results);
        $results = $this->results;
        return $results;
    }

    /**
     * 修改群名片
     * @param int $group_uin
     * @param int $member_uin
     * @param string $name
     * @param string $bkn
     * @param string $cookie
     * @return boolean
     */
    public  function updateMemberCard($group_uin, $QQ, $name) {
        $url = "http://qun.qq.com/cgi-bin/qun_mgr/set_group_card";
        $param = "gc=" . $group_uin . "&u=" . $QQ . "&name=" . urlencode($name) . "&bkn=" . $this->bkn;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        $this->results = preg_replace("/&#92;/","",$this->results);
        $results = $this->results;
        return $results;
    }

    /**
     * 移除群成员，也就是踢人
     * @param int $group_uin
     * @param int $member_uin
     * @param string $bkn
     * @param string $cookie
     * @return boolean
     */
    public function removeMember($group_uin, $QQ) {
        $url = "http://qun.qq.com/cgi-bin/qun_mgr/delete_group_member";
        $param = "gc=" . $group_uin . "&ul=" . $QQ . "&flag=0&bkn=" . $this->bkn;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        $this->results = preg_replace("/&#92;/","",$this->results);
        $results = $this->results;
        return $results;
    }






    /**
     * 邀请别人加入QQ群，被邀请的人必须是自己的好友
     * @param int $group_uin 群号码
     * @param int $member_uin 被邀请的人
     * @param string $bkn 机器人的bkn
     * @param string $cookie 机器人的cookie
     */
    public function addGroupMember($group_uin, $QQ) {
        $url = "http://qun.qq.com/cgi-bin/qun_mgr/add_group_member";
        $param = "gc=" . $group_uin . "&ul=" . $QQ . "&bkn=" . $this->bkn;
        $this->Curl->referer = self::REFERER_MEMBER;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        $this->results = preg_replace("/&#92;/","",$this->results);
        $results = $this->results;
        return $results;
    }

    /**
     * 添加/取消群管理员，需要群主权限，也就是机器人必须是群主才行
     * @param int $group_uin 群号码
     * @param int $member_uin 需要添加/取消群管理员的QQ号
     * @param int $option 添加/取消选项，当值为1时是添加，为0时是取消
     * @param string $bkn 机器人的bkn
     * @param string $cookie 机器人的cookie
     * @return boolean
     */
    public function setGroupAdmin($group_uin, $QQ, $option) {
        $url = "http://qun.qq.com/cgi-bin/qun_mgr/set_group_admin";
        $param = "gc=" . $group_uin . "&ul=" . $QQ . "&op=" . $option . "&bkn=" . $this->bkn;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        $this->results = preg_replace("/&#92;/","",$this->results);
        $results = $this->results;
        return $results;
    }





    public  function getUinVerProfile($uin, $type, $request) {
        $url   = "http://s.web2.qq.com/api/get_stranger_info2";
        $param = "tuin=" . $uin . "&verifysession=&gid=0&code=" . $type . "-" . $request . "&vfwebqq=" . $this->vfwebqq . "&t=" . time() . "559";
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        preg_match_all("/({.*})/",$this->results,$result);
        @$this->results = $result[1][0];
        @$this->results = json_decode($this->results,true);
        return ($this->results && isset($this->results['result']['nick'])) ? $this->results['result']['nick'] : false;
    }

    public  function getQunVerProfile($gcode) {
        $url = "http://s.web2.qq.com/api/get_group_public_info2";
        $param = "gcode=" . $gcode . "&vfwebqq=" . $this->vfwebqq . "&t=" . time() . "559";
        $this->Curl->referer = self::REFERER_GETINFO;
        $this->Curl->submit($url,$param);
        $this->results = $this->Curl->results;
        preg_match_all("/({.*})/",$this->results,$result);
        @$this->results = $result[1][0];
        @$this->results = json_decode($this->results,true);
        return ($this->results && isset($this->results['result']['ginfo']['name'])) ? $this->results['result']['ginfo']['name'] : false;
    }


    public function setNewFriends($uin, $request, $gcode, $rows, $is_agree = true) {
        $op_type = $is_agree ? 2 : 3;
        $url1 = "http://d1.web2.qq.com/channel/op_group_join_req?group_uin=" . $uin . "&req_uin=" . $request . "&msg=&op_type=" . $op_type . "&clientid=" . $rows['clientid'] . "&psessionid=" . $rows['psessionid'] . "&t=" . time() . "559";
        $url2 = "http://s.web2.qq.com/api/get_group_info_ext2?gcode=" . $gcode . "&vfwebqq=" . $rows['vfwebqq'] . "&t=" . time() . "559";
        self::web_curl(self::getOtherRequest($url1, null, self::REFERER_SEND), $rows['cookie'], false);
        self::web_curl(self::getOtherRequest($url2, null, self::REFERER_GETINFO), $rows['cookie'], false);
    }






    public static function hash33($t) {
        for ( $e = 0, $i = 0, $n = strlen($t); $n > $i; ++$i){
            $e += ($e << 5) + self::charCodeAt($t,$i);
        }
        return 2147483647 & $e;
    }

    public static function charCodeAt($str, $index){
        $char = mb_substr($str, $index, 1, 'UTF-8');
        if (mb_check_encoding($char, 'UTF-8'))
        {
            $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
            return hexdec(bin2hex($ret));
        }
        else
        {
            return null;
        }
    }



    public static function hashUin($uin,$ptwebqq){
        $n = array(0,0,0,0);
        for($i = 0;$i < strlen($ptwebqq); $i++){
            $n[$i % 4] ^= self::charCodeAt($ptwebqq,$i);
        }
        $u = ['EC','OK'];
        $v = [];
        $v[0] = (((floatval($uin) >> 24) & 255) ^ self::charCodeAt($u[0],0));
        $v[1] = (((floatval($uin) >> 16) & 255) ^ self::charCodeAt($u[0],1));
        $v[2] = (((floatval($uin) >> 8) & 255)  ^ self::charCodeAt($u[1],0));
        $v[3] = ((floatval($uin) & 255) ^ self::charCodeAt($u[1],1));
        $result = array();
        for($i=0; $i<8; $i++){
            if ($i%2 == 0)
                $result[$i] = $n[$i>>1];
            else
                $result[$i] = $v[$i>>1];
        }

        return self::byte2hex($result);
    }


    public static function byte2hex($bytes){//bytes array
        $hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
        $buf = "";
        for ($i=0;$i<count($bytes);$i++){
            $buf .= $hex[($bytes[$i]>>4) & 15];
            $buf .= ($hex[$bytes[$i] & 15]);
        }
        return $buf;
    }








    /**
     * 根据获取的cookie提取skey
     * @param string $cookie
     * @return string
     */
    public static function get_skey($cookie) {
        return preg_replace("/^(.*);skey=(.{0,12})(;.*)$/Uis", "\\2", $cookie);
    }


    /**
     * 根据skey计算出bkn的值
     * @param string $skey
     * @return int
     */
    public static function get_bkn($skey) {
        $hash = 5381;
        for($i=0; $i<strlen($skey); ++$i){
            $hash += ($hash << 5) + self::utf8_unicode($skey[$i]);
        }
        return $hash & 0x7fffffff;
    }

    /**
     * 用于bkn的计算
     * @param number|string $c
     * @return number|boolean
     */
    public static function utf8_unicode($c) {
        switch(strlen($c)) {
            case 1:
                return ord($c);
            case 2:
                $n = (ord($c[0]) & 0x3f) << 6;
                $n += ord($c[1]) & 0x3f;
                return $n;
            case 3:
                $n = (ord($c[0]) & 0x1f) << 12;
                $n += (ord($c[1]) & 0x3f) << 6;
                $n += ord($c[2]) & 0x3f;
                return $n;
            case 4:
                $n = (ord($c[0]) & 0x0f) << 18;
                $n += (ord($c[1]) & 0x3f) << 12;
                $n += (ord($c[2]) & 0x3f) << 6;
                $n += ord($c[3]) & 0x3f;
                return $n;
        }
    }

}