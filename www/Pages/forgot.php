<?
$HTML_body = '<div class="cap">ŞİFRE SIFIRLAMA İŞLEMİ</div><div class="inner">';
if($Database->siteData['forgot'] == '1')
{
	$_POST['forgotEmail'] = isset($_POST['forgotEmail']) ? htmlentities($_POST['forgotEmail'], ENT_QUOTES, "UTF-8") : '';
	$_AMSG['forgot'] = isset($_AMSG['forgot']) ? $_AMSG['forgot'] : '';
	
	require_once('Code/recaptchalib.php');
	$publickey = "6LfGMt8SAAAAAG3l5rmgDChrtDwCyKA8CF2cifZn";
	$HTML_catchpa = recaptcha_get_html($publickey);
	$HTML_body .= '
	Aşağıdaki formu doldurun ve gönderin.<br />
	Daha sonra size gönderilecek onay maili içindeki linke tıklayarak işlemi onaylayın.<br />
	Onayınız alındığı anda şifreniz sıfırlanacak ve yeni kullanıcı bilgileriniz gönderilecektir.<br /><br />
	<form name="forgotForm" method="post" action="index.php?page=forgot&action=forgot">
	<table width="400" align="center" border="0" cellspacing="0" cellpadding="4">
	  <tr>
	   <td align="right" width="150">E-Mail Adresiniz:</td>
	   <td width="250"><input size="32" value="'.$_POST['forgotEmail'].'" type="text" name="forgotEmail" maxlength="150" /></td>
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
		</script>'.$HTML_catchpa.'<script>
		newinner = document.getElementById(\'recaptcha_table\').innerHTML;
		newinner = newinner.replace("<td style=\"padding: 18px 7px 18px 7px;\"> <img id=\"recaptcha_logo\" alt=\"\" width=\"71\" height=\"36\" src=\"http://www.google.com/recaptcha/api/img/clean/logo.png\"> </td>", "");
		newinner = newinner.replace("<td style=\"padding: 4px 7px 12px 7px;\"> <img id=\"recaptcha_tagline\" width=\"71\" height=\"17\" src=\"http://www.google.com/recaptcha/api/img/clean/tagline.png\"> </td>", "");
		document.getElementById(\'recaptcha_table\').innerHTML = newinner;
		</script>
		</center></td>
	  </tr>
	</table>
	<input value="Onay Linki Gönder" type="submit" />
	</form><br />'.$_AMSG['forgot'].'</div>';
}
else
{
	$HTML_body .= 'Şifre sıfırlama işlemi geçici bir süreliğine devre dışı bırakılmıştır.<br /></div>';
}
?>