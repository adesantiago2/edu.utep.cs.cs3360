<html>
<head>
	<title>Composing Tables</title>
</head>
<body>
	<table style="width:100%">
		<tr>
			<th>Day</th>
		  	<th>Time</th>
		  	<th>Description</th>
	  </tr>
	
	<?php
	
		$appointments = array(
				array("Day"=>"M", "Time"=>"02:00-02:50PM", "Description"=>"Myoung Kim (Room 202B)"),
				array("Day"=>"TR", "Time"=>"10:30-11:50AM", "Description"=>"CS 3360 (Room 322)")
		);
	
		foreach($appointments as $appt)
		{
			$day = $appt['Day'];
			$time = $appt['Time'];
			$descr = $appt['Description'];
			
			echo "<tr><td align=center> $day </td>";
			echo "<td  align=\"center\"> $time</td>";
			echo "<td  align=\"center\"> $descr</td></tr>";
		}
	
	?>
	
	</table>
</body>
</html>