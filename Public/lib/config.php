<?php
/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/2/13
 * Time: 15:19
 */

/**
 *  Charset  UTF-8
 */
header('Content-type:text/html;charset=utf-8');

/**
 *  Access-Control-Allow-Origin
 */
//header("Access-Control-Allow-Origin: *");

/**
 *  Sky31 API Verification
 *
 *  Role
 *  Hash
 */
define("Role","YiBao");
define("Hash","30e9719f4fd15c3f01eb87a6770ff60d");


/**
 *  Tuling Api Verification
 */
define("APIkey","d400e0967d44447eb11afd8ea5ea2b11");
define("secret","8a08a86c4ae39f34");


/**
 * Database
 * dbHost
 * dbUser
 * dbPassword
 * dbTable
 * dbport
 *
 */
define('dbHost', '127.0.0.1');
define("dbUser","root");
define("dbPassword","wxhxa.666Z");
define("dbTable","DianQ");
define('dbport', '3306');

/**
 *  Api Database
 */
//define("dbApiUser","api");
//define("dbApiPassword","YaKRGZ2mZZMd5wBS");
//define("dbApiTable","api_cp");



/**
 *  PASSWORD TOKEN
 */

//Encode
define('ENCODE_CIPHER', MCRYPT_RIJNDAEL_128);
define('ENCODE_MODE', MCRYPT_MODE_ECB);
define('ENCODE_KEY', '93c5680f1d6f3c34036092204ef58b9d');
