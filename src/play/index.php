<?php
	$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
	// Reference: http://stackoverflow.com/questions/11480763/how-to-get-parameters-from-this-url-string
	$parts = parse_url($url);
	parse_str($parts['query'], $query);
	
	// getting vars from query string of url
	$pid = $query['pid'];	
	$move = $query['move'];
	
	if($pid == "") {
		$err = new errorInput();
		$err->reason = "Pid not specified";
		echo json_encode($err);
		return;
	} else if ($move == "") {
		$err = new errorInput();
		$err->reason = "Move not specified";
		echo json_encode($err);
		return;
	}
	
	echo "Successful input!";
	
	
	
	class move {
		public $response = false;
		public $ack_move = array("slot"=>-1, "isWin"=>false, "isDraw"=>false, "row"=>0);
		public $move = array("slot"=>-1, "isWin"=>false, "isDraw"=>false, "row"=>0);
	}
	
	class errorInput {
		public $response = false;
		public $reason = "";
	}