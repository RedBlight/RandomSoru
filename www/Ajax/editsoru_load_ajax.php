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
$_sid = isset($_GET['sid']) ? intval(urlencode(htmlentities($_GET['sid']))) : -5;

$html = "";
$html_1 = "";
$html_2 = "";
$html_3 = "";
$html_4 = "";
$html_5 = "";
$amsg = "<span>";

if( $DBcon = mysql_pconnect("##DBserver##", "##DBuser##", "##DBpass##") ) {
  if( mysql_select_db("##DBname##", $DBcon) ) {
    if( $data = mysql_fetch_array(mysql_query("SELECT cookey, adminstate FROM userdata WHERE id = '$_uid'")) ) {
	  if( $data['adminstate'] = '3' ) {
        if( validate_password($_cookey, $data['cookey']) ) { setcookie("cookey", $cookey, 2147483000, '/');
		  if( mysql_query( "UPDATE userdata SET cookey = '".create_hash($cookey, 64)."', clickcount = clickcount+1 WHERE id = '$_uid'" ) ) {
		    if( $soruData = mysql_fetch_array(mysql_query( "SELECT * FROM sorudata WHERE id = '$_sid'")) ) {

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

$soruval = explode(":", $soruData['code']);
array_unshift($soruval, $soruData['name'], $soruData['description']);

for($i=0; $i<24; $i++)
{
	$soruval[$i] = htmlentities(base64_decode($soruval[$i]), ENT_COMPAT, "UTF-8");
}

$soruValidateCode = base64_encode( create_hash($soruData['name'].":".$soruData['description'].":".$soruData['code'], 10)."::".$_sid );

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// BEGIN HTML
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
$html_1 = '
<div style="display:none" id="svlcd">'.$soruValidateCode.'</div>
<div class="es_tab es_intab" id="es_tab_general" onclick="MakeVisible(\'general\')" >Genel</div>
<div class="es_tab" id="es_tab_code" onclick="MakeVisible(\'code\')" >Kod</div>
<div class="es_tab" id="es_tab_graph" onclick="MakeVisible(\'graph\')" >Grafik</div>
<div class="es_tab" id="es_tab_toptext" onclick="MakeVisible(\'toptext\')" >Üst Metin</div>
<div class="es_tab" id="es_tab_text" onclick="MakeVisible(\'text\')" >Soru Metni</div>
<div class="es_tab" id="es_tab_choices" onclick="MakeVisible(\'choices\')" >Şıklar</div>
<div class="es_tab" id="es_tab_solution" onclick="MakeVisible(\'solution\')" >Çözüm</div>
<br />

<div class="es_cont es_viscont" id="es_cont_general">
    Soru ismi:<br />
    <textarea name="soru_name" class="es_general_tarea name" id="es_general_name">'.$soruval[NAME].'</textarea><br />
    <br />
    Açıklama:<br />
    <textarea name="soru_description" class="es_general_tarea description" id="es_general_description">'.$soruval[DESCRIPTION].'</textarea><br />
    <br />
    Soru Zenginliği:<br />';
$html_2 = '
    <table align="left" style="font-size:12px" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td align="right">Grafik:</td>
        <td align="center"><input name="haveGraph" type="radio" value="1" onclick="ShowTab(\'graph\', true)"><span class="spangreen">Var</span></td>
        <td align="center"><input name="haveGraph" type="radio" value="0" onclick="ShowTab(\'graph\', false)"><span class="spanred">Yok</span></td>
      </tr><tr>
        <td align="right">Üst Metin:</td>
        <td align="center"><input name="haveToptext" type="radio" value="1" onclick="ShowTab(\'toptext\', true)"><span class="spangreen">Var</span></td>
        <td align="center"><input name="haveToptext" type="radio" value="0" onclick="ShowTab(\'toptext\', false)"><span class="spanred">Yok</span></td>
      </tr><tr>
        <td align="right">Çözüm:</td>
        <td align="center"><input name="haveSolution" type="radio" value="1" onclick="ShowTab(\'solution\', true)"><span class="spangreen">Var</span></td>
        <td align="center"><input name="haveSolution" type="radio" value="0" onclick="ShowTab(\'solution\', false)"><span class="spanred">Yok</span></td>
      </tr>
    </table>';
if($soruval[HAVE_GRAPH])	{ $html_2 = str_replace('graph\', true)"', 'graph\', true)" checked="checked"', $html_2 ) ; }
else						{ $html_2 = str_replace('graph\', false)"', 'graph\', false)" checked="checked"', $html_2 ) ; }
if($soruval[HAVE_TOPTEXT])	{ $html_2 = str_replace('toptext\', true)"', 'toptext\', true)" checked="checked"', $html_2 ) ; }
else						{ $html_2 = str_replace('toptext\', false)"', 'toptext\', false)" checked="checked"', $html_2 ) ; }
if($soruval[HAVE_SOLUTION])	{ $html_2 = str_replace('solution\', true)"', 'solution\', true)" checked="checked"', $html_2 ) ; }
else						{ $html_2 = str_replace('solution\', false)"', 'solution\', false)" checked="checked"', $html_2 ) ; }

$html_3 = '
    <div id="es_general_buttons" align="right">
    <br /><br /><br />
    <div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="PreviewSoru()">
	<span class="symorange">&#8747; </span><span id="ed_button_inline">Soruyu Önizle</span></div>
    &nbsp;&nbsp;&thinsp;
    <div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="SaveSoru(\'ask\')">
	<span class="symgreen">&#10004; </span><span id="ed_button_inline">Kaydet</span></div>
    </div>
    
    <br /><br />
    
    <div id="preview" align="center">
    </div>
</div>


<div class="es_cont" id="es_cont_code">
  <div id="acepad_code">'.$soruval[CODE].'</div>
  <div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="CheckPhpSyntax()">
  <span class="symgreen">&#63; </span><span id="ed_button_inline">Geçerliliği Kontrol Et</span></div>
  <div id="es_code_amsg" class="es_amsg"></div>
</div>


<div class="es_cont" id="es_cont_graph">
  <div id="panel_graph">
    <table class="es_loc_table" align="center" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td align="right">Yatay Konum (X): </td>
        <td><input name="graph_x" id="es_graph_x" class="es_loc_tarea" type="text" value="'.$soruval[GRAPH_X].'" /></td>
        <td width="60">&nbsp;</td>
        <td align="right">Genişlik (W):</td>
        <td><input name="graph_w" id="es_graph_w" class="es_loc_tarea" type="text" value="'.$soruval[GRAPH_W].'" /></td>
      </tr>
      <tr>
        <td align="right">Dikey Konum (Y): </td>
        <td><input name="graph_y" id="es_graph_y" class="es_loc_tarea" type="text" value="'.$soruval[GRAPH_Y].'" /></td>
        <td width="60">&nbsp;</td>
        <td align="right">Yükseklik (H):</td>
        <td><input name="graph_h" id="es_graph_h" class="es_loc_tarea" type="text" value="'.$soruval[GRAPH_H].'" /></td>
      </tr>
    </table>
      
    <div id="acepad_graph">'.$soruval[GRAPH].'</div>
  </div>
</div>


<div class="es_cont" id="es_cont_toptext">
  <div id="panel_toptext">
    <table class="es_loc_table" align="center" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td align="right">Yatay Konum (X): </td>
        <td><input name="toptext_x" id="es_toptext_x" class="es_loc_tarea" type="text" value="'.$soruval[TOPTEXT_X].'" /></td>
        <td width="60">&nbsp;</td>
        <td align="right">Genişlik (W):</td>
        <td><input name="toptext_w" id="es_toptext_w" class="es_loc_tarea" type="text" value="'.$soruval[TOPTEXT_W].'" /></td>
      </tr>
      
      <tr>
        <td align="right">Dikey Konum (Y): </td>
        <td><input name="toptext_y" id="es_toptext_y" class="es_loc_tarea" type="text" value="'.$soruval[TOPTEXT_Y].'" /></td>
        <td width="60">&nbsp;</td>
        <td align="right">Yükseklik (H):</td>
        <td><input name="toptext_h" id="es_toptext_h" class="es_loc_tarea" type="text" value="'.$soruval[TOPTEXT_H].'" /></td>
      </tr>
    </table>
      
    <div id="acepad_toptext">'.$soruval[TOPTEXT].'</div>
  </div>
</div>


<div class="es_cont" id="es_cont_text">
  <div id="acepad_text">'.$soruval[TEXT].'</div>
</div>


<div class="es_cont" id="es_cont_choices">
  Şık Dizilimi: <select name="es_choices_layout" id="es_choices_layout">';
$html_4 = '
  <option value="1">5-0-0-0-0</option>
  <option value="2">3-2-0-0-0</option>
  <option value="3">2-2-1-0-0</option>
  <option value="4">1-1-1-1-1</option>
';
$html_4 = str_replace('"'.$soruval[CHL].'"', '"'.$soruval[CHL].'" selected', $html_4);
$html_5 = '	
  </select><br />
  <br />
  Doğru Cevap:<br />
  <div id="acepad_cht">'.$soruval[CHT].'</div>
  <br />
  
  1. Yanlış Cevap:<br />
  <div id="acepad_chf1">'.$soruval[CHF1].'</div>
  <br />
  
  2. Yanlış Cevap:<br />
  <div id="acepad_chf2">'.$soruval[CHF2].'</div>
  <br />
  
  3. Yanlış Cevap:<br />
  <div id="acepad_chf3">'.$soruval[CHF3].'</div>
  <br />
  
  4. Yanlış Cevap:<br />
  <div id="acepad_chf4">'.$soruval[CHF4].'</div>
  <br />
</div>


<div class="es_cont" id="es_cont_solution">
  <div id="acepad_solution">'.$soruval[SOLUTION].'</div>
</div>

<script>

	var acepad_code = ace.edit("acepad_code");
	var acepad_graph = ace.edit("acepad_graph");
	var acepad_toptext = ace.edit("acepad_toptext");
	var acepad_text = ace.edit("acepad_text");
	var acepad_cht = ace.edit("acepad_cht");
	var acepad_chf1 = ace.edit("acepad_chf1");
	var acepad_chf2 = ace.edit("acepad_chf2");
	var acepad_chf3 = ace.edit("acepad_chf3");
	var acepad_chf4 = ace.edit("acepad_chf4");
	var acepad_solution = ace.edit("acepad_solution");
	
	acepad_code.setTheme("ace/theme/vibrant_ink");
	acepad_graph.setTheme("ace/theme/vibrant_ink");
	acepad_toptext.setTheme("ace/theme/vibrant_ink");
	acepad_text.setTheme("ace/theme/vibrant_ink");
	acepad_cht.setTheme("ace/theme/vibrant_ink");
	acepad_chf1.setTheme("ace/theme/vibrant_ink");
	acepad_chf2.setTheme("ace/theme/vibrant_ink");
	acepad_chf3.setTheme("ace/theme/vibrant_ink");
	acepad_chf4.setTheme("ace/theme/vibrant_ink");
	acepad_solution.setTheme("ace/theme/vibrant_ink");

	acepad_code.getSession().setMode( new AceMode_php() );	
	acepad_graph.getSession().setMode( new AceMode_svg() );
	acepad_toptext.getSession().setMode( new AceMode_html() );
	acepad_text.getSession().setMode( new AceMode_html() );
	acepad_cht.getSession().setMode( new AceMode_html() );
	acepad_chf1.getSession().setMode( new AceMode_html() );
	acepad_chf2.getSession().setMode( new AceMode_html() );
	acepad_chf3.getSession().setMode( new AceMode_html() );
	acepad_chf4.getSession().setMode( new AceMode_html() );
	acepad_solution.getSession().setMode( new AceMode_html() );
	
	$(document).ready(function(){

    function heightUpdateFunction() {

        var newHeight =
                  acepad_code.getSession().getScreenLength()
                  * acepad_code.renderer.lineHeight
                  + acepad_code.renderer.scrollBar.getWidth();

        $("#acepad_code").height(newHeight.toString() + "px");
        acepad_code.resize();
    };

    heightUpdateFunction();

    acepad_code.getSession().on("change", heightUpdateFunction);
	
	});

</script>
';
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// END HTML
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
			} else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı"; }
          } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı"; }
        } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı"; }
      } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı"; }
    } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı"; }
  } else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı"; }
} else { $amsg = "<span class='spanred'>Hata! Erişim sağlanamadı"; }

