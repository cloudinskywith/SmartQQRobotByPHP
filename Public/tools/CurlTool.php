<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/13
 * Time: 19:41
 */
class CurlUtil
{

    public    $cookies;
    public    $status;
    public    $referer;
    public    $agent;
    protected $timeOut;
    public    $link;
    public    $results;
    public    $param;
    public function __construct(){
        $this->cookies = array();
        $this->agent   = "Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2970.0 Mobile Safari/537.36";
        $this->referer = "";
        $this->timeOut = 60;
    }

    public function setTimeOut($time){
        $this->timeOut = $time;
    }

    public function setCookies($cookies){
        $this->cookies = $cookies;
    }

    public function fetch($url){
        $ch = curl_init($url);
        $this->link = $url;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,  "");
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
        curl_setopt($ch, CURLOPT_COOKIE, self::CookiesToString($this->cookies));
        $this->results = curl_exec($ch);
        preg_match_all('/Set-Cookie:\s(.+);/iU', $this->results, $array);
        $return_cookie = array();
        foreach ($array[1] as $cookie) {
            $cookie_split = explode("=", $cookie);
            if (count($cookie_split) < 1 || $cookie_split[1] == "") {
                continue;
            }
            array_push($return_cookie, $cookie);
        }
        foreach ($return_cookie as $value){
            $value = preg_replace("/;/","",$value);
            $item  = explode("=",$value);
            $this->cookies[$item[0]] = isset($item[1]) ? $item[1] : "";
        }
        preg_match_all('/Content-Length: (\w+)/', $this->results, $array);
        @$len = $array[1][0];
        $this->results = substr($this->results, -$len);
        curl_close($ch);
        return $this;

    }

    public function submit($url,$param){

        $ch = curl_init($url);
        $this->link = $url;
        $this->param = $param;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,  "");
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
        curl_setopt($ch, CURLOPT_COOKIE, self::CookiesToString($this->cookies));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $this->results = curl_exec($ch);
        preg_match_all('/Set-Cookie:\s(.+);/iU', $this->results, $array);
        $return_cookie = array();
        foreach ($array[1] as $cookie) {
            $cookie_split = explode("=", $cookie);
            if (count($cookie_split) < 1 || $cookie_split[1] == "") {
                continue;
            }
            array_push($return_cookie, $cookie);
        }
        foreach ($return_cookie as $value){
            $value = preg_replace("/;/","",$value);
            $item  = explode("=",$value);
            $this->cookies[$item[0]] = isset($item[1]) ? $item[1] : "";
        }
        preg_match_all('/Content-Length: (\w+)/', $this->results, $array);
        @$len = $array[1][0];
        $this->results = substr($this->results, -$len);
        curl_close($ch);
        return $this;
    }

    /**
     *
     *
     */
    public static function CookiesToArray($cookies){
        $cookie  = array();
        $cookies = explode(";",$cookies);
        foreach ($cookies as $value){
            @preg_replace('/;/','',$value,$value);
            $value = explode("=",$value);
            $cookie[$value[0]] = isset($value[1]) ? $value[1] : "";
        }
        return $cookie;
    }


    /**
     *
     *
     */
    public static function CookiesToString($cookies){
        $cookie = "";
        foreach ($cookies as $key => $value){
            $cookie .= $key . "=" . $value . ";";
        }
        return $cookie;
    }


}