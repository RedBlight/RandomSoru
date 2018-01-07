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
$_sorucode = isset($_POST['sorucode']) ? str_replace(" ", "+", $_POST['sorucode']) : 'nonvalid';

$html_soru = "";

if( $DBcon = mysql_pconnect("##DBserver##", "##DBuser##", "##DBpass##") ) {
  if( mysql_select_db("##DBname##", $DBcon) ) {
    if( $data = mysql_fetch_array(mysql_query("SELECT cookey, adminstate FROM userdata WHERE id = '$_uid'")) ) {
	  if( $data['adminstate'] = '3' ) {
        if( validate_password($_cookey, $data['cookey']) ) { setcookie("cookey", $cookey, 2147483000, '/');
		  if( mysql_query( "UPDATE userdata SET cookey = '".create_hash($cookey, 64)."', clickcount = clickcount+1 WHERE id = '$_uid'" ) ) {
			   
// CODE ARRAY DEFINES
define( "NAME",				0,	true);
define( "DESCRIPTION",		1,	true);
define( "HAVE_GRAPH",		2,	true);
define( "HAVE_TOPTEXT",		3,	true);
define( "HAVE_SOLUTION",	4,	true);
define( "CODE",				5,	true);
define( "GRAPH_X",			6,	true);
define( "GRAPH_Y",			7,	true);
define( "GRAPH_W",			8,	true);
define( "GRAPH_H",			9,	true);
define( "GRAPH",			10,	true);
define( "TOPTEXT_X",		11,	true);
define( "TOPTEXT_Y",		12,	true);
define( "TOPTEXT_W",		13,	true);
define( "TOPTEXT_H",		14,	true);
define( "TOPTEXT",			15,	true);
define( "TEXT",				16,	true);
define( "CHL",				17,	true);
define( "CHT",				18,	true);
define( "CHF1",				19,	true);
define( "CHF2",				20,	true);
define( "CHF3",				21,	true);
define( "CHF4",				22,	true);
define( "SOLUTION",			23,	true);
// OPT DEFINES
define( "A", 0, true);
define( "B", 1, true);
define( "C", 2, true);
define( "D", 3, true);
define( "E", 4, true);


//MAKE CODE ARRAY
$soruval = explode(":", $_sorucode);
for($i=0; $i<24; $i++)
{
	$soruval[$i] = base64_decode($soruval[$i]);
}

//CHECK AND EVAL PHP
$soruval[CODE] = $Tinyphp->get_tiny(
  $soruval[CODE],
  $replace_variables,
  $remove_whitespace,
  $remove_comments,
  $excludes_array
);
			  
$errors = $Seval->checkScript($soruval[CODE], true);


//ASSEMBLE HTML
if($errors) { $html_soru = '<span class="spanred">'.print_r($Seval->htmlErrors($errors)); }
else
{
	if($soruval[HAVE_TOPTEXT])
	{
		if($soruval[HAVE_GRAPH]) { $soruval[TOPTEXT_X] -= $soruval[GRAPH_W]+$soruval[GRAPH_X]; }
		$soruval[TOPTEXT_W] -= 6;
		$soruval[TOPTEXT_H] -= 6;
	}
	
	$opt = Array( $soruval[CHT], $soruval[CHF1], $soruval[CHF2], $soruval[CHF3], $soruval[CHF4] );
	shuffle($opt);
	$i = A;
	foreach($opt as $cevap)
	{
		if($cevap == $soruval[CHT]){ $answer = $i; }
		$i++;
	}
	
	$html_soru = '<div class="orta" style="border-top-left-radius:0px; width:590px; white-space:nowrap;" align="center"><div class="inner" style="width:570px; height:auto; white-space:nowrap; text-align:left;" align="center">';
	
	if($soruval[HAVE_GRAPH])
	{
		$html_soru .= '<div id="soru_graph" class="soru_graph" style="margin-left:'.$soruval[GRAPH_X].'px; margin-top:'.$soruval[GRAPH_Y].'px; '
		.'min-width:'.$soruval[GRAPH_W].'px; max-width:'.$soruval[GRAPH_W].'px; min-height:'.$soruval[GRAPH_H].'px; max-height:'.$soruval[GRAPH_H].'px;">'
		
		.'<svg id="soru_svg" style="position:relative; margin-left:0px; margin-top:0px;"'
		.'width="'.$soruval[GRAPH_W].'" height="'.$soruval[GRAPH_H].'" xmlns="http://www.w3.org/2000/svg" >'
		.$soruval[GRAPH]
		.'</svg>'
		
		.'</div>'
		;
	}
	
	if($soruval[HAVE_TOPTEXT])
	{
		$html_soru .= '<div id="soru_toptext" class="soru_toptext" style="margin-left:'.$soruval[TOPTEXT_X].'px; margin-top:'.$soruval[TOPTEXT_Y].'px; '
		.'min-width:'.$soruval[TOPTEXT_W].'px; max-width:'.$soruval[TOPTEXT_W].'px; min-height:'.$soruval[TOPTEXT_H].'px; max-height:'.$soruval[TOPTEXT_H].'px;">'
		.$soruval[TOPTEXT]
		.'</div>'
		;
	}
	
	$html_soru .= '<div id="soru_text" class="soru_text">'.$soruval[TEXT].'</div>';
	
	$html_soru .= '<script>
function ShowAnswer(opt) { if(!inAnswer)
{
	letters = new Array();
	letters[0] = "A";
	letters[1] = "B";
	letters[2] = "C";
	letters[3] = "D";
	letters[4] = "E";
	
	if(opt == '.$answer.')
	{
		$("#soru_answer").html("<b>CEVAP: </b><span class=\'spangreen\'>"+letters[opt]+" şıkkı doğru!</span>");
	}
	else
	{
		$("#soru_answer").html("<b>CEVAP: </b><span class=\'spanred\'>"+letters[opt]+" şıkkı yanlış! Doğru cevap "+letters['.$answer.']+" şıkkı olacaktı...</span>");
	}
	
	$("#soru_answer").css("display", "block");
	if('.$soruval[HAVE_SOLUTION].' == "1") { $("#soru_solution").css("display", "block"); }
	
	$("#soru_ch_"+letters[opt]).css("border", "#000000 2px solid");
	
	$("#soru_ch_"+letters[opt]).css("background", "#FFAAAA");
	$("#soru_ch_"+letters['.$answer.']).css("background", "#AAFFAA");
	
	inAnswer = true;
}}
</script>';

	$html_soru .= '<div id="soru_ch_container" class="soru_ch_container" align="left">';
	switch($soruval[CHL])
	{
		case 1:
		$html_soru .=
		'<div id="soru_ch_A" class="soru_ch" onclick="ShowAnswer(\'0\')"><span class="soru_ch_letter">A) </span>'.$opt[A].'</div>'	.'<div id="soru_ch_gap1" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_B" class="soru_ch" onclick="ShowAnswer(\'1\')"><span class="soru_ch_letter">B) </span>'.$opt[B].'</div>'	.'<div id="soru_ch_gap2" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_C" class="soru_ch" onclick="ShowAnswer(\'2\')"><span class="soru_ch_letter">C) </span>'.$opt[C].'</div>'	.'<div id="soru_ch_gap3" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_D" class="soru_ch" onclick="ShowAnswer(\'3\')"><span class="soru_ch_letter">D) </span>'.$opt[D].'</div>'	.'<div id="soru_ch_gap4" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_E" class="soru_ch" onclick="ShowAnswer(\'4\')"><span class="soru_ch_letter">E) </span>'.$opt[E].'</div>'
		.'<script>inAnswer = false;'
		.'chwidth = $("#soru_ch_A").width() + $("#soru_ch_B").width() + $("#soru_ch_C").width() + $("#soru_ch_D").width() + $("#soru_ch_E").width() + 140 + 4;'
		.'gapwidth = Math.floor((570 - chwidth)/4);'
		.'$("#soru_ch_gap1").width(gapwidth); $("#soru_ch_gap2").width(gapwidth); $("#soru_ch_gap3").width(gapwidth); $("#soru_ch_gap4").width(gapwidth);'
		.'</script>';
		break;
		
		case 2:
		$html_soru .=
		'<div id="soru_ch_A" class="soru_ch" onclick="ShowAnswer(\'0\')"><span class="soru_ch_letter">A) </span>'.$opt[A].'</div>'	.'<div id="soru_ch_gap1_1" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_B" class="soru_ch" onclick="ShowAnswer(\'1\')"><span class="soru_ch_letter">B) </span>'.$opt[B].'</div>'	.'<div id="soru_ch_gap1_2" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_C" class="soru_ch" onclick="ShowAnswer(\'2\')"><span class="soru_ch_letter">C) </span>'.$opt[C].'</div>' .'<br />'
		.'<div id="soru_ch_gap2_1" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_D" class="soru_ch" onclick="ShowAnswer(\'3\')"><span class="soru_ch_letter">D) </span>'.$opt[D].'</div>'	.'<div id="soru_ch_gap2_2" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_E" class="soru_ch" onclick="ShowAnswer(\'4\')"><span class="soru_ch_letter">E) </span>'.$opt[E].'</div>' .'<div id="soru_ch_gap2_3" class="soru_ch_gap">&nbsp;</div>'
		.'<script>inAnswer = false;'
		.'chwidth_1 = $("#soru_ch_A").width() + $("#soru_ch_B").width() + $("#soru_ch_C").width() + 84 + 3;'
		.'chwidth_2 = $("#soru_ch_D").width() + $("#soru_ch_E").width() + 56 + 2;'
		.'gapwidth_1 = Math.floor((570 - chwidth_1)/2);'
		.'gapwidth_2 = Math.floor((570 - chwidth_2)/3);'
		.'$("#soru_ch_gap1_1").width(gapwidth_1); $("#soru_ch_gap1_2").width(gapwidth_1);'
		.'$("#soru_ch_gap2_1").width(gapwidth_2); $("#soru_ch_gap2_2").width(gapwidth_2); $("#soru_ch_gap2_3").width(gapwidth_2);'
		.'</script>';
		break;
		
		case 3:
		$html_soru .=
		'<div id="soru_ch_A" class="soru_ch" onclick="ShowAnswer(\'0\')"><span class="soru_ch_letter">A) </span>'.$opt[A].'</div>'	.'<div id="soru_ch_gap1" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_B" class="soru_ch" onclick="ShowAnswer(\'1\')"><span class="soru_ch_letter">B) </span>'.$opt[B].'</div>'	.'<br />'
		.'<div id="soru_ch_C" class="soru_ch" onclick="ShowAnswer(\'2\')"><span class="soru_ch_letter">C) </span>'.$opt[C].'</div>' .'<div id="soru_ch_gap2" class="soru_ch_gap">&nbsp;</div>'
		.'<div id="soru_ch_D" class="soru_ch" onclick="ShowAnswer(\'3\')"><span class="soru_ch_letter">D) </span>'.$opt[D].'</div>'	.'<br />'
		.'<div id="soru_ch_gap3_1" class="soru_ch_gap">&nbsp;</div>'	.'<div id="soru_ch_E" class="soru_ch" onclick="ShowAnswer(\'4\')"><span class="soru_ch_letter">E) </span>'.$opt[E].'</div>'
		.'<div id="soru_ch_gap3_2" class="soru_ch_gap">&nbsp;</div>'
		.'<script>inAnswer = false;'
		.'max_1 = Math.max( $("#soru_ch_A").width(), $("#soru_ch_C").width() );'
		.'max_2 = Math.max( $("#soru_ch_B").width(), $("#soru_ch_D").width() );'
		.'gapwidth_x = 570 - max_1 - max_2 - 56;'
		.'gapwidth_1 = max_1 - $("#soru_ch_A").width() + gapwidth_x;'
		.'gapwidth_2 = max_1 - $("#soru_ch_C").width() + gapwidth_x;'
		.'gapwidth_3 = Math.floor( (570 - $("#soru_ch_E").width() - 28 - 1)/2 );'
		.'$("#soru_ch_gap1").width(gapwidth_1); $("#soru_ch_gap2").width(gapwidth_2); $("#soru_ch_gap3_1").width(gapwidth_3); $("#soru_ch_gap3_2").width(gapwidth_3);'
		.'</script>';
		break;
		
		case 4:
		$html_soru .=
		'<div id="soru_ch_A" class="soru_ch" onclick="ShowAnswer(\'0\')"><span class="soru_ch_letter">A) </span>'.$opt[A].'</div>'	.'<br />'
		.'<div id="soru_ch_B" class="soru_ch" onclick="ShowAnswer(\'1\')"><span class="soru_ch_letter">B) </span>'.$opt[B].'</div>'	.'<br />'
		.'<div id="soru_ch_C" class="soru_ch" onclick="ShowAnswer(\'2\')"><span class="soru_ch_letter">C) </span>'.$opt[C].'</div>' .'<br />'
		.'<div id="soru_ch_D" class="soru_ch" onclick="ShowAnswer(\'3\')"><span class="soru_ch_letter">D) </span>'.$opt[D].'</div>'	.'<br />'
		.'<div id="soru_ch_E" class="soru_ch" onclick="ShowAnswer(\'4\')"><span class="soru_ch_letter">E) </span>'.$opt[E].'</div>' .'<br />'
		.'<script>inAnswer = false;</script>';
		break;
	}
	$html_soru .= '</div>';
	$html_soru .= '<div id="soru_answer" class="soru_answer">&nbsp;</div>';
	
	if($soruval[HAVE_SOLUTION])
	{
		$html_soru .= '<div id="soru_solution" class="soru_solution"><b>ÇÖZÜM:</b><br /> <span style="font-size:7px;"><br />&nbsp;</span>'
		.$soruval[SOLUTION]
		.'</div>'
		;
	}
	
	//Close container
	$html_soru .= '</div></div><span>';
	
	$i=0;
	foreach($Seval->var_token as $varname)
	{
		if(!is_array($Seval->var_val[$i]))
		{
			$html_soru = str_replace('{'.$varname.'}', $Seval->var_val[$i], $html_soru);
		}
		$i++;
	}
}
          } else { $html_soru = "<span class='spanred'>Hata! Yeni cookey yazılamadı, işlem yarıda kesildi."; }
        } else { $html_soru = "<span class='spanred'>Hata! İzinsiz erişim."; }
      } else { $html_soru = "<span class='spanred'>Hata! İzinsiz erişim."; }
    } else { $html_soru = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
  } else { $html_soru = "<span class='spanred'>Hata! Erişim sağlanamadı."; }
} else { $html_soru = "<span class='spanred'>Hata! Erişim sağlanamadı."; }

$html_soru .= "<span class='timeinfo'><p>İşlem Süresi: ".$Timer->GetTotalTime("milli")." ms</p></span></span>";

echo $html_soru;

?>