$html = $html_1.$html_2.$html_3.$html_4.$html_5;
$amsg .= "</span>";
?>
<script type="text/javascript" charset="utf-8">
var b64str = new Array();
inAnswer = false;

require("ace/edit_session").EditSession.prototype.$useWorker = false;
var AceMode_php = require("ace/mode/php").Mode;
var AceMode_svg = require("ace/mode/svg").Mode;
var AceMode_html = require("ace/mode/html").Mode;

function CheckPhpSyntax(){if(!inAjax)
{
	id_to_change = "es_code_amsg";
	inAjax = true;
	$("#es_code_amsg").load(
		"Ajax/checksyntax_code.php", {
		'uid' : getCookie("id"),
		'cookey' : getCookie("cookey"),
		'phpstr' : encodeURI( B64.encode( acepad_code.getValue() ) )
	});
}}

function MakeVisible(elemID)
{
	$("#es_cont_general").removeClass("es_viscont");
	$("#es_cont_code").removeClass("es_viscont");
	$("#es_cont_graph").removeClass("es_viscont");
	$("#es_cont_toptext").removeClass("es_viscont");
	$("#es_cont_text").removeClass("es_viscont");
	$("#es_cont_choices").removeClass("es_viscont");
	$("#es_cont_solution").removeClass("es_viscont");
	$("#es_cont_"+elemID).addClass("es_viscont");
	
	$("#es_tab_general").removeClass("es_intab");
	$("#es_tab_code").removeClass("es_intab");
	$("#es_tab_graph").removeClass("es_intab");
	$("#es_tab_toptext").removeClass("es_intab");
	$("#es_tab_text").removeClass("es_intab");
	$("#es_tab_choices").removeClass("es_intab");
	$("#es_tab_solution").removeClass("es_intab");
	$("#es_tab_"+elemID).addClass("es_intab");
}

