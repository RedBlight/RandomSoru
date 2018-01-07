<?
function ActivateUser()
{
	global $Timer, $User, $Database, $_AMSG, $HTML_login, $HTML_body, $HTML_debug;
	
	if($Database->CheckUser($_GET['user'])) {
	  if(!$Database->CheckActivationU($_GET['user'])) {
	    if($Database->MatchCookeyU($_GET['user'], $_GET['actkey'])) {
		  $Database->SetActivationU($_GET['user']);
		  $newCookey = $User->GenerateCookey(13);
		  $Database->SetCookeyU($_GET['user'], $newCookey);
		  echo '<script> alert("Üyeliğiniz etkinleştirildi!") </script>';
		} else{ echo '<script> alert("Etkinleştirme kodu yanlış.") </script>'; }
	  } else{ echo '<script> alert("Bu üyelik zaten önceden etkinleştirilmiş.") </script>'; }
	} else{ echo '<script> alert("Etkinleştirilmeye çalışılan üyelik bulunamadı.") </script>'; }
	echo '<script> window.location.href="index.php"; </script>';
}
?>