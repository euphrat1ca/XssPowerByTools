<?php
header("Content-Type: text/html; charset=utf-8");
/**
 * user.php 用户管理
 * ----------------------------------------------------------------
 * OldCMS,site:http://www.oldcms.com
 */
if(!defined('IN_OLDCMS')) die('Access Denied');

if($user->userId<=0) ShowError('未登录或已超时',$url['login'],'重新登录');

$act=Val('act','GET');


switch($act){
	case 'invite':
		$inviteSum=5;
		$db=DBConnect();
		$tbInviteReg=$db->tbPrefix.'invite_reg';
		$invites=$db->Dataset("SELECT id,inviteKey as code,isWooyun,addTime FROM {$tbInviteReg} WHERE userId='".$user->userId."' AND isUsed=0 ORDER BY id DESC");
		
		$codesWooyun=array();
		$codesOther=array();
		foreach($invites as $k=>$v){
			if($v['isWooyun']==1){
				$codesWooyun[]=$v;
			}else{
				$codesOther[]=$v;
			}
		}

		$smarty=InitSmarty();
		$smarty->assign('codesWooyun',$codesWooyun);
		$smarty->assign('codesOther',$codesOther);
		$smarty->assign('do',$do);
		$smarty->assign('key',$key);
		$smarty->assign('show',$show);
		$smarty->assign('url',$url);
		$smarty->display('user_invite.html');
		exit;
		echo "可使用的邀请码(",count($invites),")：<br/>\n";
		if(count($invites)>0){
			foreach($invites as $key=>$value){
				echo $value['inviteKey'],"<br/>\n";
			}
		}
		echo "<br/><br/>\n",'<input type="button" onclick="location.href=\''.(URL_ROOT.'/xss.php?do=user&act=createinvite').'\'" value="生成新的邀请码" />';
		break;
	case 'createinvite':
		if($user->adminLevel<=0) ShowError('没有操作权限',URL_ROOT.'/xss.php?do=user&act=invite');
		$inviteSum=100;
		$isWooyun=Val('isWooyun','GET',1)==1 ? 1 : 0;
		//判断是否可以生成
		$db=DBConnect();
		$tbInviteReg=$db->tbPrefix.'invite_reg';
		$inviteCount=$db->FirstValue("SELECT COUNT(*) FROM {$tbInviteReg} WHERE userId='".$user->userId."' AND isUsed=0");
		if($inviteCount>=$inviteSum) ShowError('最多可生成'.$inviteSum.'条未使用的邀请链接',URL_ROOT.'/xss.php?do=user&act=invite');
		$inviteKey=md5('oc_'.$user->userId.time().rand(100000,999999));
		$sqlValue=array(
			'userId'=>$user->userId,
			'inviteKey'=>$inviteKey,
			'isWooyun'=>$isWooyun,
			'addTime'=>time()
		);
		if($db->AutoExecute($tbInviteReg,$sqlValue)){
			ShowSuccess('操作成功',URL_ROOT.'/xss.php?do=user&act=invite');
		}else{
			ShowError('操作失败',URL_ROOT.'/xss.php?do=user&act=invite');
		}
				break;
	//用户个人设置
	case 'seting':
		$db=DBConnect();
		$userInfo=$db->FirstRow("SELECT * FROM ".Tb('user')." WHERE id='".$user->userId."'");
		$emailstmp=$db->Dataset("SELECT ehost,euser FROM ".Tb('email')." ORDER BY id dESC");
		$phone=$userInfo['phone'];
		$email=$userInfo['email'];
		if($userInfo['message']=='')
		{
		$emsg='0';
		$pmsg='0';
		}
		else
		{
		$msg=explode("|",$userInfo['message']);
		$emsg=$msg[0];
		$pmsg=$msg[1];
		}
		
		if($emsg=='1')
		$input1="<input name='emsg' type='checkbox' class='checon' checked='checked'>";
		else
		$input1="<input name='emsg' type='checkbox' class='checon'>";
		
		if($pmsg=='1')
		$input2="<input name='pmsg' type='checkbox' class='checon' checked='checked'>";
		else
		$input2="<input name='pmsg' type='checkbox' class='checon'>";
		
		include('common.php');
		$smarty=InitSmarty();
		
		$smarty->assign('do',$do);
		$smarty->assign('show',$show);
		$smarty->assign('url',$url);
		$smarty->assign('projects',$projects);
		$smarty->assign('modules',$modules);
		
		$smarty->assign('input1',$input1);
		$smarty->assign('input2',$input2);
		$smarty->assign('emailstmp',$emailstmp);
		$smarty->assign('email',$email);
		$smarty->assign('phone',$phone);
		$smarty->assign('emsg',$emsg);
		$smarty->assign('pmsg',$pmsg);
		$smarty->display('user_seting.html');
		exit;
		break;
	case 'submit':
		$db=DBConnect();
		$phone=Val('phone','POST');
		$emsg=Val('emsg','POST');
		$email=Val('email','POST');
		$userpwd=Val('pwd','POST');
		$newpwd=Val('newpwd','POST');
		$newpwd1=Val('newpwd1','POST');
		if(empty($userpwd) || !preg_match('/^.{6,20}$/',$userpwd)) ShowError('旧密码禁止为空，或密码应为6-20位字符',URL_ROOT.'/xss.php?do=user&act=seting','重新填写');
		$userpwd = OCEncrypt($userpwd);
		$userInfo=$db->FirstRow("SELECT * FROM ".Tb('user')." WHERE id='".$user->userId."' and userPwd='{$userpwd}'");
		if(empty($userInfo) || $user->userId==14) ShowError('旧密码输入错误，请重新输入。',URL_ROOT.'/xss.php?do=user&act=seting','重新填写');
		if($emsg=='on') $emsg='1'; else $emsg='0';
		if($emsg){
			if(empty($email) || !preg_match('/^(\w+\.)*?\w+@(\w+\.)+\w+$/',$email)) ShowError('如果选中发送邮件，那您的邮箱格式不正确',URL_ROOT.'/xss.php?do=user&act=seting','重新填写');
			if($email == $userInfo['email']){
				$email = $userInfo['email'];
			}
		}
		if(!empty($newpwd) && !empty($newpwd1)){
			if($newpwd != $newpwd1) ShowError('您新密码两次输入的不一致，请从新输入。',URL_ROOT.'/xss.php?do=user&act=seting','重新填写');
			$userpwd = OCEncrypt($newpwd);
		}
		$pmsg=Val('pmsg','POST');
		if(!empty($phone) && !preg_match('/^(\d{11})$/',$phone)) ShowError('手机格式不正确',URL_ROOT.'/xss.php?do=user&act=seting','重新填写');//手机验证
		if($pmsg=='on') $pmsg='1'; else $pmsg='0';
		$db->Execute("UPDATE ".Tb('user')." SET userPwd='".$userpwd."',email='".$email."' ,phone='".$phone."',message='".$emsg."|".$pmsg."' WHERE id='".$user->userId."'");
		ShowSuccess('修改成功',URL_ROOT.'/xss.php?do=user&act=seting');
		exit;
		break;
	// URL黑名单设置
	case 'ipurlseting':
		$db=DBConnect();
		$ipurlseting=$db->Dataset("SELECT * FROM ".Tb('ipurlblack')." WHERE userId='".$user->userId."' ORDER BY id dESC");
		$allfilter=$db->Dataset("SELECT filterurl FROM ".Tb('allfilter')." ORDER BY id dESC");
		include('common.php');
		$smarty=InitSmarty();
		$smarty->assign('do',$do);
		$smarty->assign('show',$show);
		$smarty->assign('url',$url);
		$smarty->assign('projects',$projects);
		$smarty->assign('modules',$modules);
		$smarty->assign('allfilter',$allfilter);
		$smarty->assign('ipurlseting',$ipurlseting);
		$smarty->display('user_ipurlseting.html');
		exit;
		break;
	// URL黑名单添加
	case 'addipurl':
		$db=DBConnect();
		$ip=trim(Val('ip','POST'));
		$url=trim(Val('url','POST'));
		$moduleid=trim(Val('moduleid','POST',1));
		if(empty($moduleid)) ShowError('必须填写需要过滤的项目ID值！！！',URL_ROOT.'/xss.php?do=user&act=ipurlseting','重新填写');//项目验证
		if(empty($ip) && empty($url)) ShowError('禁止IP或URL都为空！！！',URL_ROOT.'/xss.php?do=user&act=ipurlseting','重新填写');//IP URL验证
		$tbInviteReg=$db->tbPrefix.'ipurlblack';
		$modidd=$db->FirstValue("SELECT COUNT(*) FROM ".Tb('project')." WHERE userId='".$user->userId."' AND id={$moduleid}");
		if($modidd<=0) ShowError('请填写你自己的正确的项目ID值！！！',URL_ROOT.'/xss.php?do=user&act=ipurlseting','重新填写');//IP URL验证
		$sqlValue=array(
			'moduleid'=>$moduleid,
			'userId'=>$user->userId,
			'ip'=>$ip,
			'url'=>$url,
			'addTime'=>time()
		);
		if($db->AutoExecute($tbInviteReg,$sqlValue)){
			ShowSuccess('操作成功',URL_ROOT.'/xss.php?do=user&act=ipurlseting');
		}else{
			ShowError('操作失败',URL_ROOT.'/xss.php?do=user&act=ipurlseting');
		}
		exit;
		break;
	// URL黑名单删除
	case 'delipurl':
		if(! $user->CheckToken(Val('token','GET'))) ShowError('操作失败');
		$id=Val('id','GET',1);
		$db=DBConnect();
		$project=$db->FirstRow("SELECT * FROM ".Tb('ipurlblack')." WHERE id='{$id}' AND userId='".$user->userId."'");
		if(empty($project)) ShowError('不存在此黑名单');
		$db->Execute("DELETE FROM ".Tb('ipurlblack')." WHERE id='{$id}'");
		ShowSuccess('操作成功',URL_ROOT.'/xss.php?do=user&act=ipurlseting');
	default:break;
}
?>