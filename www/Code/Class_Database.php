<?
require("Code/encrypt.php");
class Database
{
	//~~~~~~~~~~~//
	//  DECLARE  //
	//~~~~~~~~~~~//
	public $DBcon;
	public $errorCount = 0;
	public $errorMessage = array("Error Messages");
	public $siteData = array(
		"status" => "1",
		"usercount" => "1",
		"admincookey" => "1"
	);

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
    //  CHANGE THESE BEFORE DEPLOYMENT  //
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	public $captchaPrivateKey = "## ENTER YOUR RECAPTCHA PRIVATE KEY HERE ##";
	public $DBserver = "##DBserver##";
    public $DBuser = "##DBuser##";
    public $DBpass = "##DBpass##";
    public $DBname = "##DBname##";
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
    //  !!! ATTENTION !!!
	//	I was an amateur kid back in 2013 and copy-pasted database login credentials to every code page.
	//	Now in 2018, I'm very lazy change them in a better way.
	//	So if you are going to use this project for yourself, be sure to change them with your own credentials.
	//	Do a find and replace across all files.
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
	
	//~~~~~~~~//
	//  MAIN  //
	//~~~~~~~~//
	function ThrowError($string)
	{
		$this->errorCount += 1;
		$this->errorMessage[$this->errorCount] = $string;
	}
	
	function PrintErrorList()
	{
		echo "<br /><br />
		Error Count: ".$this->errorCount;
		for($i=0; $i<$this->errorCount; $i++)
		{
			echo "<br />".strval($i+1).") ".$this->errorMessage[$i+1];
		}
	}
	
	function Connect($DBserver, $DBuser, $DBpass, $DBname)
	{
		$this->DBcon =  mysql_pconnect($DBserver, $DBuser, $DBpass);
		if($this->DBcon == false) { $this->ThrowError("Database connection couldn't established."); return; }
		if(!mysql_select_db($DBname, $this->DBcon)) { $this->ThrowError("Database does not exist on server."); }
	}
	
	function Disconnect()
	{
		if(!mysql_close($this->DBcon)) { $this->ThrowError("Database couldn't disconnected.");	}
	}
	
