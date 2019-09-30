<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */

// [ 应用入口文件 ]222
// 应用入口文件
if (extension_loaded('zlib')){
    ob_end_clean();
    ob_start('ob_gzhandler');
}
 
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.5.0','<') || version_compare(PHP_VERSION,'7.1.0','>'))
{
    header("Content-type: text/html; charset=utf-8");  
    die('PHP 版本必须 5.5 至 7.0 !');
}
//error_reporting(E_ALL ^ E_NOTICE);//显示除去 E_NOTICE 之外的所有错误信息
error_reporting(E_ERROR | E_WARNING | E_PARSE);//报告运行时错误

//检测是否已安装TPshop系统
if(file_exists("./install/") && !file_exists("./install/install.lock")){
	if($_SERVER['PHP_SELF'] != '/index.php'){
		header("Content-type: text/html; charset=utf-8");         
		exit("请在域名根目录下安装,如:<br/> www.xxx.com/index.php 正确 <br/>  www.xxx.com/www/index.php 错误,域名后面不能圈套目录, 但项目没有根目录存放限制,可以放在任意目录,apache虚拟主机配置一下即可");
	}  
	header('Location:/install/index.php');
	exit(); 
}

require __DIR__ . '/saas.php';

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
//define('APP_DEBUG',false); debug tp5 里面已经改为 config.php 里面
// 定义应用目录
//define('APP_PATH','./Application/');
//  定义插件目录
define('PLUGIN_PATH', __DIR__ . '/plugins/');
defined('UPLOAD_PATH') or define('UPLOAD_PATH','public/upload/'); // 编辑器图片上传路径
define('TPSHOP_CACHE_TIME',1); // TPshop 缓存时间  31104000
$http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
define('SITE_URL',$http.'://'.$_SERVER['HTTP_HOST']); // 网站域名
//define('HTML_PATH','./Application/Runtime/Html/'); //静态缓存文件目录，HTML_PATH可任意设置，此处设为当前项目下新建的html目录
define('INSTALL_DATE',1463741583);
define('SERIALNUMBER','20160520065303oCWIoa');
// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
// 定义时间
define('NOW_TIME',$_SERVER['REQUEST_TIME']);

if($_SERVER['REQUEST_URI'] == '/index.php/Home/Payment/notifyUrl/pay_code/weixin'){
	$url = SITE_URL . $_SERVER['REQUEST_URI'] . '/id/1';
	$str = trim(file_get_contents('php://input'));
	echo http_request($url,$str);
	exit;
}
//$ss = trim(file_get_contents('php://input'));
//if($ss){
	//aaa_log($ss, 'index aaa');
//}

// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';

function aaa_log($msg,$txt=''){

	if (is_array($msg)) {
		$msg = json_encode($msg, 256);
	}
	$dir = './runtime/temp/a.html';
	if($msg == 1006){
		file_put_contents($dir,'');
		return;
	}
	file_put_contents($dir, date("Y:m:d H:i:s ") . $msg .' '.$txt. PHP_EOL, FILE_APPEND);
}
function http_request($url, $data = null)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}