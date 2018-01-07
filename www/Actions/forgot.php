<?
function SendForgotLink()
{
	global $Timer, $User, $Database, $_AMSG;
	
	$rEmail = htmlentities($_POST['forgotEmail'], ENT_QUOTES, "UTF-8");
	if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST['forgotEmail'])) {
	  if( $Database->CheckEmail($rEmail) ) {
		if( $Database->CheckActivationM($rEmail) ) {
		  require_once('Code/recaptchalib.php');
  		  $privatekey = $Database->captchaPrivateKey;
  		  $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
	      if($resp->is_valid){
			$fCount = intval( $Database->GetForgotcountM($rEmail) );
			$fDate = intval( $Database->GetForgotdateM($rEmail) );
			$nDate = time();
			if( $fDate+604800 < $nDate ){ $Database->SetForgotdateM($rEmail, $nDate); $fCount = 0; }
			if($fCount < 2)
			{
			  $forgotkey = $User->GenerateCookey(23);
			  $Database->SetForgotkeyM($rEmail, $forgotkey);
			  $uname = $Database->GetFullnameM($rEmail);
			  $message =
'<html><head><title>-- ŞİFRE SIFIRLAMA ONAYI --</title></head><body>
Sayın '.$uname.';<br /><br />
Random Soru\'dan şifre sıfırlama isteğinde bulundunuz. Aşağıdaki linke tıkladığınızda bunu onaylamış olacaksınız. Şifreniz sıfırlanacak ve kullanıcı adınızla beraber size tekrardan mail olarak gönderilecektir.<br />
<a href="http://www.randomsoru.com/index.php?action=newpass&email='.urlencode($rEmail).'&forgotkey='.$forgotkey.'">Şifrenizin sıfırlanmasını onaylıyorsanız burayı tıklayın...</a><br />
<br />
<b>Önemli Not:</b> Eğer şifre sıfırlama isteğinde bulunmadıysanız bu maili dikkate almayın.
<br /><br /><i>- Random Hoca</i>
</body></html>';
			  $headers  = 'MIME-Version: 1.0' . "\r\n";
			  $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
			  $headers .= 'To: <'.$_POST['forgotEmail'].'>' . "\r\n";
			  $headers .= 'From: Random Soru <noreply@randomsoru.com>' . "\r\n";
			  mail($_POST['forgotEmail'], '-- ŞİFRE SIFIRLAMA ONAYI --', $message, $headers);
			  $fCount += 1;
			  $Database->SetForgotcountM($rEmail, $fCount);
			  $_AMSG['forgot'] = "<span class='spangreen'>İşlem başarılı!<br/>Şifre sıfırlama onayı linki mail adresinize gönderildi...</span>";
			} else{$_AMSG['forgot'] = "<span class='spanred'>İşlem yapılamadı!<br/>Bir hafta içerisinde en fazla 2 kere şifre sıfırlama talebinde bulunabilirsiniz.</span>"; }
		  } else{$_AMSG['forgot'] = "<span class='spanred'>İşlem yapılamadı!<br/>Captcha kelimelerini yanlış girdiniz.</span>"; }
		} else{$_AMSG['forgot'] = "<span class='spanred'>İşlem yapılamadı!<br/>Girdiğiniz mail adresi henüz etkinleştirilmemiş bir hesaba ait.</span>"; }
	  } else{$_AMSG['forgot'] = "<span class='spanred'>İşlem yapılamadı!<br/>Girdiğiniz mail adresi üzerine kayıtlı bir hesap bulunamadı.</span>"; }
	} else{$_AMSG['forgot'] = "<span class='spanred'>İşlem yapılamadı!<br/>Geçersiz bir mail adresi girdiniz.</span>"; }
}
?>