<?php
	require_once("../Writable/board.php");
	$board = new board();
	$gInfo = new gameInfo();
	
	$gInfo->width = $board->cols;
	$gInfo->height = $board->rows;
	$gInfo->strategies= array("Smart", "Random");
	echo json_encode($gInfo);
	
	/*			Classes			*/
	
	class gameInfo {
		public $width = "";
		public $height = "";
		public $strategies = "";
	}