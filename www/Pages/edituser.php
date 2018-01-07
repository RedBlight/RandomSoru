<?
$_GET['sortfor'] = isset($_GET['sortfor']) ? $_GET['sortfor'] : '';
$_COOKIE['sortfor'] = isset($_COOKIE['sortfor']) ? $_COOKIE['sortfor'] : '';
$_GET['sortorder'] = isset($_GET['sortorder']) ? $_GET['sortorder'] : '';
$_COOKIE['sortorder'] = isset($_COOKIE['sortorder']) ? $_COOKIE['sortorder'] : '';
$_GET['rowamount'] = isset($_GET['rowamount']) ? $_GET['rowamount'] : '';
$_COOKIE['rowamount'] = isset($_COOKIE['rowamount']) ? $_COOKIE['rowamount'] : '';
$_GET['tablepage'] = isset($_GET['tablepage']) ? $_GET['tablepage'] : '';
$_COOKIE['tablepage'] = isset($_COOKIE['tablepage']) ? $_COOKIE['tablepage'] : '';

$uCount = $Database->siteData['usercount'];

switch($_GET['sortfor'])
{
	case 'adminstate': case 'username': case 'regdate': case 'etkin': case 'lastclickdate': case 'clickcount': $gSortfor = $_GET['sortfor']; setcookie("sortfor", $gSortfor, 2147483000); break;
	default: 
	switch($_COOKIE['sortfor'])
	{
		case 'adminstate': case 'username': case 'regdate': case 'etkin': case 'lastclickdate': case 'clickcount': $gSortfor = $_COOKIE['sortfor']; break;
		default: $gSortfor = 'username'; break;
	}
	break;
}

switch($_GET['sortorder'])
{
	case 'ASC': case 'DESC': $gSortorder = $_GET['sortorder']; setcookie("sortorder", $gSortorder, 2147483000); break;
	default: 
	switch($_COOKIE['sortorder'])
	{
		case 'ASC': case 'DESC': $gSortorder = $_COOKIE['sortorder']; break;
		default: $gSortorder = 'ASC'; break;
	}
	break;
}

switch($_GET['rowamount'])
{
	case '30': case '100': case '250': case '500': case '1000': $gRowamount = $_GET['rowamount']; setcookie("rowamount", $gRowamount, 2147483000); break;
	default:
	switch($_COOKIE['rowamount'])
	{
		case '30': case '100': case '250': case '500': case '1000': $gRowamount = $_COOKIE['rowamount']; break;
		default: $gRowamount = '30'; break;
		
	}
	break;
}

$pCount = ceil($uCount / $gRowamount);
$iTablepage = intval($_GET['tablepage']);
$iTablepageC = intval($_COOKIE['tablepage']);

if($iTablepage > 0 && $iTablepage <= $pCount)
{
	$gTablepage = $iTablepage;
	setcookie("tablepage", $gTablepage, 2147483000);
}
else if($iTablepageC > 0 && $iTablepageC <= $pCount)
{
	$gTablepage = $iTablepageC;
}
else { $gTablepage = 1; }

$HTML_body = '<div class="cap">KULLANICI YÖNETİMİ</div><div class="inner">';
$sform = '
<form name="userlistForm" method="get" action="index.php">
<input type="hidden" name="page" value="edituser" />
<table width="380" align="center" border="1" cellspacing="0" cellpadding="4">
  <tr>
    <td>Sütun Adı</td>
	<td>Sıralama Düzeni</td>
	<td>Satır Miktarı</td>
  </tr>
  <tr>
    <td>
	  <select name="sortfor">
	    <option value="username">Kullanıcı Adı</option>
	    <option value="adminstate">Yetki</option>
		<option value="regdate">Üyelik Tarihi</option>
		<option value="etkin">Etkin mi?</option>
		<option value="lastclickdate">Son Click Tarihi</option>
		<option value="clickcount">Click Sayısı</option>
	  </select>
	</td>
	<td>
	  <select name="sortorder">
	    <option value="ASC">Artan</option>
	    <option value="DESC">Azalan</option>
	  </select>
	</td>
    <td>
	  <select name="rowamount">
	    <option value="30">30</option>
	    <option value="100">100</option>
		<option value="250">250</option>
		<option value="500">500</option>
		<option value="1000">1000</option>
	  </select>
	</td>
  </tr>
</table>
<input value="Sırala" type="submit" />
</form>';

$sform = str_replace('value="'.$gSortfor.'"', 'selected value="'.$gSortfor.'"', $sform);
$sform = str_replace('value="'.$gSortorder.'"', 'selected value="'.$gSortorder.'"', $sform);
$HTML_body .= str_replace('value="'.$gRowamount.'"', 'selected value="'.$gRowamount.'"', $sform).'<br />
Gösterilen: '.strval($gRowamount*$gTablepage-$gRowamount+1).' - '.strval(min($gRowamount*$gTablepage, $uCount)).' / '.$uCount.'<br />
<table class="usertable" width="750" align="center" border="1" cellspacing="0">
  <tr>
    <th>&#35;</th>
	<th>Kullanıcı Adı</th>
  	<th>Yetki</th>
	<th>Üyelik Tarihi</th>
	<th>Etkin mi?</th>
	<th>Son Click Tarihi</th>
	<th>Click Sayısı</th>
  </tr>
';
$yetkiText[0] = "Normal Üye";
$yetkiText[1] = "Premium Üye";
$yetkiText[2] = "Yavru Admin";
$yetkiText[3] = "Baş Admin";
$actiText[0] = "<span class='spanred'>Hayır</span>";
$actiText[1] = "<span class='spangreen'>Evet</span>";

$data = $Database->GetUserlist($gSortfor, $gSortorder, $gRowamount, $gTablepage);

$listId = $gRowamount*$gTablepage - $gRowamount;
for($i=1; $i<=$gRowamount; $i++)
{
	$listId += 1;
	if($row = mysql_fetch_array($data))
	{
		$HTML_body .= '
  <tr>
    <td>'.$listId.'</td>
	<td>'.mb_substr(urldecode($row['username']), 0, 30).'</td>
	<td>'.$yetkiText[$row['adminstate']].'</td>
	<td>'.date("d.m.Y H:i", $row['regdate']).'</td>
	<td>'.$actiText[$row['etkin']].'</td>
	<td>'.date("d.m.Y H:i", strtotime($row['lastclickdate'])).'</td>
	<td>'.$row['clickcount'].'</td>
  </tr>';
	}
}
$HTML_taban = '</table><br/>
Sayfa: &#124;';

for($i=1; $i<=$pCount; $i++)
{
	$HTML_taban .= ' <a class="edituser-a" href="index.php?page=edituser&tablepage='.$i.'">'.$i.'</a> &#124;';
}
$HTML_body .= str_replace('<a class="edituser-a" href="index.php?page=edituser&tablepage='.$gTablepage.'">', '<a class="edituser-a-sec" href="index.php?page=edituser&tablepage='.$gTablepage.'">', $HTML_taban);
$HTML_body .= "</div>";
?>