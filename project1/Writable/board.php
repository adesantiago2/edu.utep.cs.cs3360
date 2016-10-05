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
		// TODO: check if column is full, if yes return same board
		// TODO: drop token to the bottom of the column
		// TODO: call aiMove
		// TODO: return altered board
	}
	
	function userMove($board, $col) {
		makeMove($board, $col, 1);
	}
	
	/* Makes a move for the ai based on the strategy chosen. If smart, will make a winning move if possible or block a winning move 
	 * Otherwise, it makes a random move.
	 * */
	function aiMove($board) {
		$strategy = $board->strategy;
		if($strategy == "Smart") {
			//TODO: check if there is any move ai can do to win the game. If yes, call makeMove(board, col, 2) and return true
			//TODO: check if there is any move a user can do to win the game, block that move. If yes, call makeMove(board, col, 2) and return column number
		}
		
		/* Make random move*/
		//TODO: iterate through the first row of the board, add every index that is 0 to an array (available moves)
		//TODO: from those available moves, choose one by random and call makeMov(board, randCol, 2). return randCol
		//TODO: if there are no available moves, return -1;
	}
	
	/* Checks whether or not the game has been won by a player (1=user, 2=ai). Checked using brute force and a counter.
	 * Returns the indices for the winning line [c1, r1, c2, r2, c3, r3, c4, r4]
	 * */
	function checkIfWin($board, $player) {
		//TODO: Check all vertical moves (only for user)
		//TODO: Check all horiz moves (only for user)
		//TODO: Check all BL-TR moves (only for user)
		//TOOO: Check all TL-BR moves (only for user)
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