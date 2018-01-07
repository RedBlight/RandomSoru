<?
$HTML_body = '<div class="cap">YENİ ÜYE KAYDI</div><div class="inner">';
if($Database->siteData['register'] == '1')
{
	$_POST['registerUsername'] = isset($_POST['registerUsername']) ? htmlentities($_POST['registerUsername'], ENT_QUOTES, "UTF-8") : '';
	$_POST['registerPassword'] = isset($_POST['registerPassword']) ? htmlentities($_POST['registerPassword'], ENT_QUOTES, "UTF-8") : '';
	$_POST['registerFullname'] = isset($_POST['registerFullname']) ? htmlentities($_POST['registerFullname'], ENT_QUOTES, "UTF-8") : '';
	$_POST['registerEmail'] = isset($_POST['registerEmail']) ? htmlentities($_POST['registerEmail'], ENT_QUOTES, "UTF-8") : '';
	$_AMSG['register'] = isset($_AMSG['register']) ? $_AMSG['register'] : '';
	
	require_once('Code/recaptchalib.php');
	$publickey = "6LfGMt8SAAAAAG3l5rmgDChrtDwCyKA8CF2cifZn";
	$HTML_catchpa = recaptcha_get_html($publickey);
	$HTML_body .= '
	Üyelik almak için aşağıdaki formu dolurun ve gönderin.<br />
	Ardından size gönderilecek mail üzerinden üyeliğinizi etkinleştirin.<br /><br />
	<form name="registerForm" method="post" action="index.php?page=register&action=register">
	<table width="400" align="center" border="0" cellspacing="0" cellpadding="4">
	  <tr>
	   <td align="right" width="150">Kullanıcı Adı:</td>
	   <td width="250"><input size="32" value="'.$_POST['registerUsername'].'" type="text" name="registerUsername" maxlength="25" /></td>
	  </tr>
	  <tr>
	   <td align="right" width="150">Şifre:</td>
	   <td width="250"><input size="32" value="'.$_POST['registerPassword'].'" type="password" name="registerPassword" maxlength="25" /></td>
	  </tr>
	  <tr>
	   <td align="right" width="150">Ad Soyad:</td>
	   <td width="250"><input size="32" value="'.$_POST['registerFullname'].'" type="text" name="registerFullname" maxlength="110" /></td>
	  </tr>
	  <tr>
	   <td align="right" width="150">E-Mail Adresi:</td>
	   <td width="250"><input size="32" value="'.$_POST['registerEmail'].'" type="text" name="registerEmail" maxlength="150" /></td>
	  </tr>
	</table>
	<table height="10" width="400" align="center" border="0" cellspacing="0" cellpadding="6">
	  <tr>
		<td width="400">Gördüğünüz kelimeleri aşağısındaki boşluğa yazın:</td>
	  </tr>
	</table>
	<table width="400" align="center" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td width="400">
		<center>
		<script> var RecaptchaOptions = { theme : "clean" };
		</script>'.$HTML_catchpa.'
		</center></td>
	  </tr>
	</table>
	<input value="Formu Gönder" type="submit" />
	</form><br />'.$_AMSG['register'].'</div>';
}
else
{
	$HTML_body .= 'Kayıt işlemi geçici bir süreliğine devre dışı bırakılmıştır.<br /></div>';
}
?>