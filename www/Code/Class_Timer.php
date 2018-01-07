<?
class Timer
{
	public $firstTime;
	public $deltaTime;
	public $deltaList;

	function GetMicrotime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	function StartTimer()
	{
		$this->firstTime = $this->GetMicrotime();
		$this->deltaTime = $this->firstTime;
	}
	
	function GetDeltaTime()
	{
		$delta = $this->GetMicrotime() - $this->deltaTime;
		$this->deltaTime = $this->GetMicrotime();
		return round((float)$delta*1000);;
	}
	
	function AddDelta($label)
	{
		$this->deltaList[$label] = $this->GetDeltaTime();
	}
	
	function GetTotalTime($format)
	{
		$timeTotal = $this->GetMicrotime() - $this->firstTime;
		switch($format)
		{
			default:
			case "second":	$timeTotal = round( (float)$timeTotal, 3 );		break;
			case "milli":	$timeTotal = round((float)$timeTotal*1000);		break;
			case "micro":	$timeTotal = round((float)$timeTotal*1000000);	break;
		}
		return $timeTotal;
	}
	
	function PrintDeltaList()
	{
		asort($this->deltaList);
		echo '<br /><br />
		-- TIME REPORT --
		<table align="center" border="1">
		';
		foreach($this->deltaList as $key=>$value)
		{
			echo "<tr align='left'><td>$key:</td><td>$value ms</td></tr>";
		}
		echo "<tr align='left'><td>TOTAL:</td><td>".$this->GetTotalTime('milli')." ms</td></tr></table>";
	}
}
?>