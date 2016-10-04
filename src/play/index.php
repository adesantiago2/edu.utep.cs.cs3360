<?php
	$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
	// Reference: http://stackoverflow.com/questions/11480763/how-to-get-parameters-from-this-url-string
	$parts = parse_url($url);
	parse_str($parts['query'], $query);
	
	// getting vars from query string of url
	$pid = $query['pid'];	
	$move = $query['move'];
	
	if ($pid == "") {
		makeError("Pid not specified");
		return;
	} else if ($move == "") {
		makeError("Move not specified");
		return;
	} else if(!doesPidExist($pid)) {
		$err = new errorInput();
		$err->reason = "Unknown pid";
		echo json_encode($err);
		return;
	} else if(!validMove($move)) {
		$err = new errorInput();
		$err->reason = "Invalid slot, " . $move;
		echo json_encode($err);
		return;
	}
	
	echo "Successful input!";
	
	
	
	/* Functions */
	
	function doesPidExist($pid) {
		return file_exists("../Writable/" . $pid . ".txt");
	}
	
	function makeError($msg) {
		$err = new errorInput();
		$err->reason = $msg;
		echo json_encode($err);
	}
	
	function validMove($move){
		return $move >= 0 && $move < 7;
	}
	
	/* Classes */
	
	class move {
		public $response = false;
		public $ack_move = array("slot"=>-1, "isWin"=>false, "isDraw"=>false, "row"=>0);
		public $move = array("slot"=>-1, "isWin"=>false, "isDraw"=>false, "row"=>0);
	}
	
	class errorInput {
		public $response = false;
		public $reason = "";
	}