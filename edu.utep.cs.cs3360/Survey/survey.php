<html>
<head>
	<title>PHP Survey</title>
</head>
<body>

	<h1>PHP Survey</h1>
	
	<?php
		date_default_timezone_set('America/Denver');
		echo "Today is " . date("l F d, Y") . "<br>";
	?>
	
	<h3>Check reasons and click the submit button</h3>

	<form action="survey-handler.php", method=post>
	<input type="checkbox" name="reason1" value="interpreted"/>It's interpreted<br/>
	<input type="checkbox" name="reason2" value="dynamic"/>
	    It's dynamically typed<br/>
	<input type="checkbox" name="reason3" value="flexible"/>It's flexible<br/>
	<input type="checkbox" name="reason4" value="ooish"/>It's object-oriented<br/>
	<input type="checkbox" name="reason5" value="fun"/>It's fun<br/>
	<input type="hidden" name="num_of_reasons" value="5"/>
	
	<p>Your name: <input type="text" name="name"/></p>
	
	<input type="submit" value="Submit">
	</form>
</body>
</html>