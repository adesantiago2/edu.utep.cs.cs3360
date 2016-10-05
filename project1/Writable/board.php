<?php
	/* Creates a board with 6 rows and 7 cols & initializes the board to all 0s 
	 * returns new board
	 * */
	function createBoard($strategy){
		$board = new Board();
		$board->boardArr = array_fill(0, $board->rows - 1, array_fill(0, $board->cols - 1, 0));
		$board->strategy = $strategy;
		return $board;
	}
	
	function loadBoard($arr, $count, $strategy) {
		$board = new Board();
		$board->boardArr = $arr;
		$board->count = $count;
		$board->strategy = $strategy;
		return $board;
	}
	
	/* Attemps to put in a token into a board's column, calls aiMove, returns altered board */
	function makeMove($board, $col, $userType) {
		$boardArr = $board->boardArr;
		
		// check if column is full, if yes return same board
		if($boardArr[0][$col] != 0) {
			return $board;
		}
		
		// drop token to the bottom of the column
		$board = dropToken($board, $col, $userType);
		return $board;
	}
	
	function userMove($board, $col) {
		return makeMove($board, $col, 1);
	}
	
	/* Makes a move for the ai based on the strategy chosen. If smart, will make a winning move if possible or block a winning move 
	 * Otherwise, it makes a random move.
	 * */
	function aiMove($board) {
		$strategy = $board->strategy;
		
		// find all available columns
		$arrOfPossMoves = array();
		for($c = 0; $c < $board->cols; $c++) {
			if($board[0][$c] == 0) {
				$arrOfPossMoves[] = $c;
			}
		}
		if($arrOfPossMoves.length == 0) {
			return -1;
		}
		
		if($strategy == "Smart") {
			// check if there is any move ai can do to win the game. If yes, return col
			for($col = 0; $col < $board->rows; $col++) {
				$tempBoard = dropToken($board, $col, 2);
				if(checkIfWin($tempBoard->boardArr, 2).length != 0) {
					return $col;
				}
			}
				
			// check if there is any move a user can do to win the game, block that move. If yes, return column number to block it
			for($col = 0; $col < $board->rows; $col++) {
				$tempBoard = dropToken($board, $col, 1);
				if(checkIfWin($tempBoard->boardArr, 2).length != 0) {
					return $col;
				}
			}
		}
		
		// from those available moves, choose one by random and return it
		return array_rand($arrOfPossMoves);
	}
	
	/* Checks whether or not the game has been won by a player (1=user, 2=ai). Checked using brute force and a counter.
	 * Returns the indices for the winning line [c1, r1, c2, r2, c3, r3, c4, r4] not a boolean!
	 * */
	function checkIfWin($board, $userType) {
		$boardArr = $board->boardArr;
		
		// check all vertical moves (only for $userType)
		for($row = 0; $row < ($board->rows - 3); $row++) {	// loop through first 3 rows
			for($col = 0; $col < $board->cols; $col++) {
				$count = 0;
				$row = array_fill(0, 7, 0);		// keeps track of row to return if the game is won
				$rowCount = 1;
				for($r = $row; $r < $row + 4; $r++) {	// loops downwards 4 times
					if($board[$r][$col] == $userType) {
						$count++;
						$row[$rowCount] = $r;
						$rowCount += 2;			// rows are only odd indices r1 = 1, r2 = 3...
					} else {
						break;
					}
				}
				if($count == 4) {
					for($i = 0; $i < 7; $i += 2) {	// fills out all of the cols in the row to return (only even #s)
						$row[$i] = $col;
					}
					return $row;
				}
			}
		}
		
		// check all horiz moves (only for $userType)
		for($row = 0; $row < $board->rows; $row++) {
			for($col = 0; $col < ($board->cols - 3); $cols++) {	// loop through first 4 cols
				$count = 0;
				$row = array_fill(0,7,0);	// keeps track of row to return if the game is won
				$rowCount = 0;
				for($c = $col; $c < $col + 4; $c++) {
					if($board[$row][$c] == $userType) {
						$count++;
						$row[$rowCount] = $c;
						$rowCount += 2;	// cols are only even indices c1 = 0, c2 = 2....
					} else {
						break;
					}
				}
				if($count == 4) {
					for($i = 1; $i < 8; $i += 2) {	// fills out all of the rows in the row to return (only odd #s)
						$row[$i] = $row;
					}
					return $row;
				}
			}
		}
		
		// check all BL-TR moves (only for $userType)
		for($row = $board->rows - 1; $row > 2; $row--) {	// loop through last 3 rows
			for($col = 0; $col < ($board->cols - 3); $col++) {	// loop through first 4 cols
				$count = 0;
				$row = array_fill(0,7,0);	// keep track of row to return if the game is won
				$rowCount = -1;
				for(($r = $row), ($c = $col); $r > 2 && $c < $board->cols - 3; ($r--), ($c++)) {
					if($board[$r][$c] == $userType) {
						$count++;
						$row[++$rowCount] = $c;		// Increments before putting in the current col/row
						$row[++$rowCount] = $r;
					} else {
						break;
					}
				}
				if($count == 4) {
					return row;
				}
			}
		}
		
		// check all TL-BR moves (only for $userType)
		for($row = 0; $row < ($board->rows - 3); $row++) {		// loop through first 3 rows
			for($col = 0; $col < ($board->cols - 3); $col++) {		// loop through first 4 cols
				$count = 0;
				$row = array_fill(0,7,0);	// keep track of row to return if the game is won
				$rowCount = -1;
				for(($r = $row), ($c = $col); $r < ($board->rows - 3) && $c < ($board->cols - 3); ($r++), ($c++)) {
					if($board[$r][$c] = $userType) {
						$count++;
						$row[++$rowCount] = $c;		// Increments before putting in the current col/row
						$row[++$rowCount] = $r;
					} else {
						break;
					}
				}
				if($count == 4) {
					return row;
				}
			}
		}
	}
	
	/* drop token to the bottom of a column 
	 * returns a full Board object, not an array
	 * */
	function dropToken($board, $col, $userType) {
		for($tok = 0; $tok < $board->rows; $tok++) {
			if($tok == ($board->rows - 1)) {		// Already hit bottom
				($board->boardArr)[$tok][$col] = $userType;
			}
		}
		return $board;
	}
	
	/*			Classes			*/
		
	class Board {
		// Used for the state of the game
		public $boardArr;
		public $count = 0;
		public $strategy;
		
		public $rows = 6;
		public $cols = 7;
		public $maxTokens = $rows * $cols;
	}