	//~~~~~~~~~//
	//  MATCH  //
	//~~~~~~~~~//
	// Match Cookey w/ Username
	function MatchCookeyU($username, $cookey){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query("SELECT cookey FROM userdata WHERE username = '".urlencode($username)."'");
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			if($data['cookey'] == "") { return false; }
			else{ return validate_password($cookey, $data['cookey']); }
		} 
	} 
	$this->ThrowError("Error occured on 'MatchCookeyU'"); }
	
	// Match Forgotkey w/ Mail
	function MatchForgotkeyM($email, $forgotkey){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT forgotkey FROM userdata WHERE email = '".urlencode($email)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			return validate_password($forgotkey, $data['forgotkey']);
		} 
	}
	$this->ThrowError("Error occured on 'CheckForgotkey'"); }
	
	// Match Password w/ Username
	function MatchPasswordU($username, $password){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT password FROM userdata WHERE username = '".urlencode($username)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			if ($data['password'] == "") { return "false"; }
			else { return validate_password($password, $data['password']); }
		} 
	}
	$this->ThrowError("Error occured on 'MatchPasswordU'"); }
	
	//~~~~~~~~~//
	//  CHECK  //
	//~~~~~~~~~//
	// Check Activation w/ Username
	function CheckActivationU($username){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT etkin FROM userdata WHERE username = '".urlencode($username)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			if($data['etkin'] == "0") { return false; }
			else { return true; } 
		}
	}
	$this->ThrowError("Error occured on 'CheckActivationU'"); }
	
	// Check Activation w/ Mail
	function CheckActivationM($email){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT etkin FROM userdata WHERE email = '".urlencode($email)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			if($data['etkin'] == "0") { return false; }
			else { return true; } 
		}
	}
	$this->ThrowError("Error occured on 'CheckActivationM'"); }
	
	// Check if User exists
	function CheckUser($username){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT username FROM userdata WHERE username = '".urlencode($username)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			if( urldecode($data['username']) == $username && strlen($username)>2 ){ return true; }
			else{ return false; }
		} 
	}
	$this->ThrowError("Error occured on 'CheckUser'"); }
	
	// Check if Mail Exists
	function CheckEmail($email){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT email FROM userdata WHERE email = '".urlencode($email)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			if( urldecode($data['email']) == $email ){ return true;	}
			else{ return false; }
		} 
	}
	$this->ThrowError("Error occured on 'CheckEmail'");	}
	
	//~~~~~~~//
	//  SET  //
	//~~~~~~~//
	// Set Cookey w/ Username
	function SetCookeyU($username, $cookey){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET cookey = '".create_hash($cookey, 64)."' WHERE username = '".urlencode($username)."'" );
		if( $data != false ){ return true; } 
	}
	$this->ThrowError("Error occured on 'SetCookeyU'");	}
	
	// Set Cookey w/ Mail
	function SetCookeyM($email, $cookey){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET cookey = '".create_hash($cookey, 64)."' WHERE email = '".urlencode($email)."'" );
		if( $data != false ){ return true; } 
	}
	$this->ThrowError("Error occured on 'SetCookeyM'");	}
	
	// Set Forgotkey w/ Mail
	function SetForgotkeyM($email, $forgotkey){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET forgotkey = '".create_hash($forgotkey, 64)."' WHERE email = '".urlencode($email)."'" );
		if( $data != false ){ return true; } 
	}
	$this->ThrowError("Error occured on 'SetForgotkeyM'"); }
	
	// Set Password w/ Mail
	function SetPasswordM($email, $password){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET password = '".create_hash($password, 256)."' WHERE email = '".urlencode($email)."'" );
		if( $data != false ){ return true; }
	}
	$this->ThrowError("Error occured on 'SetPasswordM'"); }
	
	// Set Forgotcount w/ Mail
	function SetForgotcountM($email, $forgotcount){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET forgotcount = '$forgotcount' WHERE email = '".urlencode($email)."'" );
		if( $data != false ){ return true; }
	}
	$this->ThrowError("Error occured on 'SetForgotcountM'"); }
	
	// Set Forgotdate w/ Mail
	function SetForgotdateM($email, $forgotdate){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET forgotdate = '$forgotdate' WHERE email = '".urlencode($email)."'" );
		if( $data != false ){ return true; }
	}
	$this->ThrowError("Error occured on 'SetForgotdateM'"); }
	
	//$Database->SetForgotcount($rEmail, $fCount);
	
	// Activate User w/ Username
	function SetActivationU($username){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET etkin = '1' WHERE username = '".urlencode($username)."'" );
		if( $data != false ){ return true; }
	}
	$this->ThrowError("Error occured on 'SetActivationU'"); }
	
	// Set Lastclickdate w/ Username
	function SetLastclickdateU($username, $lastclickdate){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET lastclickdate = '$lastclickdate' WHERE username = '".urlencode($username)."'" );
		if( $data != false ){ return true; }
	}
	$this->ThrowError("Error occured on 'SetLastclickdateU'"); }
	
	//$Database->SetClickcountU($User->username, $Database->GetClickcountU($User->username)+1);
	// Set Clickcount w/ Username
	function SetClickcountU($username, $clickcount){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET clickcount = '$clickcount' WHERE username = '".urlencode($username)."'" );
		if( $data != false ){ return true; }
	}
	$this->ThrowError("Error occured on 'SetclickcountU'"); }
	
	// Set Usercount
	function SetUsercount($usercount){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE sitedata SET usercount = '$usercount'" );
		if( $data != false ){ return true; }
	}
	$this->ThrowError("Error occured on 'SetUsercount'"); }
	
	//~~~~~~~//
	//  GET  //
	//~~~~~~~//
	// Get Adminstate w/ Username
	function GetAdminstateU($username){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT adminstate FROM userdata WHERE username = '".urlencode($username)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			return $data['adminstate']; break;
		} 
	}
	$this->ThrowError("Error occured on 'GetAdminstateU'"); }
	
	// Get Fullname w/ Username
	function GetFullnameU($username){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT fullname FROM userdata WHERE username = '".urlencode($username)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			$fullname = urldecode($data['fullname']);
			if($fullname == ""){ return "Adı Güzel"; }
			else{ return $fullname; }
		} 
	}
	$this->ThrowError("Error occured on 'GetFullnameU'"); }
	
	// Get Fullname w/ Mail
	function GetFullnameM($email){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT fullname FROM userdata WHERE email = '".urlencode($email)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			$fullname = urldecode($data['fullname']);
			if($fullname == ""){ return "Adı Güzel"; }
			else{ return $fullname; }
		}
	}
	$this->ThrowError("Error occured on 'GetFullnameM'"); }
	
	// Get Username w/ Mail
	function GetUsernameM($email){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT username FROM userdata WHERE email = '".urlencode($email)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			return urldecode($data['username']);
		} 
	}
	$this->ThrowError("Error occured on 'GetUsernameM'"); }
	
	// Get Forgotcount w/ Mail
	function GetForgotcountM($email){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT forgotcount FROM userdata WHERE email = '".urlencode($email)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			return urldecode($data['forgotcount']);
		} 
	}
	$this->ThrowError("Error occured on 'GetForgotcountM'"); }
	
	// Get Forgotdate w/ Mail
	function GetForgotdateM($email){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT forgotdate FROM userdata WHERE email = '".urlencode($email)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			return urldecode($data['forgotdate']);
		} 
	}
	$this->ThrowError("Error occured on 'GetForgotdateM'"); }
	
	// Get Clickcount w/ Username
	function GetClickcountU($username){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT clickcount FROM userdata WHERE username = '".urlencode($username)."'" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			return intval($data['clickcount']);
		} 
	}
	$this->ThrowError("Error occured on 'GetClickcountU'"); }
	
	// Get Konular for Solmenu
	function GetKonular($ders){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT * FROM konudata WHERE ders = '$ders' ORDER BY ind ASC" );
		if( $data != false ) { return $data; } 
	}
	$this->ThrowError("Error occured on 'GetKonular'"); }
	
	// Get Usercount
	function GetUsercount(){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "SELECT usercount FROM sitedata" );
		if( $data != false )
		{
			$data = mysql_fetch_array( $data );
			return intval($data['usercount']);
		} 
	}
	$this->ThrowError("Error occured on 'GetUsercount'"); }
	
	// Get Userlist for Useredit
	function GetUserlist($sortfor, $sortorder, $rowamount, $tablepage){ for($i=0; $i<6; $i++)
	{
		$limit = $tablepage*$rowamount - $rowamount;
		$data = mysql_query( "SELECT adminstate, username, regdate, etkin, lastclickdate, clickcount FROM userdata ORDER BY $sortfor $sortorder LIMIT $limit, $rowamount" );
		if( $data != false ) { return $data; }
	}
	$this->ThrowError("Error occured on 'GetUserlist'"); }
	
	//~~~~~~~//
	//  ADD  //
	//~~~~~~~//
	// Add New User
	function AddNewUser($username, $password, $cookey, $email, $fullname, $forgotkey){ for($i=0; $i<6; $i++)
	{
		$uc = $this->GetUsercount()+1;
		$ntime = time();
		$data = mysql_query(
		"INSERT INTO userdata (adminstate, username, password, cookey, email, fullname, etkin, regdate, forgotkey, forgotdate, forgotcount, clickcount) VALUES (
		'0',
		'".urlencode($username)."',
		'".create_hash($password, 256)."',
		'".create_hash($cookey, 64)."',
		'".urlencode($email)."',
		'".urlencode($fullname)."',
		'0',
		'".$ntime."',
		'".create_hash($forgotkey, 64)."',
		'".$ntime."',
		'0',
		'1')"
		);
		
		if( $data != false )
		{
			if( $this->SetUsercount($uc) ) { return true; }
		}
	}
	$this->ThrowError("Error occured on 'AddNewUser'"); return false; }
	
	function AddNewUserTest($username, $password, $cookey, $email, $fullname, $forgotkey){ for($i=0; $i<6; $i++)
	{
		$uc = $this->GetUsercount()+1;
		$ntime = time();
		$data = mysql_query(
		"INSERT INTO userdata (adminstate, username, password, cookey, email, fullname, etkin, regdate, forgotkey, forgotdate, forgotcount, clickcount) VALUES (
		'".strval(rand(0, 2))."',
		'".urlencode($username)."',
		'".create_hash($password, 256)."',
		'".create_hash($cookey, 64)."',
		'".urlencode($email)."',
		'".urlencode($fullname)."',
		'".strval(rand(0, 1))."',
		'".$ntime."',
		'".create_hash($forgotkey, 64)."',
		'".$ntime."',
		'0',
		'".strval(rand(0, 1000))."' )"
		);
		
		if( $data != false )
		{
			if( $this->SetUsercount($uc) ) { return true; }
		}
	}
	$this->ThrowError("Error occured on 'AddNewUserTest'"); return false; }



