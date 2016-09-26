<?php $note = $_POST['note']; ?>
<html><head><title>My Guestbook</title></head>
<body>
<h1>Welcome to my Guestbook</h1>
<h2>Please write me a little note below</h2>
<form action="<?= $_SERVER[‘PHP_SELF’] ?>" method="POST">
<textarea cols=40 rows=5 name="note" wrap=virtual></textarea>
<p/>
<input type=submit value="Send it ">
</form>

<?php add_note($note); ?>

<h2>The entries so far:</h2>
<?php show_notes(); ?>

<?php
function add_note($note) {
  if (!empty($note)) {
     $fp = fopen("notes.txt", "a");	// a is for writing only
     fputs($fp, nl2br($note).'<br/>');
     fclose($fp);
   }
}

function show_notes() {

	// Write your code here!
	$fp = fopen("notes.txt", "r") or die("Unable to open file!");	// r is for reading
	
	if(filesize("notes.txt") > 0){
		echo fread($fp, filesize("notes.txt"));
	}

	fclose($fp);
}
?>
</body>
</html> 
