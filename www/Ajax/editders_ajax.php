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

$_uid = isset($_GET['uid']) ? urlencode(htmlentities($_GET['uid'])) : 'xxxxx';
$_cookey = isset($_GET['cookey']) ? urlencode(htmlentities($_GET['cookey'])) : 'xxxxx';
$_action = isset($_GET['action']) ? urlencode(htmlentities($_GET['action'])) : 'none';
$_ders = isset($_GET['ders']) ? urlencode(htmlentities($_GET['ders'])) : 'xxxxx';
$_ind = isset($_GET['ind']) ? intval(urlencode(htmlentities($_GET['ind']))) : -5;

$_newname = isset($_GET['newname']) ? htmlentities( base64_decode( str_replace(" ", "+", $_GET['newname']) ), ENT_QUOTES, "UTF-8") : 'xxxxx';

$nind = -1;
$_konu = "xxxxx";

$kct = "konucount_".$_ders;
$dText = array(
	'mat' => 'Matematik',
	'geo' => 'Geometri',
	'fiz' => 'Fizik',
	'kim' => 'Kimya',
	'biy' => 'Biyoloji'
);
$HTML_r = "";
$amsg = "<span>";

if( $DBcon = mysql_pconnect("##DBserver##", "##DBuser##", "##DBpass##") ) {
  if( mysql_select_db("##DBname##", $DBcon) ) {
    if( $data = mysql_fetch_array(mysql_query("SELECT cookey, adminstate FROM userdata WHERE id = '$_uid'")) ) {
	  if( $data['adminstate'] = '3' ) {
        if( validate_password($_cookey, $data['cookey']) ) { setcookie("cookey", $cookey, 2147483000, '/');
		  if( mysql_query( "UPDATE userdata SET cookey = '".create_hash($cookey, 64)."', clickcount = clickcount+1 WHERE id = '$_uid'" ) ) {
			if( $siteData = mysql_fetch_array(mysql_query( "SELECT $kct FROM sitedata" )) ) {

if($_action != 'none')
{	  
	switch($_action)
	{
		case 'addnewkonu':
			$siteData[$kct]++;
			$dbInd =& $siteData[$kct];
			$_konu = htmlentities("Yeni Konu", ENT_QUOTES, "UTF-8");
			$dbKonu = base64_encode($_konu);
			if( mysql_query("INSERT INTO konudata (ders, konu, ind, sorucount) VALUES ('$_ders', '$dbKonu', '$dbInd', '0')")
			&& mysql_query("UPDATE sitedata SET $kct = '$dbInd' WHERE rowid = '5'") )
			{
				$amsg = "<span class='spangreen'>_konu_ başarılı bir şekilde eklendi.";
			} else { $amsg = "<span class='spanred'>Hata! _konu_ eklenirken DB çalışmadı."; }
			$nind = $dbInd;
		break;
		
		
		case 'up':
			if( $_ind > 1 )
			{
				$nind = $_ind - 1;
				if( mysql_query("UPDATE konudata SET ind = '0' WHERE ders = '$_ders' AND ind = '$_ind'")
				&& mysql_query("UPDATE konudata SET ind = '$_ind' WHERE ders = '$_ders' AND ind = '$nind'")
				&& mysql_query("UPDATE konudata SET ind = '$nind' WHERE ders = '$_ders' AND ind = '0'") )
				{
					$amsg = "<span class='spangreen'>_konu_ bir sıra yukarı alındı.";
				} else { $amsg = "<span class='spanred'>Hata! _konu_ yukarı taşınırken DB çalışmadı."; }
			} else { $amsg = "<span class='spanred'>Hata! _konu_ zaten en yukarıda."; $nind = $_ind; }
		break;
		
		case 'down':
			if( $_ind < $siteData[$kct] )
			{
				$nind = $_ind + 1;
				if( mysql_query("UPDATE konudata SET ind = '0' WHERE ders = '$_ders' AND ind = '$_ind'")
				&& mysql_query("UPDATE konudata SET ind = '$_ind' WHERE ders = '$_ders' AND ind = '$nind'")
				&& mysql_query("UPDATE konudata SET ind = '$nind' WHERE ders = '$_ders' AND ind = '0'") )
				{
					$amsg = "<span class='spangreen'>_konu_ bir sıra aşağı alındı.";
				} else { $amsg = "<span class='spanred'>Hata! _konu_ aşağı taşınırken DB çalışmadı."; }
			} else { $amsg = "<span class='spanred'>Hata! _konu_ zaten en aşağıda."; $nind = $_ind; }
		break;
		
		case 'rename':
			$wKonu = base64_encode($_newname);
			if( mysql_query("UPDATE konudata SET konu = '$wKonu' WHERE ders = '$_ders' AND ind = '$_ind'") )
			{
				$amsg = "<span class='spangreen'>_konu_ yeni isim olarak atandı.";
			} else { $amsg = "<span class='spanred'>Hata! _konu_ yeni isim olarak atanırken DB çalışmadı."; }
			$nind = $_ind;
		break;
		
		case 'delete':
		if( $_ders != 'non')
		{
			$datadel = mysql_fetch_array(mysql_query( "SELECT sorucount, sorulist, konu FROM konudata WHERE ders = '$_ders' AND ind = '$_ind'" ));
			$dataun = mysql_fetch_array(mysql_query( "SELECT sorucount, sorulist FROM konudata WHERE id = '1'" ));
			if($dataun['sorucount'] > 0 && $datadel['sorucount'] > 0)
			{
				$newsorulist = implode( ':', array( $dataun['sorulist'], $datadel['sorulist'] ) );
			}
			else { $newsorulist = $dataun['sorulist'].$datadel['sorulist']; }
			
			$newsorucount = $dataun['sorucount'] + $datadel['sorucount'];
			$_konu = '"'.base64_decode($datadel['konu']).'"';
			
			$siteData[$kct]--;
			$newkc =& $siteData[$kct];
			
			if(mysql_query("UPDATE konudata SET sorulist = '$newsorulist', sorucount = '$newsorucount' WHERE id = '1'")
			&& mysql_query("UPDATE sitedata SET $kct = '$newkc' WHERE rowid = '5'")
			&& mysql_query("DELETE FROM konudata WHERE ders = '$_ders' AND ind = '$_ind'")
			&& mysql_query("UPDATE konudata SET ind = ind-1 WHERE ders = '$_ders' AND ind > '$_ind'") )
			{
				$amsg = "<span class='spangreen'>_konu_ silindi, soruları boştaki sorular listesine eklendi.";
			} else { $amsg = "<span class='spanred'>Hata! Konu silinirken DB çalışmadı."; }
		}
		else { $amsg = "<span class='spanred'>Hata! Bunun silinmesi teklif dahi edilemez."; }
		break;
		
		default: $amsg = "<span class='spanred'>Bu eylem henüz tanımlanmadı."; break;
	}
}

		      if( $data = mysql_query( "SELECT * FROM konudata WHERE ders = '$_ders' ORDER BY ind ASC" ) ) {
	
//markup			  
$HTML_r .= '

<div style="text-align:left">
<div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="AddNewKonu(\''.$_ders.'\')">
<span style="text-align:left" class="symgreen">+ </span><span id="ed_button_inline">Yeni Konu Ekle</span></div>
</div>

<div class="ed_table">
  <div class="ed_hrow" id="row_th">
    <div class="ed_col_ind" id="thind">#</div>
	<div class="ed_col_konu" id="thkonu">Konu Adı</div>
	<div class="ed_col_sorucount" id="thsorucount">Soru</div>
	<div class="ed_col_buttons" id="thbuttons">Eylemler</div>
  </div>
';
if($siteData[$kct] != 0)
{
	for( $i=1; $i<=$siteData[$kct]; $i++)
	{
		$row = mysql_fetch_array($data);
		$HTML_r .= '
	  <div class="ed_row" id="row_'.$i.'">
		<div class="ed_col_ind" id="ind_'.$i.'">'.$i.'</div>
		<div class="ed_col_konu" id="konu_'.$i.'">'.base64_decode($row['konu']).'</div>
		<div class="ed_col_sorucount" id="sorucount_'.$i.'">'.$row['sorucount'].'</div>
		<div class="ed_col_buttons" id="buttons_'.$i.'">
		  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="GetListSoru(\''.$_ders.'\', \''.$i.'\')">
		  <span class="symgreen">&#926; </span><span id="ed_button_inline">Düzenle</span></div>
		  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoSwapKonu(\'up\', \''.$_ders.'\', \''.$i.'\')">
		  <span class="symblue">&#8593; </span><span id="ed_button_inline">Yukarı Al</span></div>
		  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoSwapKonu(\'down\', \''.$_ders.'\', \''.$i.'\')">
		  <span class="symblue">&#8595; </span><span id="ed_button_inline">Aşağı Al</span></div>
		  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoRenameKonu(\'ask\', \''.$_ders.'\', \''.$i.'\')">
		  <span class="symorange">&#8747; </span><span id="ed_button_inline">Adı Değiştir</span></div>
		  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoDeleteKonu(\'ask\', \''.$_ders.'\', \''.$i.'\')">
		  <span class="symred">&#8709; </span><span id="ed_button_inline">Sil</span></div>
		</div>
	  </div>
		';
		if($i == $nind) { $_konu = '"'.base64_decode($row['konu']).'"'; } //rapor için
	}
}
else
{
	$HTML_r .= '
  <div class="ed_row ed_bordered" id="row">
    <p><span class="spanred">Bu ders için hiç bir konu eklenmemiş!</span></p>
  </div>
	';
}
$HTML_r .= '</div><br />';

			  } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
			} else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
          } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
        } else { $amsg = "<span class='spanred'>Hata! İzinsiz erişim."; }
      } else { $amsg = "<span class='spanred'>Hata! İzinsiz erişim."; }
    } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
  } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
} else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }

$amsg = str_replace("_konu_", $_konu, $amsg);

$amsg .= "<span class='timeinfo'><p>İşlem Süresi: ".$Timer->GetTotalTime("milli")." ms</p></span></span>";
$HTML_r .= '<span id="amsg">'.$amsg.'</span><br />';

echo $HTML_r;

?>