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
$_sid = isset($_GET['sid']) ? intval(urlencode(htmlentities($_GET['sid']))) : -5;
$_sind = isset($_GET['sind']) ? intval(urlencode(htmlentities($_GET['sind']))) : -5;

$_addunsid = isset($_GET['addunsid']) ? (int)htmlentities( base64_decode( str_replace(" ", "+", $_GET['addunsid']) ), ENT_QUOTES, "UTF-8") : -5;

$nind = -1;
$arrkey = -5;
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

if($_action != 'none')
{	  
	switch($_action)
	{
		case 'addnewsorubyid':
			if( $data = mysql_fetch_array( mysql_query("SELECT sorulist, sorucount FROM konudata WHERE id = '1'") ) ) {
				if($data['sorucount'] > 0) {
					if( $datakonu = mysql_fetch_array( mysql_query("SELECT sorulist, sorucount FROM konudata WHERE ders = '$_ders' AND ind = '$_ind'") ) ) {
						$unSorulist = explode(':', $data['sorulist']);
						foreach($unSorulist as $key => $val)
							{ if($val == $_addunsid) { $arrkey = $key; break; } }
						if( $arrkey != -5  ) {
							unset($unSorulist[$arrkey]);
							$newunsorulist = implode(':', $unSorulist);
							if( $datakonu['sorucount'] != 0 )
								{ $newsorulist = $datakonu['sorulist'].':'.$_addunsid; }
							else
								{ $newsorulist = $_addunsid; }
							if( mysql_query("UPDATE konudata SET sorulist = '$newunsorulist', sorucount = sorucount-1 WHERE id = '1'") ) {
								if( mysql_query("UPDATE konudata SET sorulist = '$newsorulist', sorucount = sorucount+1 WHERE ders = '$_ders' AND ind = '$_ind'") ) {
									
									$amsg = "<span class='spangreen'>Boştaki soru listeye eklendi.";
									
								} else { $amsg = "<span class='spanred'>Hata! Boştaki soru eklenirken DB çalışmadı."; }
							} else { $amsg = "<span class='spanred'>Hata! Boştaki soru eklenirken DB çalışmadı."; }
						} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru boştakiler listesinde bulunamadı."; }
					} else { $amsg = "<span class='spanred'>Hata! Böyle bir konu yok."; }
				} else { $amsg = "<span class='spanred'>Hata! Boşta kalan soru yok."; }
			} else { $amsg = "<span class='spanred'>Hata! Boştaki soru eklenirken DB çalışmadı."; }
		break;
		
		case 'addnewsoru':
			if( $data = mysql_fetch_array( mysql_query("SELECT * FROM sorudata WHERE id = '1'") ) ) {
				if( mysql_query("INSERT INTO sorudata (name, description, code) VALUES ('".$data['name']."', '".$data['description']."', '".$data['code']."')") ) {
					if( $datakonu = mysql_fetch_array( mysql_query("SELECT sorulist, sorucount FROM konudata WHERE ders = '$_ders' AND ind = '$_ind'") ) ) {
						$lastsorudata = mysql_fetch_array( mysql_query("SELECT id FROM sorudata ORDER BY id DESC LIMIT 1") );
						$lastsoruid = $lastsorudata["id"];
						if($datakonu['sorucount'] > 0)	{ $newsorulist = $datakonu['sorulist'].":".$lastsoruid; }
						else							{ $newsorulist = $lastsoruid; }					
						if( mysql_query("UPDATE konudata SET sorulist = '$newsorulist', sorucount = sorucount+1 WHERE ders = '$_ders' AND ind = '$_ind'") ) {
							
							$amsg = "<span class='spangreen'>Yeni soru eklendi.";
							
						} else { $amsg = "<span class='spanred'>Hata! Yeni soru eklenirken DB çalışmadı."; }
					} else { $amsg = "<span class='spanred'>Hata! Yeni soru eklenirken DB çalışmadı."; }
				} else { $amsg = "<span class='spanred'>Hata! Yeni soru eklenirken DB çalışmadı."; }
			} else { $amsg = "<span class='spanred'>Hata! Yeni soru eklenirken DB çalışmadı."; }
		break;
		
		
		case 'up':
			if( $_sind > 1) {
				if( $data = mysql_query("SELECT sorulist FROM konudata WHERE ders = '$_ders' AND ind = '$_ind'") ) {
					if( $data = mysql_fetch_array($data) ) {
						$soruArray = explode(':', $data['sorulist']);
						if( $soruArray[$_sind-1] == $_sid ) {
							$upsoru = $soruArray[$_sind-2];
							$soruArray[$_sind-2] = $soruArray[$_sind-1];
							$soruArray[$_sind-1] = $upsoru;
							$newsorulist = implode(':', $soruArray);
							if( mysql_query("UPDATE konudata SET sorulist = '$newsorulist' WHERE ders = '$_ders' AND ind = '$_ind'") ) {
								
								$amsg = "<span class='spangreen'>Seçtiğiniz soru bir sıra yukarı alındı.";
								
							} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru yukarı taşınırken DB çalışmadı."; }
						} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz sorunun kimliği, indeksi ile uyuşmuyor."; }
					} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru aslında yok."; }
				} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru yukarı taşınırken DB çalışmadı."; }
			} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru zaten en yukarıda."; }
		break;
		
		case 'down':
			if( $data = mysql_query("SELECT sorulist FROM konudata WHERE ders = '$_ders' AND ind = '$_ind'") ) {
				if( $data = mysql_fetch_array($data) ) {
					$soruArray = explode(':', $data['sorulist']);
					if( $_sind < count($soruArray) ) {
						if( $soruArray[$_sind-1] == $_sid ) {
							$upsoru = $soruArray[$_sind];
							$soruArray[$_sind] = $soruArray[$_sind-1];
							$soruArray[$_sind-1] = $upsoru;
							$newsorulist = implode(':', $soruArray);
							if( mysql_query("UPDATE konudata SET sorulist = '$newsorulist' WHERE ders = '$_ders' AND ind = '$_ind'") ) {
								
								$amsg = "<span class='spangreen'>Seçtiğiniz soru bir sıra aşağı alındı.";
								
							} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru aşağı taşınırken DB çalışmadı."; }
						} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz sorunun kimliği, indeksi ile uyuşmuyor."; }
					} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru zaten en aşağıda."; }
				} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru aslında yok."; }
			} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru aşağı taşınırken DB çalışmadı."; }
		break;
		
		case 'delete':
		if( $_ders != 'non') {
			if( $data = mysql_query("SELECT sorulist FROM konudata WHERE ders = '$_ders' AND ind = '$_ind'") ) {
				if( $data = mysql_fetch_array($data) ) {
					$soruArray = explode(':', $data['sorulist']);
					if( $soruArray[$_sind-1] == $_sid ) {
						unset($soruArray[$_sind-1]);
						$newsorulist = implode(':', $soruArray);
						if( mysql_query("UPDATE konudata SET sorulist = '$newsorulist', sorucount = sorucount-1 WHERE ders = '$_ders' AND ind = '$_ind'") ) {
							if( $undata = mysql_fetch_array( mysql_query("SELECT sorucount, sorulist FROM konudata WHERE id = '1'") ) ) {
								if($undata['sorucount'] != 0)
									{ $newunsorulist = $undata['sorulist'].':'.$_sid; }
								else
									{ $newunsorulist = $_sid; }
								if( mysql_query("UPDATE konudata SET sorulist = '$newunsorulist', sorucount = sorucount+1 WHERE id='1'") ) {
							
									$amsg = "<span class='spangreen'>Seçtiğiniz soru boşa alındı.";
									
								} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru boşa alınırken DB çalışmadı."; }
							} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru boşa alınırken DB çalışmadı."; }
						} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru boşa alınırken DB çalışmadı."; }
					} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz sorunun kimliği, indeksi ile uyuşmuyor."; }
				} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru aslında yok."; }
			} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru boşa alınırken DB çalışmadı."; }
		} else { $amsg = "<span class='spanred'>Hata! Bunun silinmesi teklif dahi edilemez."; }
		break;
		
		case 'erase':
		if( $_ders == 'non') {
			if( $data = mysql_query("SELECT sorulist FROM konudata WHERE id = '1'") ) {
				if( $data = mysql_fetch_array($data) ) {
					$soruArray = explode(':', $data['sorulist']);
					if( $soruArray[$_sind-1] == $_sid ) {
						unset($soruArray[$_sind-1]);
						$newsorulist = implode(':', $soruArray);
						if( mysql_query("UPDATE konudata SET sorulist = '$newsorulist', sorucount = sorucount-1 WHERE id = '1'") ) {
							if( mysql_query("DELETE FROM sorudata WHERE id = '$_sid'") ) {
							
								$amsg = "<span class='spangreen'>Seçtiğiniz soru kalıcı olarak silindi.";
									
							} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru silinirken DB çalışmadı."; }
						} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru silinirken DB çalışmadı."; }
					} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz sorunun kimliği, indeksi ile uyuşmuyor."; }
				} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru aslında yok."; }
			} else { $amsg = "<span class='spanred'>Hata! Seçtiğiniz soru silinirken DB çalışmadı."; }
		} else { $amsg = "<span class='spanred'>Hata! Bunun eylem teklif dahi edilemez."; }
		break;
		
		default: $amsg = "<span class='spanred'>Bu eylem henüz tanımlanmadı."; break;
	}
}

		      if( $konuData = mysql_fetch_array( mysql_query( "SELECT sorucount, sorulist FROM konudata WHERE ders = '$_ders' AND ind = '$_ind'" )) ) {
				if($konuData['sorucount'] != 0)
				{
					$sorulist = explode(':', $konuData['sorulist']);
				  	$sorulistq = "'".implode("','", $sorulist)."'";
				}
				else { $sorulistq  = '0'; }
				if( $soruData = mysql_query("SELECT id, name FROM sorudata WHERE id IN($sorulistq) ORDER BY FIELD(id, $sorulistq)") ) {
					
if($_ders == 'non' && $_ind == 1) //Eğer boştaki sorular ise
{
	$HTML_r .= '
	<div style="text-align:left">
	<div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="AddNewSoru(\''.$_ders.'\', \''.$_ind.'\')">
	<span style="text-align:left" class="symgreen">+ </span><span id="ed_button_inline">Yeni Soru Ekle</span></div>
	</div>
	
	<div class="ed_table">
	  <div class="ed_hrow" id="row_th">
		<div class="ed_col_ind-s" id="thind">#</div>
		<div class="ed_col_id-s" id="thind">ID</div>
		<div class="ed_col_soru" id="thkonu">Soru</div>
		<div class="ed_col_buttons-s" id="thbuttons">Eylemler</div>
	  </div>
	';
	
	if($konuData['sorucount'] != 0)
	{
		
		for( $i=1; $i<=$konuData['sorucount']; $i++)
		{
			$row = mysql_fetch_array($soruData);
			$HTML_r .= '
		  <div class="ed_row" id="row_'.$i.'">
			<div class="ed_col_ind-s" id="ind_'.$i.'">'.$i.'</div>
			<div class="ed_col_id-s" id="id_'.$i.'">'.$row['id'].'</div>
			<div class="ed_col_soru" id="soru_'.$i.'">'.base64_decode($row['name']).'</div>
			<div class="ed_col_buttons-s" id="buttons_'.$i.'">
			  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoEditSoru(\''.$_ders.'\', \''.$_ind.'\', \''.$row['id'].'\', \''.$i.'\')">
			  <span class="symgreen">&#926; </span><span id="ed_button_inline">Düzenle</span></div>
			  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoSwapSoru(\'up\', \''.$_ders.'\', \''.$_ind.'\', \''.$row['id'].'\', \''.$i.'\')">
			  <span class="symblue">&#8593; </span><span id="ed_button_inline">Yukarı Al</span></div>
			  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoSwapSoru(\'down\', \''.$_ders.'\', \''.$_ind.'\', \''.$row['id'].'\', \''.$i.'\')">
			  <span class="symblue">&#8595; </span><span id="ed_button_inline">Aşağı Al</span></div>
			  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoEraseSoru(\'ask\', \''.$_ders.'\', \''.$_ind.'\', \''.$row['id'].'\', \''.$i.'\')">
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
		<p><span class="spanred">Boşta kalmış bir soru yok!</span></p>
	  </div>
		';
	}
	$HTML_r .= '</div><br />';	
}
else  //Eğer kullanılan sorular ise
{		  
	$HTML_r .= '
	<div style="text-align:left">
	<div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="AddNewSoru(\''.$_ders.'\', \''.$_ind.'\')">
	<span class="symgreen">+ </span><span id="ed_button_inline">Yeni Soru Ekle</span></div>
	&nbsp;
	<span style="font-size:12px;" id="addbyid">
		<div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="AddNewSoruById(\'ask\', \''.$_ders.'\', \''.$_ind.'\')">
		<span class="symgreen">+ </span><span id="ed_button_inline">Boştaki Sorulardan Ekle</span></div>
	</span>
	</div>
	
	<div class="ed_table">
	  <div class="ed_hrow" id="row_th">
		<div class="ed_col_ind-s" id="thind">#</div>
		<div class="ed_col_id-s" id="thind">ID</div>
		<div class="ed_col_soru" id="thkonu">Soru</div>
		<div class="ed_col_buttons-s" id="thbuttons">Eylemler</div>
	  </div>
	';
		
	
	if($konuData['sorucount'] != 0)
	{
		for( $i=1; $i<=$konuData['sorucount']; $i++)
		{
			$row = mysql_fetch_array($soruData);
			$HTML_r .= '
		  <div class="ed_row" id="row_'.$i.'">
			<div class="ed_col_ind-s" id="ind_'.$i.'">'.$i.'</div>
			<div class="ed_col_id-s" id="id_'.$i.'">'.$row['id'].'</div>
			<div class="ed_col_soru" id="soru_'.$i.'">'.base64_decode($row['name']).'</div>
			<div class="ed_col_buttons-s" id="buttons_'.$i.'">
			  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoEditSoru(\''.$_ders.'\', \''.$_ind.'\', \''.$row['id'].'\', \''.$i.'\')">
			  <span class="symgreen">&#926; </span><span id="ed_button_inline">Düzenle</span></div>
			  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoSwapSoru(\'up\', \''.$_ders.'\', \''.$_ind.'\', \''.$row['id'].'\', \''.$i.'\')">
			  <span class="symblue">&#8593; </span><span id="ed_button_inline">Yukarı Al</span></div>
			  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoSwapSoru(\'down\', \''.$_ders.'\', \''.$_ind.'\', \''.$row['id'].'\', \''.$i.'\')">
			  <span class="symblue">&#8595; </span><span id="ed_button_inline">Aşağı Al</span></div>
			  <div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoDeleteSoru(\'ask\', \''.$_ders.'\', \''.$_ind.'\', \''.$row['id'].'\', \''.$i.'\')">
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
		<p><span class="spanred">Bu konu için hiç bir soru eklenmemiş!</span></p>
	  </div>
		';
	}
	$HTML_r .= '</div><br />';
}

			  } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
			} else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
          } else { $amsg = "<span class='spanred'>Hata! Yeni cookey yazılamadı, işlem yarıda kesildi."; }
        } else { $amsg = "<span class='spanred'>Hata! İzinsiz erişim."; }
      } else { $amsg = "<span class='spanred'>Hata! İzinsiz erişim."; }
    } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
  } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
} else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı."; }

$amsg = str_replace("_konu_", $_konu, $amsg);

$amsg .= "<span class='timeinfo'><p>İşlem Süresi: ".$Timer->GetTotalTime("milli")." ms</p></span></span>";
$HTML_r .= '<span id="amsg">'.$amsg.'</span><br />';

echo $HTML_r;