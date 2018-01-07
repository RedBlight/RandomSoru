<?
function EditSite()
{
	global $Timer, $User, $Database, $_AMSG;
	$_POST['status'] = isset($_POST['status']) ? $_POST['status'] : '';
	$_POST['register'] = isset($_POST['register']) ? $_POST['register'] : '';
	$_POST['forgot'] = isset($_POST['forgot']) ? $_POST['forgot'] : '';
	
	if($Database->SetSiteData($_POST['status'], $_POST['register'], $_POST['forgot'], false, $User->cookey))
	{
		setcookie("admincookey", $User->cookey, 2147483000);
		$_AMSG['editsite'] = '<span class="spangreen">Kaydedildi.</span>';
	}
	else { $_AMSG['editsite'] = '<span class="spanred">Hata oluştu! Kaydedilemedi.</span>'; }
}
?>