<?
$HTML_body = '
<style>
.homelogo {
	width: 770px;
	height: 220px;
	margin: 0px 0px -2px 0px;
	padding: 0px;
    border-top-left-radius: 14px;
	
	background: #3b444b;
	background: -moz-linear-gradient(top, #3b444b 5%, #ffffff 95%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(5%,#3b444b), color-stop(95%,#ffffff));
	background: -webkit-linear-gradient(top, #3b444b 5%,#ffffff 95%);
	background: -o-linear-gradient(top, #3b444b 5%,#ffffff 95%);
	background: -ms-linear-gradient(top, #3b444b 5%,#ffffff 95%);
	background: linear-gradient(to bottom, #3b444b 5%,#ffffff 95%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#3b444b", endColorstr="#ffffff",GradientType=0 );
}
</style>
';

$HTML_body .= '
<div class="homelogo"><img src="biglogo.png" width="770" height="220" /></div>
<div class="inner">
  Eğitimde teknoloji devrimi...<br/><br/><br/>
  Çok yakında sizlerle...<br/><br/><br/>
</div>
<script> $(".inner").css( "box-shadow", "none" ); </script>
';
?>