
<table style  ="widhth:500 px;" border ="4">
    <tr>
    <th colspan ="10"><center><h2>ConversionFiredsStatus </h2></center></th>
</tr>
<tr>
  <td>S.NO</td>
  <td>serial</td>
  <td>affilaite</td>
  <td>campaign</td>
  <td>channel</td>
  <td>offer_id</td>
  <td>country</td>
  <td>link</td>
</tr>
<?php include ('connection.php');
{
	$snum = $_POST['snumber'];
	$homecount=0;
	$result1 = mysqli_query($conn,"SELECT serial FROM users_sheet_info ORDER BY serial DESC LIMIT 1;");
	$row2 = mysqli_fetch_array($result1);
	$C2 = $row2['serial'];
	//echo $C2;
	$result = mysqli_query($conn,"SELECT serial,start_no,end_no,platform FROM users_sheet_info WHERE serial ='$snum' ;");
	echo "<br>";
	$row1 = mysqli_fetch_array($result);
	$C3 = $row1['start_no'];
	$C4 = $row1['end_no'];
	$C5 = $row1['platform'];
	$C6 = $C4 - $C3;
	
	echo "You Have Entered a DownloadSerial No -: " .$snum;
	echo "<br>";
	echo "Starting Serial-: " .$C3;
	echo "<br>";
	echo "Last Serial -: " .$C4;
	echo "<br>";
	echo "Total links checked in between Starting Serial and Last Serial -:" .$C6;
	echo "<br>";
	echo "Platform-: " .$C5;
	echo "<br>";
	if(isset($_POST['submit']))
	{
		

		if ($snum <= $C2)
		{
		 if ($C5 != 'ios')
		{
			$sql ="SELECT serial,affiliate,campaign,channel,offer_id,country,link FROM `link_test` WHERE `serial` >= '".$C3."' AND `serial` <= '".$C4."' AND `is_test` = '1' AND `server_status_code` = '10';";
		}
		elseif($C5 ='ios')
		{
			$sql ="SELECT serial,affiliate,campaign,channel,offer_id,country,link FROM `link_test_ios` WHERE `serial` >= '".$C3."' AND `serial` <= '".$C4."' AND `is_test` = '1' AND `server_status_code` = '10';";
		}
	}
	else 
	{
		exit("YOU HAVE ENTERED A WRONG SERIAL :");
	}
		$result = mysqli_query($conn,$sql);
		$resultcheck = mysqli_num_rows($result);
		$rowcount=mysqli_num_rows($result);
		echo "Total Number of Links Fired -:" .$rowcount;
		if ($resultcheck > 0) 
		{
			 for($x =1; $x<=$rowcount;$x++)
			 {
			while ($row = mysqli_fetch_assoc($result))
				{
					echo "<tr>";
					echo "<td>" .$x++. "</td>";
					echo "<td>" .$row['serial']. "</td>";
					echo "<td>" .$row['affiliate']."</td>";
					echo "<td>" .$row['campaign']."</td>";
					echo "<td>" .$row['channel']."</td>";
					echo "<td>" .$row['offer_id']."</td>";
					echo "<td>" .$row['country']."</td>";
					echo "<td>" .$row['link']."</td>";
					echo "</tr>"; 
				
				}
			}
		}
		else
		{
			echo "<br>";
			echo "No Links Fired";
		}
	}
}
?>
