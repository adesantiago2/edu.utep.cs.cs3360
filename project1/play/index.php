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
	
	require_once("commonFunctions.php");
	
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
	$myfile = fopen("$pid".".txt", "r+");
	$jsonArray = fgets($myfile);//read line which contains array in jason format
	$gameObject = json_decode($jsonArray);
	$loadedBoard = $gameObject->boardArr;
	fclose($myfile);
	
	
	require_once("board.php");
	
	
	// getting response ready
	$obj = new jsonObject();

	$obj = checksAndMove($obj, $loadedBoard, 1);//one for human player
	$obj = checksAndMove($obj, $loadedBoard, 2);//AI
	
	// saves altered board as json text, save into file and echo it
	$responseEncoded = json_encode($obj);
	echo $responseEncoded;//echoes json repsonse
	$boardEncoded = json_encode($loadedBoard);
	addToTextDoc($pid, $boardEncoded);//changed this to only save board
	
	
	
	/*			Functions			*/
	
	//this function automizes the turn functionality.
	//PARAM: 	$boardObj is the board object
	//			$who is int, 1 representing human player, 2 representing AI.
	function checksAndMove($obj, $loadedBoard, $who){//RETURNS updated object with final params
		$loadedBoard = makeMove($loadedBoard, $move, $who);
		if($who == 1)
			$obj->ack_move['slot'] = $move;
		else
			$obj->move['slot'] = $aiDecision;
		
		if(checkIfTie){//game tied, set proper params
			$obj->ack_move['isDraw'] = true;
			$obj->move['isDraw'] = true;
			deleteFile($pid);
			return $obj;
		}
		else if(checkIfWin($loadedBoard)){//game won, set winning coordinates
			if($who == 1){
				$obj->ack_move['isWin'] = true;
				$obj->ack_move['row'] = getWinArray($loadedBoard);
			}
			else{
				$obj->move['isWin'] = true;
				$obj->move['row'] = getWinArray($loadedBoard);
			}
			deleteFile($pid);
			return $obj;
		}
		else if(checkIfTie){//check for tie again, since AI moved
			$obj->ack_move['isDraw'] = true;
			$obj->move['isDraw'] = true;
			deleteFile($pid);
			return $obj;
		}
		else 
			return obj;
	}
	
	function withinRange($move){
		require_once("../Writable/board.php");
		$board = new Board();
		return $move >= 0 && $move < $board->cols;
	}
	
	function deleteFile($pid) {
		// TODO: delete the file associated with the pid to cleanup after a game is finished
	}
	
	
	/*			Classes			*/
	class jsonObject {
		public $response = true;
		public $ack_move = array("slot" => -1, "isWin" => false, "isDraw" => false, "row"=>array());	// User move
		public $move = array("slot" => -1, "isWin" => false, "isDraw" => false, "row"=>array());		// AI move
	}
	
	