<?
function PasswordReset()
{
	global $Timer, $User, $Database, $_AMSG, $HTML_login, $HTML_body, $HTML_debug;
	
	if( $Database->CheckEmail($_GET['email']) ) {
	  if( $Database->MatchForgotkeyM($_GET['email'], $_GET['forgotkey']) ) {
		$newForgotkey = $User->GenerateCookey(23);
		$newPass = $User->GenerateCookey(13);
		$newCookey = $User->GenerateCookey(13);
		$Database->SetForgotkeyM($_GET['email'], $newForgotkey);
		$Database->SetCookeyM($_GET['email'], $newCookey);
		$Database->SetPasswordM($_GET['email'], $newPass);
		$fullname = $Database->GetFullnameM($_GET['email']);
		$username = $Database->GetUsernameM($_GET['email']);
		$message =
'<html><head><title>-- YENİ KULLANICI BİLGİLERİNİZ --</title></head><body>
Sayın '.$fullname.';<br /><br />
Random Soru\'dan talep ettiğiniz şifre sıfırlama işlemi gerçekleştirildi. Yeni kullanıcı bilgileriniz şu şekildedir:<br /><br />
<b>Kullanıcı Adı:</b> '.$username.'<br />
<b>Şifre:</b> '.$newPass.'
<br /><br /><i>- Random Hoca</i>
</body></html>';
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= 'To: <'.$_GET['email'].'>' . "\r\n";
		$headers .= 'From: Random Soru <noreply@randomsoru.com>' . "\r\n";
		mail($_GET['email'], '-- YENİ KULLANICI BİLGİLERİNİZ --', $message, $headers);
		echo '<script type="text/JavaScript"> alert("Şifreniz başarıyla sıfırlandı! Yeni kullanıcı bilgileriniz mail adresinize gönderilmiştir.") </script>';
	  } else{ echo '<script type="text/JavaScript"> alert("Şifre sıfırlama kodu yanlış.") </script>'; }
	} else{ echo '<script type="text/JavaScript"> alert("Bu mail adresine kayıtlı bir hesap bulunamadı.") </script>'; }
}
?>