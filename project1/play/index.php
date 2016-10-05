<?php
	/* Allows the play to input a move, alters the board accordingly, and saves the altered board in a file */
	// Input: localhost:XXXX/play/?pid=XXXXXXXXXXXXX&move=X
	// Output:  {"response":true,"ack_move":{"slot":2,"isWin":false,"isDraw":false,"row":[]},"move":{"slot":4,"isWin":false,"isDraw":false,"row":[]}}

	//TODO: check if pid exists
	//TODO: check if move exists
	//TODO: check if file associated with pid exists
	//TODO: check if move is valid (within range)
	
	//TODO: create instance of board loaded/parsed from pid file json
	//TODO: calls makeMove(col) in board.php, saves altered board
	//TODO: check if the game is won or tied
	//TODO: saves altered board as json text, save into file and echo it