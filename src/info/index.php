<?php
	$gInfo = new gameInfo();
	$gInfo->width = 7;
	$gInfo->height = 6;
	$gInfo->strategies= array("Smart", "Random");
	echo json_encode($gInfo);
	
	class gameInfo {
		public $width = "";
		public $height = "";
		public $strategies = "";
	}