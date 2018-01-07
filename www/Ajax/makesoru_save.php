<?
error_reporting(-1);
ini_set("display_errors", 1);
date_default_timezone_set('UTC');
include("../Code/Class_Timer.php");
$Timer = new Timer;
$Timer->StartTimer();

require_once("../Code/encrypt.php");

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
$_sorucode = isset($_POST['sorucode']) ? str_replace(" ", "+", $_POST['sorucode']) : 'nonvalid';
$_svlcd = isset($_POST['svlcd']) ? base64_decode( str_replace(" ", "+", $_POST['svlcd']) ) : 'nonvalid';

$_svlcd_e = explode('::', $_svlcd);
$soruHash = $_svlcd_e[0];
$soruId = $_svlcd_e[1];

$amsg = "";

if( $DBcon = mysql_pconnect("##DBserver##", "##DBuser##", "##DBpass##") ) {
  if( mysql_select_db("##DBname##", $DBcon) ) {
    if( $data = mysql_fetch_array(mysql_query("SELECT cookey, adminstate FROM userdata WHERE id = '$_uid'")) ) {
	  if( $data['adminstate'] = '3' ) {
        if( validate_password($_cookey, $data['cookey']) ) { setcookie("cookey", $cookey, 2147483000, '/');
		  if( mysql_query( "UPDATE userdata SET cookey = '".create_hash($cookey, 64)."', clickcount = clickcount+1 WHERE id = '$_uid'" ) ) {
		    if( $soruData = mysql_fetch_array(mysql_query( "SELECT * FROM sorudata WHERE id = '$soruId'" )) ) {
			  if( validate_password($soruData['name'].":".$soruData['description'].":".$soruData['code'], $soruHash) ) {
				$soruval = explode(":", $_sorucode);
				$newName = array_shift($soruval);
				$newDescription = array_shift($soruval);
				$newCode = implode(":", $soruval);
			    if( mysql_query( "UPDATE sorudata SET name = '$newName', description = '$newDescription', code = '$newCode' WHERE id = '$soruId'" ) ) {
					
				  $_svlcd_new = base64_encode( create_hash( $newName.":".$newDescription.":".$newCode, 10 )."::".$soruId );
				  $amsg = "<span class='spangreen'>Soru başarıyla kaydedildi.";
				  $amsg .= '<script>$("#svlcd").html("'.$_svlcd_new.'");</script>';

				} else { $amsg = "<span class='spanred'>Hata! Database'de sorun oluştu."; }
              } else { $amsg = "<span class='spanred'>Hata! Yanlış giden bir şeyler var :)"; }
            } else { $amsg = "<span class='spanred'>Hata! Database'de sorun oluştu."; }
          } else { $amsg = "<span class='spanred'>Hata! Database'de sorun oluştu."; }
        } else { $amsg = "<span class='spanred'>Hata! İzinsiz erişim."; }
      } else { $amsg = "<span class='spanred'>Hata! İzinsiz erişim."; }
    } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
  } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
} else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }

$amsg .= "<span class='timeinfo'><p>İşlem Süresi: ".$Timer->GetTotalTime("milli")." ms</p></span></span>";

echo $amsg;

?>