/*~~~~~~~//
//  END  //
//~~~~~~~*/

	// Set Cookey w/ id
	function SetCookeyI($id, $cookey){ for($i=0; $i<6; $i++)
	{
		$data = mysql_query( "UPDATE userdata SET cookey = '".create_hash($cookey, 64)."', clickcount = clickcount+1 WHERE id = '$id'" );
		if( $data != false ){ return true; } 
	}
	$this->ThrowError("Error occured on 'SetCookeyI'");	}
	

	// Author User
	function AuthorUser($authwithwhat, $key, $getwithwhat, $id, &$oUser)
	{
		$id = urlencode($id);
		for($i=0; $i<6; $i++)
		{
			if($data = mysql_query( "SELECT id, username, adminstate, $authwithwhat, email, fullname, etkin FROM userdata WHERE $getwithwhat = '$id'" )){$i=9;}
			else if($i==5){ $this->ThrowError("Error occured on 'AuthorUser'"); return "null"; }
		}
		if( $data = mysql_fetch_object($data) )
		{
			if( validate_password($key, $data->$authwithwhat) )
			{
				if($data->etkin == "1")
				{
					$data->username = urldecode($data->username);
					$data->fullname = urldecode($data->fullname);
					$data->email = urldecode($data->email);
					foreach($data as $key => $value)
					{
   						$oUser->$key = $value;
					} return "true";
				} else { return "inactive"; }
			}  else { return "false"; }
		} else { return "false"; }
	}
	
	// Get Site Data
	function GetSiteData(){ for($i=0; $i<6; $i++)
	{
		if( $this->siteData = mysql_fetch_array(mysql_query( "SELECT * FROM sitedata" )) )
		{
			return true;
		}
	}
	$this->ThrowError("Error occured on 'GetSiteData'");	}
	
	// Get Konu Data
	function GetKonuData(){ for($i=0; $i<6; $i++)
	{
		if( $data = mysql_query( "SELECT * FROM konudata ORDER BY ders ASC, ind ASC" ) )
		{
			return $data;
		}
	}
	$this->ThrowError("Error occured on 'GetKonuData'");	}
	
	// Set Site Data
	function SetSiteData($status, $register, $forgot, $usercount, $admincookey){ for($i=0; $i<6; $i++)
	{
		$qry = "UPDATE sitedata SET ";
		if($status !== false) { $qry .= "status = '$status', "; $this->siteData['status'] = $status; }
		if($register !== false) { $qry .= "register = '$register', "; $this->siteData['register'] = $register; }
		if($forgot !== false) { $qry .= "forgot = '$forgot', "; $this->siteData['forgot'] = $forgot; }
		if($usercount !== false) { $qry .= "usercount = usercount$usercount, "; $this->siteData['usercount'] = (int)$this->siteData['usercount'] + (int)$usercount; }
		if($admincookey !== false) { $qry .= "admincookey = '".create_hash($admincookey, 64)."'"; }
		else { $qry = substr($qry, 0, -2);  }
		$qry .= " WHERE rowid = '5'";
			
		$data = mysql_query( $qry );
		if( $data != false ){ return true; } 
	}
	$this->ThrowError("Error occured on 'GetSiteData'"); return false; }

}?>











