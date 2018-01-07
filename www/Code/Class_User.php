<?
class User
{
	public $authState = 'null';
	public $keyvalid = false;
	public $adminstate = "0";

	function RenewCookie()
	{
		setcookie("id", $this->id, 2147483000);
		setcookie("cookey", $this->cookey, 2147483000);
	}
	
	function DeleteCookie()
	{
		setcookie("id", "x", time()-100000);
		setcookie("cookey", "x", time()-100000);
	}

	function GenerateCookey($length)
	{
		$chars = "0123456789abcdefghijklmnopqrstuvwxyzQWERTYUIOPASDFGHJKLZXCVBNMxxxx";
		$key = "";    
		for($i=0; $i<$length; $i++) { $key .= $chars[mt_rand(0, 62)]; }
		return $key;
	}
}
?>