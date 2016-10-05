<?php
	/* Creates a board with 6 rows and 7 cols & initializes the board to all 0s 
	 * returns new board
	 * */
	function createBoard(){
		$board = new Board();
		$board->rows = 6;
		$board->cols = 7;
		$board->boardArr = array_fill(0, $board->rows - 1, array_fill(0, $board->cols - 1, 0));
		return $board;
	}
	
	/* Attemps to put in a token into a board's column, returns altered board */
	function makeMove($board, $col) {
		// TODO: check if column is full, if yes return same board
		// TODO: drop token to the bottom of the column
		// TODO: return altered board
	}
	
	/* Makes a move for the ai based on the strategy chosen. If smart, will make a winning move if possible or block a winning move 
	 * Otherwise, it makes a random move.
	 * */
	function aiMove($board, $strategy) {
		if($strategy == "Smart") {
			//TODO: check if there is any move ai can do to win the game. If yes, call makeMove(col) and return true
			//TODO: check if there is any move a user can do to win the game, block that move. If yes, call makeMove(col) and return true
		}
		
		/* Make random move*/
		//TODO: iterate through the first row of the board, add every index that is 0 to an array (available moves)
		//TODO: from those available moves, choose one by random and call makeMov(randCol). return true
		//TODO: if there are no available moves, return false;
	}
	
	/* Returns whether or not the game has been won by a player (1=user, 2=ai). Checked using brute force and a counter. */
	function checkIfWin($board, $user) {
		//TODO: Check all vertical moves (only for user)
		//TODO: Check all horiz moves (only for user)
		//TODO: Check all BL-TR moves (only for user)
		//TOOO: Check all TL-BR moves (only for user)
	}
		
	class Board {
		public $cols;
		public $rows;
		public $boardArr;
	}