<?
class Content
{
	public $HTML = array(
		'login' => "",
		'topmenu' => "",
		'solmenu' => "do-not-show",
		'content' => ""
	);

	function GenerateLogin()
	{
		global $User;
		
		$this->HTML['login'] = '
		<form name="loginForm" id="loginForm" method="post" action="index.php">
		Kullanıcı Adı: <input type="text" name="loginUsername" maxlength="25" size="18" />
		<div class="saginblock">Şifre: <input type="password" name="loginPassword" maxlength="25" size="18" /></div>
		<table width="154" border="0" cellspacing="0" cellpadding="0">
		 <tr>
		  <td align="left" width="75"><div class="bmask"><button class="sagbutton2" type="submit" />Giriş Yap</button></div></td>
		  <td width="4">&nbsp;</td> 
		  <td align="right" width="75"><button class="sagbutton2" type="button" onclick="window.location = \'index.php?page=register\';">Üye Ol</button></td>
		 </tr>
		</table>
		<table width="154" border="0" cellspacing="0" cellpadding="0">
		 <tr>
		  <td align="center" width="154"><button class="sagbutton1" type="button" onclick="window.location = \'?page=forgot\';">Şifremi Unuttum</button></td>
		 </tr>
		</table>
		</form>';
		switch($User->authState)
		{
			case "false":  $this->HTML['login'] .= '<br />Kullanıcı adı ya da şifre yanlış.';  break;
			case "inactive":  $this->HTML['login'] .= '<br />Üyelik etkinleştirilmemiş.';  break;
			
			case "true":
				$this->HTML['login'] = $User->fullname.'<br/>';
				switch($User->adminstate)
				{
					case '3':
					$this->HTML['login'] .= '<i>-- Baş Admin --</i><br />'
					.'<button class="sagbutton1" type="button" onclick="window.location = \'index.php?page=editsite\';">Siteyi Yönet</button>'
					.'<button class="sagbutton1" type="button" onclick="window.location = \'index.php?page=edituser\';">Kullanıcıları Yönet</button>'
					.'<button class="sagbutton1" type="button" onclick="window.location = \'index.php?page=editders\';">Soruları Yönet</button>'
					.'<button class="sagbutton1" type="button" onclick="window.location = \'index.php?page=stats\';">İstatistikler</button><br />'
					.'<button class="sagbutton1" type="button" onclick="window.location = \'index.php?action=logout\';">Çıkış Yap</button>';
					break;
					
					case '0': case '1': case '2':
					$this->HTML['login'] .= '<i>-- Ücretsiz Kullanıcı --</i><br />'
					.'<button class="sagbutton1" type="button" onclick="window.location = \'index.php?action=logout\';">Çıkış Yap</button>';
					break;
				}
			break;
		}
	}
	
	function GenerateTopmenu()
	{
		$this->HTML['topmenu'] =
		'<a href="index.php?page=mat" class="topbtn">Matematik</a>'
		.'<a href="index.php?page=geo" class="topbtn">Geometri</a>'
		.'<a href="index.php?page=fiz" class="topbtn">Fizik</a>'
		.'<a href="index.php?page=kim" class="topbtn">Kimya</a>'
		.'<a href="index.php?page=biy" class="topbtn">Biyoloji</a>';
		if( $_GET['page'] != "" ){ $this->HTML['topmenu'] = str_replace( $_GET['page'].'" class="topbtn">', $_GET['page'].'" class="topbtn_in">', $this->HTML['topmenu']); }
	}
	
	function GenerateSolmenu()
	{
		global $Database;
		$this->HTML['solmenu'] = "";
		$data = $Database->GetKonular($_GET['page']);
		$i = 1;
		while( $row = mysql_fetch_array($data) )
		{
			$this->HTML['solmenu'] .= '<a href="index.php?page='.$_GET['page'].'&konu='.$i.'" class="sollink">'.base64_decode($row['konu']).'</a>';
			$i++;
		}
		$_GET['konu'] = (isset($_GET['konu']) && $_GET['konu']>0 && $_GET['konu']<$i) ? intval($_GET['konu']) : '1';
		$this->HTML['solmenu'] = str_replace('='.$_GET['konu'].'" class="sollink">', '='.$_GET['konu'].'" class="sollinksecili">', $this->HTML['solmenu']);
	}
	
	function GenerateContent()
	{
		global $Database, $User, $_AMSG;
		
		switch($_GET['page'])
		{
			case 'register': case 'forgot':
			if($User->authState != "true"){ require_once('Pages/'.$_GET['page'].'.php'); }
			else{ require_once('Pages/home.php'); }
			break;
			
			case 'editsite': case 'edituser': case 'editders': case 'stats':
			if($User->adminstate == '3'){ require_once('Pages/'.$_GET['page'].'.php'); }
			else{ require_once('Pages/home.php'); }
			break;
			
			case "mat":	case "geo": case "fiz":	case "kim": case "biy":
			$this->GenerateSolmenu();
			$HTML_body = '<div class="cap" id="cap" style="text-align:right; white-space:nowrap;">'
			.'<div class="cap_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="GetSoru_Single(\''.$_GET['page'].'\', \''.$_GET['konu'].'\')">'
			.'<span class="symgreen">&#8747; </span><span id="ed_button_inline">Yeni Soru Getir</span></div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
			.'</div><div id="inner" class="inner" style=" width:570px; height:auto; white-space:nowrap; text-align:left;">'
			.'<script>GetSoru_Single("'.$_GET['page'].'", "'.$_GET['konu'].'");</script>'
			.'</div>';
			break;
					
			default: require_once('Pages/home.php'); break;
		}
		
		$this->HTML['content'] =& $HTML_body;
	}
	
	function PrintMainHTML()
	{
		$this->GenerateTopmenu();
		$this->GenerateContent();
		$this->GenerateLogin();
		$cnt = '
		<div class="orta">'.$this->HTML['content'].'</div>
		<div class="sag">'.$this->HTML['login'].'</div>
		';
		
		if($this->HTML['solmenu'] != "do-not-show")
		{
			$cnt = '<div class="sol tex2jax_ignore">'.$this->HTML['solmenu'].'</div>'.$cnt;
		}
		
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <meta http-equiv="X-UA-Compatible" content="IE=edge">
		  <title>Random Soru</title>
		  <link rel="icon" type="image/png" href="http://www.randomsoru.com/favicon_v1.png">
		  <link href="index.css" rel="stylesheet" type="text/css">
		  <script src="Javascript/main.js"></script>
		  <script type="text/x-mathjax-config"> MathJax.Hub.Config({tex2jax: {inlineMath: [[\'$\',\'$\'], [\'\\\\(\',\'\\\\)\']]}}); </script>
		  <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"> </script>
		  <script src="java.js"></script>
		</head>
		
		<body>
		<div class="site">
		<div class="topmenu"><a href="index.php"><img src="logo.png" width="240" height="50" /></a>'.$this->HTML['topmenu'].'</div>
		<div class="gap">&nbsp;</div>
		'.$cnt;
	}
	
	function PrintUpkeepHTML()
	{
		echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Random Soru</title>
		<link rel="icon" type="image/png" href="http://www.randomsoru.com/favicon_v1.png">
		<link href="index.css" rel="stylesheet" type="text/css">
		</head>
		<body><center>
		<br /><br /><br /><br /><br />
		<img src="biglogo.png" width="770" height="220" /><br />
		Random Soru şu anda bakım aşamasındadır...';
	}
/*~~~~~~~//
//  END  //
//~~~~~~~*/
}
?>