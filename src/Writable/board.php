<?php
	/*			Functions			*/

	function setupBoard() {
		$board = new board();
		$boardTokensArray = array_fill(0, $board->rows - 1, array_fill(0, $board->cols - 1, 0));
		//print_r($boardTokensArray);
	}
	
	function readBoard($pid) {
		$myfile = fopen("../"."$pid".".txt", "r+");
		fgets($myfile);//move to second line
		$jsonWithArray = fgets($myfile);//read second line which contains array in jason format
		$jsonVars = json_decode($json);
		$newBoard = $jsonVars->boardTokensArray;
		fclose($myfile);
		
		$board->boardTokensArray = $newBoard;
	}
	
	/* Returns board after dropping token */
	function userMove($move, $pid, $board){
		$moves = new moves();
		$moves->ack_move['slot'] = $move;
	
		$y = 6;	//to check rows
		while($y > 0){
			if($board[$move][$y] == 0){
				$board[$move][$y] = 1;
				break;
			}
			$y--;
		}
	
		return $board;
	}
	
	function checkWin($board){
		$win = false;
		$temp = fill_array(11, 12, 0);
		for($x = 0; $x < 7; $x++){
			for($y = 0; $y < 6; $y++){
				$temp[$x][$y] = $board[$x][$y];
			}
		}
	
		for($x = 0; $x < 7; $x++){
			for($y = 0; $y < 6; $y++){
				$token = $temp[$x][$y];//current token being checked
				if($token == 0){
					//do nothing
				}
				else{
					$tmpX = x;$tmpY = y;$count = 0;
					while($temp[$x][$tmpY] != 0){ //horizontal
						$tmpY++;$count++;
						if($count > 3)
							return true;
					}
	
					$tmpX = $x; $tmpY = $y; $count = 0; //vertical
					while ($temp[$tmpX][$y] != 0){
						$tmpX++;$count++;
						if ($count > 3)
							return true;
					}
	
					$tmpX = $x; $tmpY = $y; $count = 0;
					while($temp[$tmpX][$tmpY] != 0){ //upwards diagonal
						$tmpX++;$tmpY++;$count++;
						if($count > 3)
							return true;
					}
	
					$tmpX = $x; $tmpY = $y; $count = 0;
					while($board[$tmpX][$tmpY] != 0){ //downward diagonal
						$tmpX--;$tmpY++;$count++;
						if($tmpX <= 0){break;}
						if($count > 3)
							return true;
					}
				}
			}
		}
		return $win;
	}

	/*			Classes			*/
	
	class moves {
		public $response = false;
		public $ack_move = array("slot"=>-1, "isWin"=>false, "isDraw"=>false, "row"=>0);
		public $move = array("slot"=>-1, "isWin"=>false, "isDraw"=>false, "row"=>0);
	}
	
	class board {
		public $cols = 7;
		public $rows = 6;
		public $boardTokensArray;
	}
	
	class RandomStrategy {
		public $response = true;
		public $pid = 0;
		
		function AImove($boardTokensArray) {
			$arrOfPossibleMoves = "";	// Contains an array of possible moves for the AI to choose a column from
			for($i = 0; $i < $boardTokensArray[0].length; $i++) {
				if($boardTokensArray[0][i] == 0) {
					$arrOfPossibleMoves[] = i;
				}
			}
			
			if($arrOfPossibleMoves.length != 0) {	// At least one possible move
				$randMove = array_rand($arrOfPossibleMoves);
			} else {
				// Error, no moves left (should have already caught this by tie checking system)
				return;
			}
			
			// Drop the token, make edits to board, return board
			for($row = 0; $row < 6; $row++) {
				if($boardTokensArray[$row][$randMove] != 0) {
					$boardTokensArray[$row][$randMove] = 2;
				}
			}
			return $boardTokensArray;
		}
	}
	
	class SmartStrategy {
		public $response = true;
		public $pid = 0;
		
		function AImove($boardTokensArray) {
			
			for($i = 0; $i < $boardTokensArray[0].length; $i++) {
				$imaginaryBoard = array();
				for($row = 0; $row < $boardTokensArray.length; $row++) {
					for($col = 0; $col < $boardTokensArray[$row].length; $col++) {
						$imaginaryBoard[$row][$col] = $boardTokensArray[$row][$col];
					}
				}
				// Drop the token into imaginary board, make edits to board
				for($row = 0; $row < 6; $row++) {
					if($imaginaryBoard[$row][$i] != 0) {
						$imaginaryBoard[$row][$i] = 2;
					}
				}
				
				if(checkWin($imaginaryBoard)) {
					return $imaginaryBoard;
				}
			}
			
			// No way for the AI to win at this moment, check if there is anyway for the user to win
			
			for($i = 0; $i < $boardTokensArray[0].length; $i++) {
				$imaginaryBoard = array();
				for($row = 0; $row < $boardTokensArray.length; $row++) {
					for($col = 0; $col < $boardTokensArray[$row].length; $col++) {
						$imaginaryBoard[$row][$col] = $boardTokensArray[$row][$col];
					}
				}
				// Drop the token into imaginary board, make edits to board
				for($row = 0; $row < 6; $row++) {
					if($imaginaryBoard[$row][$i] != 0) {
						$imaginaryBoard[$row][$i] = 1;
					}
				}
			
				if(checkWin($imaginaryBoard)) {
					return $imaginaryBoard;
				}
			}
			
			// If the AI can't win and the user can't win, make random move
			$rand = new RandomStrategy();
			return $rand->AImove($boardTokensArray);
		}
	}