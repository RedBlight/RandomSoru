<?
function Logout()
{
	global $Timer, $User, $Database, $_AMSG;
	
	if($User->authState == 'true')
	{
		$newCookey = $User->GenerateCookey(13);
		$Database->SetCookeyU($User->username, $newCookey);
		$User->DeleteCookie();
		$User->authState = 'null';
	}
	echo '<script>window.location.href="index.php"; </script>';
}
?>