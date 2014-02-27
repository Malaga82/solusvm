<?php
$time = time() - 60; // or filemtime($fn), etc
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $time).' GMT+1');
header('Refresh: 3600');
$baseurl = "http://la.semoweb.com/api/client";

$online_col = "#309B46";
$offline_col = "#E61E26";
$warn_50 = "#fff36c";
$warn_75 = "#ff802b";
$warn_90 = "#ff2b2b";

$servers = array(
		//repeat as many times as needed, modifying the keys / hashes..
		array(
			"key" => "",
			"hash" => "",
			"action" => "status"
		),
		array(
			"key" => "",
			"hash" => "",
			"action" => "status"
		),		
	);
$servers_reboot = array(
		//this must have the same values as before, it will be used to boot the machine if a vps is offline
		array(
			"key" => "",
			"hash" => "",
			"action" => "boot"
		),
		array(
			"key" => "",
			"hash" => "",
			"action" => "boot"
		),		
	);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Server Statistics :: Information</title>
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noodp" />
        <meta charset="UTF-8" />
        <style type="text/css">
            span { color: #fff;display: block;font-size: 0.7em;margin-bottom: .5em;padding: 0 .5em; }
            html { background-color: #000;color: #777;font-family: sans-serif;font-size: 2em;padding: 1em 2em; }
            #links { float: right;text-align: right; }
            a { color: #68c;display: block;font-size: 1.7em;text-decoration: none; }
            a:hover { color: #b4c9ff;}
            small {	opacity: 0.8;}
			small>small {opacity: 0.5;}
			#servers tr td{padding: 5px; margin: 0;}
        </style>
    </head>
    <body>
	<div align="center" valign="middle"><small>VPS SERVER INFO</small></div>
    <div class="wrapper">
    <table width="100%" border="0" id="servers">
          <tr>
          	<td align="center" valign="middle">server</td>
            <td align="center" valign="middle">ip</td>
            <td align="center" valign="middle">status</td>
            <td align="center" valign="middle">disk</td>
            <td align="center" valign="middle">memory</td>
            <td align="center" valign="middle">bandwidth</td>
          </tr>
   

<?php
foreach ($servers as $server){
	$url = $baseurl . "/command.php?key=".$server['key']."&hash=".$server['hash']."&action=". $server["action"] ."&ipaddr=true&bw=true&mem=true&hdd=true";
	
	$file = fopen ($url, "r");
	if (!$file) {
		die("Unable to open remote file.");
	}
	$data = fgets ($file);
	fclose($file);
	
	preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $match);
	$result = array();
	foreach ($match[1] as $x => $y)
	{
		$result[$y] = $match[2][$x];
	}
	
	$name = $result["hostname"];
	$ip = $result["ipaddress"];
	$status = $result["vmstat"];
	$disk = explode(",", $result["hdd"]);
	$bw = explode(",", $result["bw"]);
	$mem = explode(",", $result["mem"]);
	
	//colouring
	if (strstr($status, "online")){
		$status_col = $online_col;
	}elseif (strstr($status, "offline")){
$status= "booting";
foreach ($servers_reboot as $server_reboot){
	$url2 = $baseurl . "/command.php?key=".$server_reboot['key']."&hash=".$server_reboot['hash']."&action=". $server_reboot["action"] ."&ipaddr=true&bw=true&mem=true&hdd=true";
	
	$file2 = fopen ($url2, "r");
	if (!$file2) {
		die("Unable to open remote file.");
	}
	$data2 = fgets ($file2);
	fclose($file2);
}
	
	}
	else{
		$status_col = $offline_col;
	}
	
	// BANDWIDTH
		//default
		$bw_col = "#FFFFFF";
		
		//above 50%
		if($bw[1] > ($bw[0]*0.5)){
			$bw_col = $warn_50;
		}
		//above 75%
		if($bw[1] > ($bw[0]*0.75)){
			$bw_col = $warn_75;
		}
		//above 90%;
		if($bw[1] > ($bw[0]*0.9)){
			$bw_col = $warn_90;
		}
		
	// MEMORY
		//default
		$mem_col = "#FFFFFF";
		
		//above 50%
		if($mem[1] > ($mem[0]*0.5)){
			$mem_col = $warn_50;
		}
		//above 75%
		if($mem[1] > ($mem[0]*0.75)){
			$mem_col = $warn_75;
		}
		//above 90%;
		if($mem[1] > ($mem[0]*0.9)){
			$mem_col = $warn_90;
		}
		
	// DISK
		//default
		$disk_col = "#FFFFFF";
		
		//above 50%
		if($disk[1] > ($disk[2]*0.5)){
			$disk_col = $warn_50;
		}
		//above 75%
		if($disk[1] > ($disk[2]*0.75)){
			$disk_col = $warn_75;
		}
		//above 90%;
		if($disk[1] > ($disk[2]*0.9)){
			$disk_col = $warn_90;
		}
	
	$bw[0] = round($bw[0] / 1024 / 1024 / 1024, 2);
	$bw[1] = round($bw[1] / 1024 / 1024 / 1024, 2);
	$bw[2] = round($bw[2] / 1024 / 1024 / 1024, 2);
	$mem[0] = round($mem[0] / 1024 / 1024, 2);
	$mem[1] = round($mem[1] / 1024 / 1024, 2);
	$mem[2] = round($mem[2] / 1024 / 1024, 2);
	$disk[0] = round($disk[2] / 1024 / 1024 / 1024, 2);
	$disk[1] = round($disk[1] / 1024 / 1024 / 1024, 2);
	$disk[2] = round($disk[2] / 1024 / 1024 / 1024, 2);
	
	//print our data
	echo "<tr>";
	echo "<td><span>$name</span></td>";
	echo "<td><span>$ip</span></td>";
	echo "<td><span style='color: $status_col'>$status</span></td>";
	echo "<td><span style='color: $disk_col'>$disk[1]G/<small>$disk[0]G<small>($disk[3]%)</small></small></span></td>";
	echo "<td><span style='color: $mem_col'>$mem[1]M<small> / $mem[0]M<small> ($mem[3]%)</small></small></span></td>";
	echo "<td><span style='color: $bw_col'>$bw[1]G<small> / $bw[0]G<small> ($bw[3]%)</small></small></span></td>";
	echo "</tr>";
}

?>
 	</table>
    <br /><br />
	<td align="center" valign="middle" ><small><p style='text-align:center'>
<?php	echo "<small>This page was last modified: </small>".date("d.m.Y H:i:s",time());?>
</small></p></td>
    </div>	
    </body>
</html>
