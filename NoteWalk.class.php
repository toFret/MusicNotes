<?php
//requireFromLibrary('classes/fretboard/MusicNotes.class.php');


Class NoteWalk extends MusicNotes {

	protected $startNote;	
	protected $pickNote;	
	
	public function __construct( $startNote ) {
		parent::__construct();
		
		
		if ( isset( $this->allNotes[$startNote] ) ) {
			$this->startNote = $startNote;
		} else {
			//$this->debug( debug_backtrace(false) );
			$this->debug( "NoteWalk.class:: NO NOTE: " . $startNote); 
		}		
	}
	
	
	public function nextNote() {
		return $this->pickNote( +1 );
	}
	
	public function previousNote() {
		return $this->pickNote( -1 );
	}
	
	public function pickNote( $numSteps ) {
		
		if (0 === $numSteps) {
			return $this->startNote;
		}
		
		if ( is_numeric($numSteps) ) {
			if ( $numSteps < 0 ) {
				while($numSteps < 0) {
					$numSteps += count($this->allNotesFlipFlat);
				}			
			}
		}
						
		$notes 		= $this->walkNotes( $this->startNote, $numSteps+1, 'sharp' );	
		$noteWanted = $notes[ count($notes) ]; // get last element
		
		return $noteWanted;			
	}
	
	
}
?>