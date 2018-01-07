<?
$data = $Database->GetKonuData();

//Başlangıç
$HTML_body = '<div class="cap" id="cap">SORU YÖNETİMİ<br />
  <div class="ed_nav" id="ed_nav">
	  Görüntülenen Ders: <select name="ders" onchange="GetListKonu(this.value)">
	  <option value="mat">Matematik</option>
	  <option value="geo">Geometri</option>
	  <option value="fiz">Fizik</option>
	  <option value="kim">Kimya</option>
	  <option value="biy">Biyoloji</option>
	  </select>
  </div>
</div>
<div class="inner">
  <script>
  $(document).ready(function()
  {
	  GetListKonu("mat");
  });
  </script>
  <div id="derslist" class="tex2jax_ignore"></div>
</div>';
?>