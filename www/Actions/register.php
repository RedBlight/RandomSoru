<?
function Register()
{
	global $Timer, $User, $Database, $_AMSG;
	
	$rUsername = htmlentities($_POST['registerUsername'], ENT_QUOTES, "UTF-8");
	$rPassword = htmlentities($_POST['registerPassword'], ENT_QUOTES, "UTF-8");
	$rFullname = htmlentities($_POST['registerFullname'], ENT_QUOTES, "UTF-8");
	$rEmail = htmlentities($_POST['registerEmail'], ENT_QUOTES, "UTF-8");
	  
	if(mb_strlen($_POST['registerUsername']) > 3 && mb_strlen($_POST['registerUsername']) < 19) {
	  if( !$Database->CheckUser($rUsername) ) {
	    if(mb_strlen($_POST['registerPassword']) > 3 && mb_strlen($_POST['registerPassword']) < 19 ) {
		  if(mb_strlen($_POST['registerFullname']) > 3 && mb_strlen($_POST['registerFullname']) < 101 ) {
	        if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST['registerEmail'])) {
	          if( !$Database->CheckEmail($rEmail) ) {
				require_once('Code/recaptchalib.php');
  				$privatekey = $Database->captchaPrivateKey;
  				$resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
	            if($resp->is_valid){
				  $actkey = $User->GenerateCookey(13);
				  $forgotkey = $User->GenerateCookey(23);
		          $result = $Database->AddNewUser( $rUsername, $rPassword, $actkey, $rEmail, $rFullname, $forgotkey );
			      if($result)
				  {
					   $message =
'<html><head><title>-- ÜYELİK BİLGİLERİNİZ --</title></head><body>
Sayın '.$rFullname.';<br />
Random Soru\'daki üyeliğiniz aşağıdaki bilgiler ile başarılı bir şekilde oluşturulmuştur.<br /><br />
<b>Kullanıcı Adı:</b> '.$rUsername.'<br />
<b>Şifre:</b> '.$rPassword.'<br /><br />
Lütfen bu maili şifrenizi unutmanız ihitmaline karşı iyi saklayınız.<br /><br />
<strong>Üye girişi yapabilmek için üyeliğinizi etkinleştirmeniz gerekmektedir.</strong><br />
<a href="http://www.randomsoru.com/index.php?action=activate&user='.urlencode($rUsername).'&actkey='.$actkey.'">Üyeliğinizi etkinleştirmek için burayı tıklayın...</a>
<br /><br /><i>- Random Hoca</i>
</body></html>';
					  $headers  = 'MIME-Version: 1.0' . "\r\n";
					  $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
					  $headers .= 'To: <'.$_POST['registerEmail'].'>' . "\r\n";
					  $headers .= 'From: Random Soru <noreply@randomsoru.com>' . "\r\n";
					  mail($_POST['registerEmail'], '-- ÜYELİK BİLGİLERİNİZ --', $message, $headers);
					  $_AMSG['register'] = "<span class='spangreen'>Üyeliğiniz oluşturuldu!<br/>Lütfen size gönderilen mailden üyeliğinizi etkinleştirin.
				      <br/>Etkinleştirilmeyen üyelikler birkaç hafta içerisinde silinecektir.</span>";
				  } else{$_AMSG['register'] = "<span class='spanred'>Üyelik oluşturulamadı!<br/>İşlemlerde bir hata oluştu. Boşluklarda egzotik karakter kullanmayın. Bu hata devam ederse yöneticiye başvurun.</span>"; }
			    } else{ $_AMSG['register'] = "<span class='spanred'>Üyelik olşuturulamadı!<br/>Captcha kelimelerini yanlış girdiniz.</span>"; }
		  	  } else{$_AMSG['register'] = "<span class='spanred'>Üyelik oluşturulamadı!<br/>Yazdığınız mail adresi ile daha önce üye olunmuş.</span>"; }
		    } else{$_AMSG['register'] = "<span class='spanred'>Üyelik oluşturulamadı!<br/>Geçersiz bir mail adresi girdiniz.</span>"; }
		  } else{$_AMSG['register'] = "<span class='spanred'>Üyelik oluşturulamadı!<br/>Adınız soyadınız 4 ila 100 karakter arası olmalıdır.</span>"; }
		} else{$_AMSG['register'] = "<span class='spanred'>Üyelik oluşturulamadı!<br/>Şifreniz 4 ila 18 karakter arası olmalıdır.</span>"; }
	  } else{$_AMSG['register'] = "<span class='spanred'>Üyelik oluşturulamadı!<br/>Yazdığınız kullanıcı adı ile daha önce üye olunmuş.</span>"; }
	} else{$_AMSG['register'] = "<span class='spanred'>Üyelik oluşturulamadı!<br/>Kullanıcı adı 4 ila 18 karakter arası olmalıdır.</span>"; }
}
?>