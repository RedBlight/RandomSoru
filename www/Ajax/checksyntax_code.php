<?
error_reporting(-1);
ini_set("display_errors", 1);
date_default_timezone_set('UTC');
include("../Code/Class_Timer.php");
$Timer = new Timer;
$Timer->StartTimer();

require_once("../Code/encrypt.php");

require_once("../Code/safereval/config.php");
require_once("../Code/safereval/class.php");
$Seval = new SaferEval();

require_once("../Code/tinyphp.php");
$Tinyphp = new tinyPhp;
$php_excludes = $Tinyphp->php_excludes;
$replace_variables = FALSE;
$remove_whitespace = FALSE;
$remove_comments = TRUE;
$excludes_array = array();

function GenerateCookey($length)
{
	$chars = "0123456789abcdefghijklmnopqrstuvwxyzQWERTYUIOPASDFGHJKLZXCVBNMxxxx";
	$key = "";    
	for($i=0; $i<$length; $i++) { $key .= $chars[mt_rand(0, 62)]; }
	return $key;
}
$cookey = GenerateCookey(13);

$_uid = isset($_POST['uid']) ? urlencode(htmlentities($_POST['uid'])) : 'xxxxx';
$_cookey = isset($_POST['cookey']) ? urlencode(htmlentities($_POST['cookey'])) : 'xxxxx';

$_phpstr = isset($_POST['phpstr']) ? base64_decode( str_replace(" ", "+", $_POST['phpstr']) ) : 'nonvalid';


$amsg = "";

if( $DBcon = mysql_pconnect("##DBserver##", "##DBuser##", "##DBpass##") ) {
  if( mysql_select_db("##DBname##", $DBcon) ) {
    if( $data = mysql_fetch_array(mysql_query("SELECT cookey, adminstate FROM userdata WHERE id = '$_uid'")) ) {
	  if( $data['adminstate'] = '3' ) {
        if( validate_password($_cookey, $data['cookey']) ) { setcookie("cookey", $cookey, 2147483000, '/');
		  if( mysql_query( "UPDATE userdata SET cookey = '".create_hash($cookey, 64)."', clickcount = clickcount+1 WHERE id = '$_uid'" ) ) {

$_phpstr = $Tinyphp->get_tiny(
  $_phpstr,
  $replace_variables,
  $remove_whitespace,
  $remove_comments,
  $excludes_array
);

$errors = $Seval->checkScript($_phpstr, true);

if($errors) { $amsg = '<span class="spanred">'.$Seval->htmlErrors($errors); }
else		{ $amsg = '<span class="spangreen">Kodlarda güvenlik hatası yok. Eğer başka uyarı mesajı görmüyorsanız sentax hatası da yok demektir.';  }


          } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
        } else { $amsg = "<span class='spanred'>Hata! İzinsiz erişim."; }
      } else { $amsg = "<span class='spanred'>Hata! İzinsiz erişim."; }
    } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
  } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
} else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }

$amsg .= "<span class='timeinfo'><p>İşlem Süresi: ".$Timer->GetTotalTime("milli")." ms</p></span></span>";

echo $amsg;
?>