function ShowTab(tabName, show)
{
	switch(show)
	{
		case true: $("#es_tab_"+tabName).css("display", "inline-block"); break;
		case false: $("#es_tab_"+tabName).css("display", "none"); break;	
	}
}

function MakeSoruCode()
{
	b64str['name'] = encodeURI( B64.encode( $("#es_general_name").val() ) );
	b64str['description'] = encodeURI( B64.encode( $("#es_general_description").val() ) );
	b64str['haveGraph'] = encodeURI( B64.encode( $('input:radio[name=haveGraph]:checked').val() ) );
	b64str['haveToptext'] = encodeURI( B64.encode( $('input:radio[name=haveToptext]:checked').val() ) );
	b64str['haveSolution'] = encodeURI( B64.encode( $('input:radio[name=haveSolution]:checked').val() ) );
	b64str['code'] = encodeURI( B64.encode( acepad_code.getValue() ) );
	b64str['graph_x'] = encodeURI( B64.encode( $("#es_graph_x").val() ) );
	b64str['graph_y'] = encodeURI( B64.encode( $("#es_graph_y").val() ) );
	b64str['graph_w'] = encodeURI( B64.encode( $("#es_graph_w").val() ) );
	b64str['graph_h'] = encodeURI( B64.encode( $("#es_graph_h").val() ) );
	b64str['graph'] = encodeURI( B64.encode( acepad_graph.getValue() ) );
	b64str['toptext_x'] = encodeURI( B64.encode( $("#es_toptext_x").val() ) );
	b64str['toptext_y'] = encodeURI( B64.encode( $("#es_toptext_y").val() ) );
	b64str['toptext_w'] = encodeURI( B64.encode( $("#es_toptext_w").val() ) );
	b64str['toptext_h'] = encodeURI( B64.encode( $("#es_toptext_h").val() ) );
	b64str['toptext'] = encodeURI( B64.encode( acepad_toptext.getValue() ) );
	b64str['text'] = encodeURI( B64.encode( acepad_text.getValue() ) );
	b64str['chl'] = encodeURI( B64.encode( $("#es_choices_layout").val() ) );
	b64str['cht'] = encodeURI( B64.encode( acepad_cht.getValue() ) );
	b64str['chf1'] = encodeURI( B64.encode( acepad_chf1.getValue() ) );
	b64str['chf2'] = encodeURI( B64.encode( acepad_chf2.getValue() ) );
	b64str['chf3'] = encodeURI( B64.encode( acepad_chf3.getValue() ) );
	b64str['chf4'] = encodeURI( B64.encode( acepad_chf4.getValue() ) );
	b64str['solution'] = encodeURI( B64.encode( acepad_solution.getValue() ) );
	return	b64str['name']
	+ ':' +	b64str['description']
	+ ':' +	b64str['haveGraph']
	+ ':' +	b64str['haveToptext']
	+ ':' +	b64str['haveSolution']
	+ ':' +	b64str['code']
	+ ':' +	b64str['graph_x']
	+ ':' +	b64str['graph_y']
	+ ':' +	b64str['graph_w']
	+ ':' +	b64str['graph_h']
	+ ':' +	b64str['graph']
	+ ':' +	b64str['toptext_x']
	+ ':' +	b64str['toptext_y']
	+ ':' +	b64str['toptext_w']
	+ ':' +	b64str['toptext_h']
	+ ':' +	b64str['toptext']
	+ ':' +	b64str['text']
	+ ':' +	b64str['chl']
	+ ':' +	b64str['cht']
	+ ':' +	b64str['chf1']
	+ ':' +	b64str['chf2']
	+ ':' +	b64str['chf3']
	+ ':' +	b64str['chf4']
	+ ':' +	b64str['solution']
	;
}

