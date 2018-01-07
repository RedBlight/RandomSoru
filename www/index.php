<?php
ob_start();
error_reporting(-1);
ini_set("display_errors", 1);
date_default_timezone_set('UTC');
/**/
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Include Classes.           //
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~// 
/* global $Timer, $User, $Database, $Content, $_AMSG */

require_once('Code/Class_Timer.php');
$Timer = new Timer();
$Timer->StartTimer();

require_once('Code/Class_User.php');
require_once('Code/Class_Database.php');
require_once('Code/Class_Content.php');
$User = new User();
$Database = new Database();
$Content = new Content();
$Timer->AddDelta("Initial");

$Database->Connect($Database->DBserver, $Database->DBuser, $Database->DBpass, $Database->DBname);
$Timer->AddDelta("DBconnect");

$Database->GetSiteData();
$superAdmin = false;
if(isset($_COOKIE['admincookey'])) { $superAdmin = validate_password($_COOKIE['admincookey'], $Database->siteData['admincookey']); }
if($Database->siteData['status'] == '1' || $superAdmin)
{
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	// Handle user status.        //
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	if(isset($_COOKIE['id'], $_COOKIE['cookey']))
	{
		$User->authState = $Database->AuthorUser("cookey", $_COOKIE['cookey'], "id", $_COOKIE['id'], $User);
	}
	
	if( $User->authState != "true" && isset($_POST['loginUsername'], $_POST['loginPassword']) )
	{
		$loginUsername = htmlentities($_POST['loginUsername'], ENT_QUOTES, "UTF-8");
		$loginPassword = htmlentities($_POST['loginPassword'], ENT_QUOTES, "UTF-8");
		$User->authState = $Database->AuthorUser("password", $loginPassword, "username", $loginUsername, $User);
	}
	
	if($User->authState == "true")
	{
		$User->cookey = $User->GenerateCookey(13);
		$User->RenewCookie();
		$Database->SetCookeyI($User->id, $User->cookey);
	}
	else { $User->DeleteCookie(); }
	$Timer->AddDelta("User");
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	// Handle actions.            //
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	$_AMSG = array("Action Messages");
	if(isset($_GET['action']))
	{
		$actionPath = "Actions/".$_GET['action'].".php";
		switch($_GET['action'])
		{
			case 'logout':      require_once($actionPath); 	Logout();     break;
			case 'register':    require_once($actionPath); 	Register();   break;
			case 'forgot':      require_once($actionPath); 	SendForgotLink();   break;
			case 'newpass':     require_once($actionPath); 	PasswordReset();   break;
			case 'activate':    require_once($actionPath); 	ActivateUser();   break;
			
			case 'editsite':    if($User->adminstate == "3") { require_once($actionPath); 	EditSite(); }   break;
		}
	}
	$Timer->AddDelta("Action");
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	// Print HTML content.        //
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : '';
	$Content->PrintMainHTML();
}
else
{
	$Content->PrintUpkeepHTML();
}
$Timer->AddDelta("Content");



//~~~~~~~~~~~//
//   DEBUG   //
//~~~~~~~~~~~//
//Usertest Debug
if(isset($_GET['aa']))
{
	$r = intval($_GET['bb']);
	for($i = 0; $i < $r; $i++)
	{
		$Database->AddNewUserTest(
			htmlentities($User->GenerateCookey(rand(5, 17)), ENT_QUOTES, "UTF-8"),
			htmlentities($User->GenerateCookey(rand(5, 17)), ENT_QUOTES, "UTF-8"),
			$User->GenerateCookey(13),
			htmlentities($User->GenerateCookey(7).'@'.$User->GenerateCookey(5).'.'.$User->GenerateCookey(3), ENT_QUOTES, "UTF-8"),
			htmlentities($User->GenerateCookey(rand(6, 20)).' '.$User->GenerateCookey(rand(6, 20)), ENT_QUOTES, "UTF-8"),
			$User->GenerateCookey(23)
		);
	}
	echo "<br /><br />
	-- Usertest --<br />
	Loop: ".$_GET['bb']."<br />
	Time: ".$Timer->GetDeltaTime();
}
$Database->PrintErrorList();
$Timer->PrintDeltaList();

$output = ob_get_contents();
ob_end_clean();
echo $output;
?>
</div></body></html>