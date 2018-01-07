jQuery.cachedScript = function(url, options) {
	options = $.extend(options || {}, {
		dataType: "script",
		cache: true,
		url: url
	});
	return jQuery.ajax(options);
};

var dText = new Array();
dText["mat"] = 'Matematik';
dText["geo"] = 'Geometri'; 
dText["fiz"] = 'Fizik'; 
dText["kim"] = 'Kimya'; 
dText["biy"] = 'Biyoloji';
dText["non"] = 'Boştaki Sorular';

inAjax = false;
inAction = false;

konuHtmlPrev = "";
buttonsHtmlPrev = "";

id_to_change = "derslist";

function AjaxChangeInner(innerhtml){ document.getElementById(id_to_change).innerHTML = innerhtml; }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// EDIT SORU
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
function GetListKonu(ders) { if(!inAjax)
{
	newNavHtml = 'Görüntülenen Ders: <select name="ders" onchange="GetListKonu(this.value)">';
	newNavHtml += '<option value="mat">Matematik</option> <option value="geo">Geometri</option> <option value="fiz">Fizik</option>'
	newNavHtml += '<option value="kim">Kimya</option> <option value="biy">Biyoloji</option> <option value="non">Boştaki Sorular</option></select>';
	newNavHtml = newNavHtml.replace('"'+ders+'"', '"'+ders+'" selected');
	$("#ed_nav").html(newNavHtml);
	id_to_change = "derslist";
	inAjax = true;
	if(ders == 'non')
	{
		$("#derslist").load(
			"Ajax/editkonu_ajax.php?"
			+"uid="+getCookie("id")
			+"&cookey="+getCookie("cookey")
			+"&action=none"
			+"&ders="+ders
			+"&ind=1"
		);
	}
	else
	{
		$("#derslist").load(
			"Ajax/editders_ajax.php?"
			+"uid="+getCookie("id")
			+"&cookey="+getCookie("cookey")
			+"&action=none"
			+"&ders="+ders
		);
	}
}}

function AddNewKonu(ders){ if(!inAjax)
{
	id_to_change = "amsg";
	inAjax = true;
	$("#derslist").load(
		"Ajax/editders_ajax.php?"
		+"uid="+getCookie("id")
		+"&cookey="+getCookie("cookey")
		+"&action=addnewkonu"
		+"&ders="+ders
	);
}}

function DoSwapKonu(action, ders, ind){ if(!inAjax)
{
	id_to_change = "amsg";
	inAjax = true;
	$("#derslist").load(
		"Ajax/editders_ajax.php?"
		+"uid="+getCookie("id")
		+"&cookey="+getCookie("cookey")
		+"&action="+action
		+"&ders="+ders
		+"&ind="+ind
	);
}}