function PreviewSoru(){ if(!inAjax)
{
	id_to_change = "preview";
	inAjax = true;
	
	$("#preview").load(
		"Ajax/makesoru_preview.php", {
		'uid' : getCookie("id"),
		'cookey' : getCookie("cookey"),
		'sorucode' : MakeSoruCode()
	});
}}

function SaveSoru(action){ if(!inAjax)
{
	switch(action)
	{
		case "ask":
			if(!inAction)
			{
				buttonsHtmlPrev = $("#es_general_buttons").html().replace('text-decoration: underline', 'text-decoration: none');
				buttonsHtml = '<br /><br />';
				buttonsHtml += 'Soruyu kaydetmek istediğinize emin misiniz?<br />';
				buttonsHtml += '<div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="SaveSoru(\'do\')">';
				buttonsHtml += '<span class="symgreen">&#10004; </span><span id="ed_button_inline">Onayla</span></div>';
				buttonsHtml += '&nbsp;&nbsp;';
				buttonsHtml += '<div class="ed_button add" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="SaveSoru(\'decline\')">';
				buttonsHtml += '<span class="symred">&#8709; </span><span id="ed_button_inline">Vazgeç</span></div>';
				$("#es_general_buttons").html(buttonsHtml);
				inAction = true;
			}
		break;
		
		case "decline":
			if(inAction)
			{
				$("#es_general_buttons").html(buttonsHtmlPrev);
				inAction = false;
			}
		break;
		
		case "do":
			if(inAction)
			{
				id_to_change = "preview";
				inAjax = true;
				$("#preview").load(
					"Ajax/makesoru_save.php", {
					'uid' : getCookie("id"),
					'cookey' : getCookie("cookey"),
					'svlcd' : $("#svlcd").text(),
					'sorucode' : MakeSoruCode()
					},
					function() //on complete
					{
  						$("#soruname").html($("#es_general_name").val());
						$("#es_general_buttons").html(buttonsHtmlPrev);
					}
				);
				
			}
		break;
	}
}}
</script>
<style type="text/css">
.es_tab {
	user-select: none;
	width: auto; height: auto;
	margin: 0px 3px 0px 3px; padding: 3px 7px 3px 7px;
	border: none; display: inline-block;
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size: 14px; font-weight: normal; color: #000000;
}.es_tab:hover {
	text-decoration: underline;
	cursor: pointer;
}.es_tab:active {
	text-decoration: underline;
	position:relative;
	top:1px;
}
.es_intab {
	font-weight: bold;
	text-decoration: underline;
	color: #00A;
}

.es_cont {
	width: 740px; height: auto;
	text-align: left;
	padding: 10px 5px 10px 5px;
	margin: 0px;
	display: none;
}
.es_viscont {
	display: block;
}

.es_general_tarea {
	margin: 0px;
	min-width: 734px;
	max-width: 734px;
}
.es_general_tarea.name {
	height: 22px;
	word-wrap:break-word;
	word-break: normal;
}
.es_general_tarea.description {
	height: 110px;
	word-wrap:break-word;
	word-break: normal;
}


#acepad_code, #acepad_graph, #acepad_toptext, #acepad_text, #acepad_solution {
	width: auto;
	height: auto;
	min-height: 400px;
	white-space: pre !important;
	unicode-bidi: embed !important;
}

#acepad_cht, #acepad_chf1, #acepad_chf2, #acepad_chf3, #acepad_chf4 {
	width: auto;
	height: auto;
	min-height: 100px;
	white-space: pre !important;
	unicode-bidi: embed !important;
}

.es_loc_table {
	font-size: 12px;
}
.es_loc_tarea {
	width: 40px;
}
</style>

<?
echo $html.$amsg;
?>

