<?
$sStatus = $Database->siteData['status'];
$sRegister = $Database->siteData['register'];
$sForgot = $Database->siteData['forgot'];
$_AMSG['editsite'] = isset($_AMSG['editsite']) ? $_AMSG['editsite'] : '';

$HTML_body = '<div class="cap">SİTE YÖNETİMİ</div><div class="inner">
<form name="editsite" method="post" action="index.php?page=editsite&action=editsite">
<table align="center" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <th align="right">&nbsp;</th>
    <th align="center"><span class="spangreen">Açık</span></th>
    <th align="center"><span class="spanred">Kapalı</span></th>
  </tr>';
  
$hStatus = '<tr>
    <td align="right">Site Erişimi:</td>
    <td align="center"><input name="status" type="radio" value="1"></td>
    <td align="center"><input name="status" type="radio" value="0"></td>
  </tr>';
$hStatus = str_replace("\"$sStatus\"", "\"$sStatus\" checked", $hStatus);

$hRegister = '<tr>
    <td align="right">Üyelik Alımı:</td>
    <td align="center"><input name="register" type="radio" value="1"></td>
    <td align="center"><input name="register" type="radio" value="0"></td>
  </tr>';
$hRegister = str_replace("\"$sRegister\"", "\"$sRegister\" checked", $hRegister);

$hForgot = '<tr>
    <td align="right">Şifre Sıfırlama:</td>
    <td align="center"><input name="forgot" type="radio" value="1"></td>
    <td align="center"><input name="forgot" type="radio" value="0"></td>
  </tr>';
$hForgot = str_replace("\"$sForgot\"", "\"$sForgot\" checked", $hForgot);

$HTML_body .= $hStatus.$hRegister.$hForgot.'
</table><br>
<input value="Kaydet" type="submit" />
</form>
<br />'.$_AMSG['editsite']."<br /><br /><br /><br /><br /></div>";
?>