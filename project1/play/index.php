<?php
	/* Allows the player to input a move, alters the board accordingly, and saves the altered board in a file */
	// Input: localhost:XXXX/play/?pid=XXXXXXXXXXXXX&move=X
	// Output:  {"response":true,"ack_move":{"slot":2,"isWin":false,"isDraw":false,"row":[]},"move":{"slot":4,"isWin":false,"isDraw":false,"row":[]}}

	/* Getting queries from url
	 * Reference: http://stackoverflow.com/questions/11480763/how-to-get-parameters-from-this-url-string
	 */
	$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$parts = parse_url($url);
	parse_str($parts['query'], $query);

	/* getting vars from query string of url */
	$pid = $query['pid'];
	$move = $query['move'];
	
	require_once("commFunctions.php");
	
	if ($pid == "") {	// check if pid exists
		makeError("Pid not specified");
		return;
	} else if ($move == "") {	// check if move exists
		makeError("Move not specified");
		return;
	} else if(!doesPidExist($pid)) {	// check if file associated with pid exists
		makeError("Unknown pid");
		return;
	} else if(!withinRange($move)) {		// check if move is valid (within range)
		makeError("Invalid slot, " . $move);
		return;
	}
	
	//$pid = "57f56419ca015"; //For testing purposes
	$myfile = fopen("../Writable/" . $pid . ".txt", "r+");
	
	$pidLine = fgets($myfile);	// ignored for now
	$gameObject = json_decode(fgets($myfile));
	$loadedBoard = $gameObject->boardArr;
	fclose($myfile);
	
	require_once("board.php");
	
	// getting response ready
	$obj = new jsonObject();

	//$obj = checksAndMove($obj, $loadedBoard, 1);//one for human player
	//$obj = checksAndMove($obj, $loadedBoard, 2);//AI
	
	$obj = userMovePlay($obj, $move);
	$obj = aiMovePlay($obj, $strategy);
	
	// saves altered board as json text, save into file and echo it
	$responseEncoded = json_encode($obj);
	echo $responseEncoded;//echoes json repsonse
	$boardEncoded = json_encode($loadedBoard);
	
	
	addToTextDoc($pid, $pidLine);
	addToTextDoc($pid, $obj);//changed this to only save board
	
	
	
	/*			Functions			*/
	
	function userMovePlay($obj, $move) {
		$obj->boardArr = userMove($obj->boardArr, $move);
		$obj = checksAndMove($obj, 1);
		return $obj;
	}
	
	function aiMovePlay($obj, $strategy) {
		$obj->boardArr = aiMove($obj->boardArr, $strategy);
		$obj = checksAndMove($obj, 2);
		return $obj;
	}
	
	//this function automizes the turn functionality.
	//PARAM: 	$boardObj is the board object
	//			$who is int, 1 representing human player, 2 representing AI.
	function checksAndMove($obj, $who){//RETURNS updated object with final params
		$loadedBoard = $obj->boardArr;
		if($who == 1)
			$obj->ack_move['slot'] = $move;
		else
			$obj->move['slot'] = $aiDecision;
		
		
		if(checkIfWin($loadedBoard)){		//game won, set winning coordinates
			if($who == 1){
				$obj->ack_move['isWin'] = true;
				$obj->ack_move['row'] = getWinArray($loadedBoard);
			}
			else{
				$obj->move['isWin'] = true;
				$obj->move['row'] = getWinArray($loadedBoard);
			}
			return $obj;
		}
		else if(checkIfTie($loadedBoard)){		//game tied, set proper params
			$obj->ack_move['isDraw'] = true;
			$obj->move['isDraw'] = true;
			return $obj;
		}
		else 
			return obj;
	}
	
	function withinRange($move){
		require_once("board.php");
		$board = new Board();
		return $move >= 0 && $move < $board->cols;
	}
	
	function doesPidExist($pid){
		return file_exists("../Writable/" . $pid . ".txt");
	}
	
	
	/*			Classes			*/
	class jsonObject {
		public $response = true;
		public $ack_move = array("slot" => -1, "isWin" => false, "isDraw" => false, "row"=>array());	// User move
		public $move = array("slot" => -1, "isWin" => false, "isDraw" => false, "row"=>array());		// AI move
	}
	
	