function DoRenameKonu(action, ders, ind){ if(!inAjax)
{
	switch(action)
	{
		case "ask":
			if(!inAction)
			{	
				konuHtmlPrev = $("#konu_"+ind).html();
				buttonsHtmlPrev = $("#buttons_"+ind).html().replace('text-decoration: underline', 'text-decoration: none');
				konu = $("#konu_"+ind).text();
				konuHtml = '<input style="text-align:center; width:255px;" type="text" id="newname" maxlength="150" />'
				buttonsHtml = '<b>Bu konunun adını değiştirmek istediğinize emin misiniz?</b><br/>';
				buttonsHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoRenameKonu(\'do\', \''+ders+'\', \''+ind+'\')">';
				buttonsHtml += '<span class="symgreen">&#10004; </span><span id="ed_button_inline">Onayla</span></div>';
				buttonsHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoRenameKonu(\'decline\', \''+ders+'\', \''+ind+'\')">';
				buttonsHtml += '<span class="symred">&#8709; </span><span id="ed_button_inline">Vazgeç</span></div>';
				$("#konu_"+ind).html(konuHtml);
				$("#buttons_"+ind).html(buttonsHtml);
				$("#amsg").html("");
				$("#newname").val(konu);
				inAction = true;
			}
		break;
		
		case "decline":
			if(inAction)
			{
				$("#konu_"+ind).html(konuHtmlPrev);
				$("#buttons_"+ind).html(buttonsHtmlPrev);
				$("#amsg").html("");
				inAction = false;
			}
		break;
		
		case "do":
			if(inAction)
			{
				newname = encodeURI( B64.encode( $("#newname").val() ) );
				id_to_change = "amsg";
				inAjax = true;
				$("#derslist").load(
					"Ajax/editders_ajax.php?"
					+"uid="+getCookie("id")
					+"&cookey="+getCookie("cookey")
					+"&action=rename"
					+"&ders="+ders
					+"&ind="+ind
					+"&newname="+newname
				);
			}
		break;
	}
}}

function DoDeleteKonu(action, ders, ind){ if(!inAjax)
{
	switch(action)
	{
		case "ask":
			if(!inAction)
			{	
				buttonsHtmlPrev = $("#buttons_"+ind).html().replace('text-decoration: underline', 'text-decoration: none');
				buttonsHtml = '<b>Bu konuyu silmek istediğinize emin misiniz?</b><br/>';
				buttonsHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoDeleteKonu(\'do\', \''+ders+'\', \''+ind+'\')">';
				buttonsHtml += '<span class="symgreen">&#10004; </span><span id="ed_button_inline">Onayla</span></div>';
				buttonsHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoDeleteKonu(\'decline\', \''+ders+'\', \''+ind+'\')">';
				buttonsHtml += '<span class="symred">&#8709; </span><span id="ed_button_inline">Vazgeç</span></div>';
				$("#buttons_"+ind).html(buttonsHtml);
				$("#amsg").html("");
				inAction = true;
			}
		break;
		
		case "decline":
			if(inAction)
			{
				$("#buttons_"+ind).html(buttonsHtmlPrev);
				$("#amsg").html("");
				inAction = false;
			}
		break;
		
		case "do":
			if(inAction)
			{
				id_to_change = "amsg";
				inAjax = true;
				$("#derslist").load(
					"Ajax/editders_ajax.php?"
					+"uid="+getCookie("id")
					+"&cookey="+getCookie("cookey")
					+"&action=delete"
					+"&ders="+ders
					+"&ind="+ind
				);
			}
		break;
	}
}}



//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// EDIT KONU
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
function GetListSoru(ders, ind){ if(!inAjax)
{
	konuName = $("#konu_"+ind).html();
	if(konuName == null) { konuName = $("#konuname").html(); }
	newNavHtml = 'Görüntülenen Konu: <span style="font-weight:normal;">';
	newNavHtml += '<span class="ed_navlink" onclick="GetListKonu(\''+ders+'\')">'+dText[ders]+'</span> &gt; <span id="konuname">'+konuName+'</span>'
	newNavHtml += '</span>';
	$("#ed_nav").html(newNavHtml);
	id_to_change = "derslist";
	inAjax = true;
	$("#derslist").load(
		"Ajax/editkonu_ajax.php?"
		+"uid="+getCookie("id")
		+"&cookey="+getCookie("cookey")
		+"&action=none"
		+"&ders="+ders
		+"&ind="+ind
	);
}}

function AddNewSoruById(action, ders, ind){ if(!inAjax)
{
	switch(action)
	{
		case "ask":
			if(!inAction)
			{
				oldAddbyidHtml = $("#addbyid").html().replace('text-decoration: underline', 'text-decoration: none');
				newAddbyidHtml = 'Eklemek istediğiniz sorunun ID\'sini yazın: &nbsp; <input style="text-align:center; width:70px;" type="text" id="addunsid" maxlength="6" />';
				newAddbyidHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="AddNewSoruById(\'do\', \''+ders+'\', \''+ind+'\')">';
				newAddbyidHtml += '<span class="symgreen">&#10004; </span><span id="ed_button_inline">Ekle</span></div>';
				newAddbyidHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="AddNewSoruById(\'decline\', \''+ders+'\', \''+ind+'\')">';
				newAddbyidHtml += '<span class="symred">&#8709; </span><span id="ed_button_inline">Vazgeç</span></div>';
				$("#addbyid").html(newAddbyidHtml);
				$("#amsg").html("");
				inAction = true;
			}
		break;
		
		case "decline":
			if(inAction)
			{
				$("#addbyid").html(oldAddbyidHtml);
				inAction = false;
			}
		break;
		
		case "do":
			if(inAction)
			{
				valAddunsid = encodeURI( B64.encode( $("#addunsid").val() ) );
				id_to_change = "amsg";
				inAjax = true;
				$("#derslist").load(
					"Ajax/editkonu_ajax.php?"
					+"uid="+getCookie("id")
					+"&cookey="+getCookie("cookey")
					+"&action=addnewsorubyid"
					+"&ders="+ders
					+"&ind="+ind
					+"&addunsid="+valAddunsid
				);
			}
		break;
	}
}}

function AddNewSoru(ders, ind){ if(!inAjax)
{
	id_to_change = "amsg";
	inAjax = true;
	$("#derslist").load(
		"Ajax/editkonu_ajax.php?"
		+"uid="+getCookie("id")
		+"&cookey="+getCookie("cookey")
		+"&action=addnewsoru"
		+"&ders="+ders
		+"&ind="+ind
	);
}}

function DoSwapSoru(action, ders, ind, sid, sind){ if(!inAjax)
{
	id_to_change = "amsg";
	inAjax = true;
	$("#derslist").load(
		"Ajax/editkonu_ajax.php?"
		+"uid="+getCookie("id")
		+"&cookey="+getCookie("cookey")
		+"&action="+action
		+"&ders="+ders
		+"&ind="+ind
		+"&sid="+sid
		+"&sind="+sind
	);
}}

function DoDeleteSoru(action, ders, ind, sid, sind){ if(!inAjax)
{
	switch(action)
	{
		case "ask":
			if(!inAction)
			{	
				buttonsHtmlPrev = $("#buttons_"+sind).html().replace('text-decoration: underline', 'text-decoration: none');
				buttonsHtml = '<b>Bu soruyu boşa almak istediğinize emin misiniz?</b><br/>';
				buttonsHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoDeleteSoru(\'do\', \''+ders+'\', \''+ind+'\', \''+sid+'\', \''+sind+'\')">';
				buttonsHtml += '<span class="symgreen">&#10004; </span><span id="ed_button_inline">Onayla</span></div>';
				buttonsHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoDeleteSoru(\'decline\', \''+ders+'\', \''+ind+'\', \''+sid+'\', \''+sind+'\')">';
				buttonsHtml += '<span class="symred">&#8709; </span><span id="ed_button_inline">Vazgeç</span></div>';
				$("#buttons_"+sind).html(buttonsHtml);
				$("#amsg").html("");
				inAction = true;
			}
		break;
		
		case "decline":
			if(inAction)
			{
				$("#buttons_"+sind).html(buttonsHtmlPrev);
				$("#amsg").html("");
				inAction = false;
			}
		break;
		
		case "do":
			if(inAction)
			{
				id_to_change = "amsg";
				inAjax = true;
				$("#derslist").load(
					"Ajax/editkonu_ajax.php?"
					+"uid="+getCookie("id")
					+"&cookey="+getCookie("cookey")
					+"&action=delete"
					+"&ders="+ders
					+"&ind="+ind
					+"&sid="+sid
					+"&sind="+sind
				);
			}
		break;
	}
}}
function DoEraseSoru(action, ders, ind, sid, sind){ if(!inAjax)
{
	switch(action)
	{
		case "ask":
			if(!inAction)
			{
				buttonsHtmlPrev = $("#buttons_"+sind).html().replace('text-decoration: underline', 'text-decoration: none');
				buttonsHtml = '<b>Soru kalıcı olarak silinecek! Devam edilsin mi?</b><br/>';
				buttonsHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoEraseSoru(\'do\', \''+ders+'\', \''+ind+'\', \''+sid+'\', \''+sind+'\')">';
				buttonsHtml += '<span class="symgreen">&#10004; </span><span id="ed_button_inline">Onayla</span></div>';
				buttonsHtml += '<div class="ed_button" onmouseover="Btn_onmouseover(this)" onmouseout="Btn_onmouseout(this)" onclick="DoEraseSoru(\'decline\', \''+ders+'\', \''+ind+'\', \''+sid+'\', \''+sind+'\')">';
				buttonsHtml += '<span class="symred">&#8709; </span><span id="ed_button_inline">Vazgeç</span></div>';
				$("#buttons_"+sind).html(buttonsHtml);
				$("#amsg").html("");
				inAction = true;
			}
		break;
		
		case "decline":
			if(inAction)
			{
				$("#buttons_"+sind).html(buttonsHtmlPrev);
				$("#amsg").html("");
				inAction = false;
			}
		break;
		
		case "do":
			if(inAction)
			{
				id_to_change = "amsg";
				inAjax = true;
				$("#derslist").load(
					"Ajax/editkonu_ajax.php?"
					+"uid="+getCookie("id")
					+"&cookey="+getCookie("cookey")
					+"&action=erase"
					+"&ders="+ders
					+"&ind="+ind
					+"&sid="+sid
					+"&sind="+sind
				);
			}
		break;
	}
}}

function DoEditSoru(ders, ind, sid, sind){ if(!inAjax)
{
	konuName = $("#konuname").html();
	soruName = $("#soru_"+sind).html();
	newNavHtml = 'Görüntülenen Soru: <span style="font-weight:normal;">';
	newNavHtml += '<span class="ed_navlink" onclick="GetListKonu(\''+ders+'\')">'+dText[ders]+'</span> &gt; ';
	if(ders != 'non') { newNavHtml += '<span class="ed_navlink" onclick="GetListSoru(\''+ders+'\', \''+ind+'\')" id="konuname">'+konuName+'</span> &gt; '; }
	newNavHtml += '<span id="soruname">'+soruName+'</span>';
	newNavHtml += '</span>';
	$("#ed_nav").html(newNavHtml);
	id_to_change = "derslist";
	inAjax = true;
	$.cachedScript("Javascript/ace.js").done(function(script, textStatus) {
		$("#derslist").load(
			"Ajax/editsoru_load_ajax.php?"
			+"uid="+getCookie("id")
			+"&cookey="+getCookie("cookey")
			+"&sid="+sid
		);
	});
}}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// SORU				
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
function GetSoru_Single(ders, konu)
{
	id_to_change = "inner";
	inAjax = true;
	$("#inner").load(
		"Ajax/getsoru_single.php?"
		+"uid="+getCookie("id")
		+"&cookey="+getCookie("cookey")
		+"&ders="+ders
		+"&konu="+konu,{cache: false}
	);
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// GENERAL				
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
$(document).ready(function()
{
	if ($(".sol")[0])
	{
		$(".orta").css( "border-top-left-radius", "0px" );
		$(".sol").width(180 - 9);
		$(".orta").width(600 - 10);
		$(".sag").width(180 - 26);
	}
	else
	{
		$(".orta").width(780 - 10);
		$(".sag").width(180 - 26);
	}
	
	minh = Math.max($(".sol").height(), $(".sag").height())+30;
	$(".inner").height("auto");
	$(".inner").css("min-height", String(minh)+"px");
	
	$(document).ajaxSend(function(e, xhr, opt){ AjaxChangeInner('Talep işleniyor <img src="../ajaxloading.gif" width="16" height="11" alt="Loading..." />'); });
	$(document).ajaxError(function(e, xhr, opt){ AjaxChangeInner('<span class="spanred">Hata! Bağlantı sağlanamadı.</span>'); });
	$(document).ajaxComplete(function(e, xhr, opt)
	{
		inAction = false;
		inAjax = false;
		
		MathJax.Hub.Queue(["Typeset", MathJax.Hub, id_to_change]);
	});
});

function Btn_onmouseover(elem)
{
	$(elem).css("cursor", "pointer");
	$(elem).children("#ed_button_inline").css("text-decoration", "underline");
}
function Btn_onmouseout(elem)
{
	$(elem).css("cursor", "default");
	$(elem).children("#ed_button_inline").css("text-decoration", "none");
}