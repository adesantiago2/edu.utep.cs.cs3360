<html>
	<head>
		<title>My Guestbook</title>
	</head>
	
	<body>
			<h1>PHP Survey</h1>
		
			<?php
			$finalString = '';
			if(isset($_POST['name'])) {
				$name = $_POST['name'];
				echo "Thanks, " . $name . "!<br><br>";
				$finalString = $finalString . $name . ': ';
			}
			
			if(isset($_POST['num_of_reasons'])) {
				$num_of_reasons = $_POST['num_of_reasons'];
			}
			
			for($i = 1; $i < $num_of_reasons + 1; $i++) {
				if(isset($_POST['reason' . $i])) {
					$reason = $_POST['reason' . $i];
					echo ('Reaason: ' . $reason . '<br>');
					$finalString = $finalString . $reason . "; ";
				}
			}
			add_to_text_doc($finalString . PHP_EOL);
			
			function add_to_text_doc($finalString) {
				if (!empty($finalString)) {
					$fp = fopen("survey.txt", "a");	// a is for writing only
					fputs($fp, nl2br($finalString));
					fclose($fp);
				}
			}
		
			?>
	</body>

</html>


