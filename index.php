

<h1>toFret examples</h1>

<p>toFret was born out of a desire to learn the fretboard of a bass guitar. I wanted to see the position of the notes on the fretboard.</p>
<p>But first you need the notes.</p>

<?php
	include('MusicNotes.class.php');
	include('NoteWalk.class.php');
	include('tab.class.php');
?>

<?php
$notesArray 		= array('C','E','G','F');
$musicNotes 		= new MusicNotes();
$chordSuggestions 	= $musicNotes->getChordsForNotes( $notesArray );

?>
<h2>
	Getting chords for notes <?php echo implode(' ', $notesArray); ?>
</h2>
<?php

$chordContainingAllNotes 		= $chordSuggestions[0];
$chordContainingAllNotesButOne 	= $chordSuggestions[1];


echo '<h3>Chords containing all given notes</h3>';
	foreach ($chordContainingAllNotes as $chord) {
		$chordName 		= $chord['note'] . '' . $chord['chord'];
		$notesInChord 	= implode(' ', $chord['chordNotes']);

		echo "<p>{$chordName} ($notesInChord)</p>";
	}

echo '<h3>Chords containing all but one given notes</h3>';
	foreach ($chordContainingAllNotesButOne as $chord) {
		$chordName 		= $chord['note'] . '' . $chord['chord'];
		$notesInChord 	= implode(' ', $chord['chordNotes']);

		echo "<p>{$chordName} ($notesInChord)</p>";
	}
/*
echo '<pre>';
print_r($chordContainingAllNotes);
echo '</pre>';

echo '<p>chordContainingAllNotesButOne</p>';
echo '<pre>';
print_r($chordContainingAllNotesButOne);
echo '</pre>';
*/
?>





<?php



include('/var/www/html/gitHub/toFret/MusicNotes.class.php');
include('/var/www/html/gitHub/toFret/NoteWalk.class.php');
include('/var/www/html/gitHub/toFret/tab.class.php');


$examples['guitar_tablature_1']['guitar']['tab_player']['input'] = '

Posted by: marp@irix.bris.ac.uk (R. Porter)
Date: Tue, 18 Jan 1994 12:27:59 GMT

Below is a guitar transcription of Imagine. I have tried to
make it sound as true to the piano style as possible.


(1)
Twice:
e-----------------------------------|--------------------------------||
B----------------------------0------|-----------------------------0--||
G----0-------0-------0-------0------|-2-------2-------2-------2-3----||
D----2-------2-------2--------------|-3-------3-------3--------------||
A--------3-------3-------3-------3--|-----3-------3-------3----------||
E----3-------3-------3-------3------|-1-------1-------1--------------||
     C                       Cmaj7    F            
(2)
3 times:
e-----------------------------------|--------------------------------||
B----------------------------0------|-----------------------------0--||
G----0-------0-------0-------0------|-2-------2-------2-------2-3----||
D----2-------2-------2--------------|-3-------3-------3--------------||
A--------3-------3-------3-------3--|-----3-------3-------3----------||
E----3-------3-------3-------3------|-1-------1-------1--------------||
     C                       Cmaj7    F            


e-----------------------------------|--------------------------------|
B----------------------------0------|--------------------------------|
G----0-------0-------0-------0------|-2-------2-------2-------2------|
D----2-------2-------2--------------|-3-------3-------3-------3------|
A--------3-------3-------3-------3--|-----3-------3-------3-------3--|
E----3-------3-------3-------3------|-1-------1-------1-------1------|
     C                       Cmaj7    F


e------------1---------------0------|---------1---------------1------|
B------------1---------------1------|---------3---------------3------|
G------------2-----------2----------|-----2-------------------2------|
D----------------3---2-----------2--|-0-----------0-------0----------|
A--------3--------------------------|-----------------3-----------3--|
E----1------------------------------|--------------------------------|
     F               Am               Dm


e------------3---------------3------|-1-------------------------------|
B------------0---------------3------|-3-------------------------------|
G------------0---------------0------|---------------------------------|
D--------0---------------0----------|---------------------------------|
A----2---------------2--------------|---------------------------------|
E-----------------------------------|---------------------------------|
     G                                G7
(3)
3 times:
e-----------------------------------|---------0---------------0-------||
B------------1---------------0------|---------1---------------3-------||
G------------2---------------0------|---------0---------------1-------||
D------------3---------------0------|-----2---------------2-----------||
A--------3---------------2----------|-3-----------3---2---------------||
E----1-----------1---3-----------3--|------------------------------0--||
     F               G                C               Eadd4
                                            __________________________
                                            | 1st time only
e-----------------------------------|-0-------------------------------|
B------------1---------------0------|-1-------------------------------|
G------------2---------------0------|-0-----------0-------0-------0---|
D------------3---------------0------|-2-------------------------------|
A--------3---------------2----------|-3---------------0-------2-------|
E----1-----------1---3-----------3--|---------3-----------------------|
     F               G                C

Play as : (1) , (2)x2 , (3) , (2) , (3)
(I think)

Diki

';



?>

<h1>toFret demonstration</h1>
<br>
<textarea cols=80 rows=4>
//	Include the files
include('MusicNotes.class.php');
include('NoteWalk.class.php');
include('tab.class.php');</textarea

