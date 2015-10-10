<?php
requireFromLibrary('classes/fretboard/NoteWalk.class.php');

/*
Cymatic Frequency Numbers         
27, 54, 108, 216,432, 864, 1728
9, 18, 36, 72, 144, 288, 576 1152
81, 162, 324, 648, 1296 
*/

Class MusicNotes {

    public $allNotes            = array();
    public $possibleAdditions   = array();
    public $allNotesFlipFlat    = array();
    public $allNotesFlipSharp   = array();
    public $guitarStrings       = array();
    public $numberOfNotes;
    
    protected $debug = true;





    public function __construct() {
        $this->defineAllNotes();        //  sets the tone. By design it is C based, could be used for exotic tunings.
        $this->defineChordIntervals();  //  defines chords, scales and modes.
        $this->defineGuitarStrings();   //  defines string order for a range of instruments    
    }
    


    /*
     *      The one and only definition of all musical notes used in the system.

            Designed for the western C centered tuning,
            it might be used with other types of tunings.
     
     *      Every application will use this note order:   
     *
     */
    protected function defineAllNotes() {

        $this->setNoteSystem();

        $countArr = array();
        foreach ( $this->allNotes as $note => $index ) {
            $countArr[$index][] = $note;
        }
        foreach ( $countArr as $i => $countedRow  ) {
            if ( count($countedRow) == 2 ) {
                //  This a sharp / flat same note situation
                //  map them to each other.
                $this->flatSharpLookup [$countedRow[0]] = $countedRow[1];
                $this->flatSharpLookup [$countedRow[1]] = $countedRow[0];               
            }
        }

        // create flat and sharp lookups for the numeric keys.
        $this->allNotesFlipFlat = array_flip(               $this->allNotes       );
        $this->allNotesFlipSharp= array_flip( array_reverse($this->allNotes, true ));                           
        ksort($this->allNotesFlipSharp);

        $this->numberOfNotes();
    }


        
    /*
     *  Set and gets the number of notes in the note system.
     *
     */
    public function numberOfNotes() {
        if ( isset($this->allNotesFlipFlat) && is_array($this->allNotesFlipFlat) ) {
            $this->numberOfNotes = count($this->allNotesFlipFlat);
            return $this->numberOfNotes;
        }
    }


    /*
     *  Set a note system. Standard is "C".
     *  Options:
            hexachord

     *
     */
    public function setNoteSystem( $noteSystemName = NULL ) {

        // always define the sharp note first and then the flat note. 
        // you can't define more than 2 notes per note_index, things will go weird. Try it.
        // This is the assumed order in other functions



        // https://nl.wikipedia.org/wiki/Hexachord
        $noteSystems['hexachord']['allNotes']['F'] = 1;
        $noteSystems['hexachord']['allNotes']['G'] = 2;
        $noteSystems['hexachord']['allNotes']['A'] = 3;
        $noteSystems['hexachord']['allNotes']['Bb'] = 4;
        $noteSystems['hexachord']['allNotes']['Cb'] = 4;
        $noteSystems['hexachord']['allNotes']['C'] = 5;
        $noteSystems['hexachord']['allNotes']['D'] = 6;
        $noteSystems['hexachord']['allNotes']['E'] = 7;

        $noteSystems['hexachord']['chordIntervals']['major'] = array(1,5,8); 
        $noteSystems['hexachord']['chordIntervals']['major6'] = array(1,5,8,10); 
        $noteSystems['hexachord']['chordIntervals']['major9'] = array(1,5,8,12,3); 


        //  Get rid of the 'guitar' thingie in there.
        $noteSystems['hexachord']['guitarStrings']['guitar'][0] = E;
        $noteSystems['hexachord']['guitarStrings']['guitar'][1] = G; 
        $noteSystems['hexachord']['guitarStrings']['guitar'][2] = C; 
        $noteSystems['hexachord']['guitarStrings']['guitar'][3] = A; 





        if ( isset( $noteSystems[$noteSystemName] ) ) {
            foreach ($noteSystems[$noteSystemName] as $variableName => $definitionArray) {
                foreach ( $definitionArray as $noteName => $notePosition ) {



                    if ($variableName == 'allNotes') {
                        $this->allNotes[$noteName] = $notePosition;                        
                    }
                    if ($variableName == 'chordIntervals') {
                        $this->chordIntervals[$noteName] = $notePosition;                        
                    }
                    if ($variableName == 'guitarStrings') {
                        $this->guitarStrings[$noteName] = $notePosition;                        
                    }

                }
            }
        } else {
            //$this->debug('Unknown $noteSystemName: ' . $noteSystemName);

            //              note   note_index
            $this->allNotes['C']    = 1;
            $this->allNotes['C#']   = 2; // sharp C
            $this->allNotes['Db']   = 2; // flat D
            $this->allNotes['D']    = 3;
            $this->allNotes['D#']   = 4; // sharp D
            $this->allNotes['Eb']   = 4; // flat E
            $this->allNotes['E']    = 5;
            $this->allNotes['F']    = 6;
            $this->allNotes['F#']   = 7; // sharp F
            $this->allNotes['Gb']   = 7; // flat G
            $this->allNotes['G']    = 8;
            $this->allNotes['G#']   = 9; // sharp G
            $this->allNotes['Ab']   = 9; // flat A
            $this->allNotes['A']    = 10;
            $this->allNotes['A#']   = 11; // sharp A
            $this->allNotes['Bb']   = 11; // flat B
            $this->allNotes['B']    = 12;
        
        }

        unset($this->allNotesFlipFlat);
        unset($this->allNotesFlipSharp);

    }


    protected function defineChordIntervals () {

        /*          
         http://www.hearandplay.com/main/learn-these-most-common-chord-progression-types-and-never-get-stuck-again
         http://www.vt2000.com/basswork/chords/interval.htm for the intervals
         http://www.scales-chords.com/chdbmain.php
         
                C  *  D  *  E  F  *  G   *  A   *   B
                1  2  3  4  5  6  7  8   9  10  11  12 


        */
        /*
            Caug7 has an added minor seventh:  C – E – G♯ – B♭
        */
        $this->chordIntervals['major']      = array(1,5,8);          //  C E G
        $this->chordIntervals['minor']      = array(1,4,8);          //  C Eb G
        $this->chordIntervals['augmented']  = array(1,5,9);          //  C F G#
        $this->chordIntervals['diminished'] = array(1,4,7);          //  verified
        $this->chordIntervals['sus4']       = array(1,6,8);          //  verified
        $this->chordIntervals['sus2']       = array(1,3,8);          //  verified   
        $this->chordIntervals['major6']     = array(1,5,8,10);       //  C E G A 
        $this->chordIntervals['major7']     = array(1,5,8,12);       //  verified
        $this->chordIntervals['minor6']     = array(1,4,8,10);       //  C Eb G A 
        $this->chordIntervals['minor7']     = array(1,4,8,11);       //  verified
        $this->chordIntervals['augmented7'] = array(1,5,9,11);       //  C – E – G♯ – B♭
        $this->chordIntervals['add9']       = array(1,5,8,3);        //  C E G D 
        $this->chordIntervals['madd9']      = array(1,4,8,3);        //  C D# G D 
        $this->chordIntervals['7sus4']      = array(1,6,8,11);       //  C F G Bb
        $this->chordIntervals['diminished7']= array(1,4,7,10);       //  verified 
        $this->chordIntervals['dominant7th']= array(1,5,8,11);       //  C E G Bb also known as C7
        $this->chordIntervals['dominant9th']= array(1,5,8,11,3);     //  C E G Bb D also C9  
        $this->chordIntervals['minor9']     = array(1,4,8,11,3);       // C, Eb, G, Bb and D.  
        $this->chordIntervals['9sus4']      = array(1,6,8,11,3);     //  C F G Bb D  
        $this->chordIntervals['major9']     = array(1,5,8,12,3);     //  C E G B D 
        $this->chordIntervals['major11']    = array(1,6,8,12,3);     //  C F G B D 
        $this->chordIntervals['minor9']     = array(1,4,8,11,3);     //  C Eb G Bb D    
        $this->chordIntervals['major13']    = array(1,5,8,12,3,6,10);//  C E G B D F A 
        //$this->chordIntervals['C7 #9#5']    = array(1,5,7,11,4);//  C E G# Bb D#
        $this->chordIntervals['m7b5']       = array(1,4,7,11);//  C   E♭  G♭  B♭
                      
        $this->scaleIntervals['major']               = array(1,3,5,6,8,10,12);   //  C; D; E; F; G; A; B;
        $this->scaleIntervals['minor']               = array(1,3,4,6,8,9,11);    //  C, D, Eb, F, G, Ab and Bb.
        $this->scaleIntervals['Minor pentatonic']    = array(1,4,6,8,11);        //  C, Eb, F, G and Bb.
        $this->scaleIntervals['major pentatonic']    = array(1,3,5,8,10);        //  C; D; E; G; A; C;       
        $this->scaleIntervals['Harmonic minor']      = array(1,3,4,6,8,9,12);    //  C, D, Eb, F, G, Ab and B.
        $this->scaleIntervals['Melodic minor']       = array(1,3,4,6,8,10,12);   //  C, D, Eb, F, G, A and B.
        $this->scaleIntervals['Diminished']          = array(1,3,4,6,7,9,10,12); // C, D, Eb, F, Gb, Ab, A and B.
        $this->scaleIntervals['Blues']               = array(1,4,6,7,8,11);      //  C, Eb, F, Gb, G and Bb.
                

        $this->modeIntervals['Dorian mode']          = array(1,3,4,6,8,10,11);   //  D Dorian mode       is the same as a C major
        $this->modeIntervals['Phrygian mode']        = array(1,2,4,6,8,9,11);    //  E Phrygian mode     is the same as a C major
        $this->modeIntervals['Lydian mode']          = array(1,3,5,7,8,10,12);   //  F Lydian mode       is the same as a C major
        $this->modeIntervals['Mixolydian mode']      = array(1,3,5,6,8,10,11);   //  G Mixolydian mode   is the same as a C major
        $this->modeIntervals['Locrian mode']         = array(1,2,4,6,7,9,11);    //  B Locrian mode   
      
    }   
        

    public function getChordNameRewrite($chordRequest=false) {

        if(isset($this->chordIntervals[$chordRequest])) {
            return $chordRequest;
        }

        // Some common ways to write chords
        $commonNames = array();
        $commonNames['M']     = 'major';
        $commonNames['maj']   = 'major';
        $commonNames['min']   = 'minor';
        $commonNames['m']     = 'minor';
        $commonNames['aug']   = 'augmented';
        $commonNames['+']     = 'augmented';
        $commonNames['%2B']   = 'augmented';
        $commonNames['dim']   = 'diminished';
        $commonNames['-']     = 'diminished';
        $commonNames['C7']    = 'dominant7th';  
        $commonNames['C9']    = 'dominant9th';  
        
        // Apply seventh and ninth notes to the chord 
        $extraNotes = array(7, 9, 11, 13);      
        foreach ($commonNames as $chordNameAlt => $realName) { 
            foreach ( $extraNotes as $extraNoteName ) {
                $commonNames[$chordNameAlt.$extraNoteName] = $realName.$extraNoteName;  
            }       
        }
        
        //$this->debug($commonNames);


        if ( is_string($chordRequest) && isset($commonNames[$chordRequest]) ) {
            return $commonNames[$chordRequest];
        }   
        return false;       
    }   
    
    
    protected function defineGuitarStrings() {
        $this->guitarStrings['guitar'][0] = 'E'; // set to D for drop D tuning
        $this->guitarStrings['guitar'][1] = 'A';
        $this->guitarStrings['guitar'][2] = 'D';
        $this->guitarStrings['guitar'][3] = 'G';
        $this->guitarStrings['guitar'][4] = 'B';
        $this->guitarStrings['guitar'][5] = 'E';        
        
        $this->guitarStrings['bass'][0] = 'E';      
        $this->guitarStrings['bass'][1] = 'A';
        $this->guitarStrings['bass'][2] = 'D';
        $this->guitarStrings['bass'][3] = 'G';
                        
        $this->guitarStrings['ukulele'][0] = 'G';       
        $this->guitarStrings['ukulele'][1] = 'C';
        $this->guitarStrings['ukulele'][2] = 'E';
        $this->guitarStrings['ukulele'][3] = 'A';
        
        $this->guitarStrings['banjo'][0] = 'G';     
        $this->guitarStrings['banjo'][1] = 'D';
        $this->guitarStrings['banjo'][2] = 'G';
        $this->guitarStrings['banjo'][3] = 'B';
        $this->guitarStrings['banjo'][4] = 'D'; 
                    
        $this->guitarStrings['mandolin'][0] = 'G';      
        $this->guitarStrings['mandolin'][1] = 'D';
        $this->guitarStrings['mandolin'][2] = 'A';
        $this->guitarStrings['mandolin'][3] = 'E';

        foreach ( $this->guitarStrings as $allowedInstrument => $strings ) {
            $this->allowedInstrumentTypes[] = $allowedInstrument;
        }       
    }
    
    
    
    protected function getScaleNameRewrite($scaleRequest=false) {

        // Some common ways to write scales
        $commonNames = array();
        $commonNames['M']     = 'major';    
        $commonNames['maj']   = 'major';    
        $commonNames['min']   = 'minor';    
        $commonNames['m']     = 'minor';    
        $commonNames['aug']   = 'augmented';    
        $commonNames['+']     = 'augmented';
        $commonNames['%2B']   = 'augmented';
        $commonNames['dim']   = 'diminished';       
        
        // Apply seventh and ninth notes to the scale 
        $extraNotes = array(7, 9);      
        foreach ($commonNames as $scaleNameAlt => $realName) { 
            foreach ( $extraNotes as $extraNoteName ) {
                $commonNames[$scaleNameAlt.$extraNoteName] = $realName.$extraNoteName;  
            }       
        }   

        if ( is_string($scaleRequest) && isset($commonNames[$scaleRequest]) ) {
            return $commonNames[$scaleRequest];
        }   
        return $commonNames;        
    }   
    
    
    public function getGuitarStrings() {
        return $this->guitarStrings;
    }
        
    

    function getSharpFlat () {
        
        $countSharp = count($this->allNotesFlipSharp);      
        $countFlat  = count($this->allNotesFlipFlat);
        
        $sharpFlatLookup = array();
        if ( $countSharp == $countFlat && $countFlat > 0 ) {
        
            foreach ( $this->allNotesFlipFlat as $noteNumber => $noteName ) {
                if ( isset( $this->allNotesFlipSharp[$noteNumber] ) && $noteName != $this->allNotesFlipSharp[$noteNumber] ) {
                    
                    // This is a note with a sharp and flat.                    
                    $sharpFlatLookup[$this->allNotesFlipFlat[$noteNumber]]  = $this->allNotesFlipSharp[$noteNumber];
                    $sharpFlatLookup[$this->allNotesFlipSharp[$noteNumber]] = $this->allNotesFlipFlat[$noteNumber];                                     
                }
            } // foreach
        
        }
        return $sharpFlatLookup;        
    }   


    /*
     *      Get possible chords for the notes in the array
     *      Used for reverse chord lookup.
     *
     *      Returns chords that each contain all notes in $noteArray 
     *
     */
     public function getChordsForNotes( $notesArray ) {

        if ( ! is_array($notesArray) ) {
            $this->debug("getChordsForNotes( notesArray ) : notesArray is not an array.");
            $this->debug($notesArray);
            return;
        }


        $return = array();
        //  First get the chord types
        foreach ( $this->chordIntervals as $chordName => $intervals ) {
            // Now get all notes and apply the chord types to them.
            foreach ( $this->allNotes as $note => $blub  ) {                
                // Get the notes in the current Root note for the current Chord
                $chordInterval = $this->getChordInterval($note, $chordName);
                //convert to note ID to accomodate for sharps and flat notes being the same
                $chordNoteIds = array();
                foreach( $chordInterval as $chordNoteName ) {
                    $chordNoteIds[] = $this->allNotes[$chordNoteName];
                }
                
                // Check if all notes from $notesArray are in the chord, count how many are not             
                $rejected = 0;
                foreach ( $notesArray as $requestedNote ) {
                    if ( !in_array($this->allNotes[$requestedNote], $chordNoteIds   ) ) {
                        $rejected ++;
                    } 
                }
                        
                $thisChord['note']          = $note;
                $thisChord['chord']         = $chordName;
                $thisChord['chordNotes']    = $chordInterval;
        
                // $return[0] contains chords with only these notes
                // $return[1] contains chords that miss one note from the given notes.
                // $return[2] contains chords that miss two notes from the given notes.
                // etc.
                $return[$rejected][] = $thisChord;


                            
            } // foreach
        } // foreach    
        //$this->debug($return);
        return $return;
    }
    
    /*
     *      Get possible chords for the notes in the array
     *      Used for reverse chord lookup.
     *
     *      Returns sets of chords that together contain all notes in $noteArray 
     *
     */
    public function getCombinedChordsForNotes( $notesArray, $limit = 3 ) {
        if ( ! is_numeric($limit) ) { $limit = 3; }
        if ( ! is_array($notesArray) ) {
            return;
        }
                
        $return = array();
        //  First get the chord types
        foreach ( $this->chordIntervals as $chordName => $intervals ) {
            // Now get all notes and apply the chord types to them.
            foreach ( $this->allNotes as $note => $blub  ) {                
                // Get the notes in the current Root note for the current Chord
                $chordInterval = $this->getChordInterval($note, $chordName);
                //convert to note ID to accomodate for sharps and flat notes being the same
                $chordNoteIds = array();
                foreach( $chordInterval as $chordNoteName ) {
                    $chordNoteIds[] = $this->allNotes[$chordNoteName];
                }
                
                // Check if all notes from $notesArray are in the chord, count how many are not             
                $rejected = 0;
                foreach ( $notesArray as $requestedNote ) {
                    if ( !in_array($this->allNotes[$requestedNote], $chordNoteIds   ) ) {
                        $rejected ++;
                    } 
                }
                        
                $thisChord['note']          = $note;
                $thisChord['chord']         = $chordName;
                $thisChord['chordNotes']    = $chordInterval;
        
                // $return[0] contains chords with only these notes
                // $return[1] contains chords that miss one note from the given notes.
                // $return[2] contains chords that miss two notes from the given notes.
                // etc.
                
                // Here we want to get the second level of the matches, whic includes the first. So we can drop it 
                $rejected -= 1;
                $return[$rejected][] = $thisChord;
                            
            } // foreach
        } // foreach    
                
        return $return;     
    }
    
    
    /*
     *      Get possible scales for the notes in the array
     *      Used for reverse scale lookup.
     *
     */
    public function getScalesForNotes( $notesArray ) {
        if ( ! is_array($notesArray) ) {
            return;
        }       
        $return = array();
        //  First get the scale types
        foreach ( $this->scaleIntervals as $scaleName => $intervals ) {                 
            // Now get all notes and apply the scale types to them.
            foreach ( $this->allNotes as $note => $blub  ) {                
                // Get the notes in the current Root note for the current scale
                $scaleInterval = $this->getScaleInterval($note, $scaleName);
                //convert to note ID to accomodate for sharps and flat notes being the same
                $scaleNoteIds = array();
                foreach( $scaleInterval as $scaleNoteName ) {
                    $scaleNoteIds[] = $this->allNotes[$scaleNoteName];
                }
                
                // Check if all notes from $notesArray are in the scale, count how many are not             
                $rejected = 0;
                foreach ( $notesArray as $requestedNote ) {
                    if ( !in_array($this->allNotes[$requestedNote], $scaleNoteIds   ) ) {
                        $rejected ++;
                    }                                   
                }
                
                $thisScale['note']       = $note;
                $thisScale['scale']      = $scaleName;
                $thisScale['scaleNotes'] = $scaleInterval;
        
                $fixedNotes = $this->fixSharpFlat($thisScale['scaleNotes']);

                if ( $fixedNotes ) {
                    $return[$rejected][] = $thisScale;
                }
                
                            
            } // foreach
        } // foreach    
        return $return;
    }
    

    
    
    public function extractNotesFromText($textIn) {
         
        
        $return = array();
                        
        if ( is_array($textIn) )    {
            $exploded = $textIn;
        } else {
            $delimiter = ' ';           
            $textIn = str_replace('-', $delimiter, $textIn);
            $textIn = str_replace('_', $delimiter, $textIn);
            $textIn = str_replace(',', $delimiter, $textIn);
            $textIn = str_replace('.', $delimiter, $textIn);
            $textIn = str_replace(';', $delimiter, $textIn);
            $textIn = str_replace(':', $delimiter, $textIn);
            $textIn = str_replace('%', $delimiter, $textIn);
            $textIn = str_replace('>', $delimiter, $textIn);
            $textIn = str_replace('<', $delimiter, $textIn);
            $textIn = str_replace('/', $delimiter, $textIn);    
            $textIn = str_replace('&', $delimiter, $textIn);    
            $textIn = str_replace('^', $delimiter, $textIn);    
            $textIn = str_replace('*', $delimiter, $textIn);    
            $textIn = str_replace('+', $delimiter, $textIn);    
            $textIn = str_replace('=', $delimiter, $textIn);    
            $textIn = str_replace("\t", $delimiter, $textIn);
            $textIn = str_replace("\n", $delimiter, $textIn);
            $textIn = str_replace("\r", $delimiter, $textIn);
                        
            $textIn = str_replace('      ', $delimiter, $textIn);
            $textIn = str_replace('    ', $delimiter, $textIn);
            $textIn = str_replace('   ' , $delimiter, $textIn);
            $textIn = str_replace('  '  , $delimiter, $textIn);

            
            $exploded = explode($delimiter, $textIn);
        }
                
             
        foreach ( $exploded as $possibleNote ) {
            if ( !$possibleNote ) {
                continue;
            }
            
            $noteLength = 0;
            $fullInput = trim($possibleNote);

            
            $possibleNote = strtoupper($possibleNote);
            if ( strtolower( substr($possibleNote, 1, 1) ) == 'b' ) {
                // flat Half step down
                $possibleNote = strtoupper( substr($possibleNote, 0, 1) ) . 'b';
                $noteLength = 2;               
            } elseif( substr($possibleNote, 1, 1)  == '#' || strtolower(substr($possibleNote, 1, 5))  == 'sharp' ) {
                // sharp Half step up
                $possibleNote = strtoupper( substr($possibleNote, 0, 1) ) . '#';
                $noteLength = 2;               
            } else {
                $possibleNote = strtoupper( substr($possibleNote, 0, 1) );
                $noteLength = 1;
            }


            
            if ( isset($this->allNotes[$possibleNote] ) )  {


                $fullInputLength = strlen($fullInput);
                if( $noteLength && $fullInputLength > $noteLength ) {
                    $possibleChordName  = substr($fullInput, 1, $fullInputLength - 1 );
                    $chordnameRewrite   = $this->getChordNameRewrite($possibleChordName);


                    if(strlen($chordnameRewrite)) {
                        $fullChordName = $possibleNote . $chordnameRewrite;
/*
                        $this->debug($possibleNote);
                        $this->debug($fullChordName);
                        $this->debug($chordnameRewrite);
*/


                        // Get the notes in the current Root note for the current Chord
                        $chordInterval = $this->getChordInterval($possibleNote, $chordnameRewrite);
                        //convert to note ID to accomodate for sharps and flat notes being the same
                        $chordNoteIds = array();
                        //foreach( $chordInterval as $chordNoteName ) {
                            //$return['chord_notes'][$fullChordName][] = $chordNoteName;
                        //}
                        //$this->debug($chordInterval);
                    }

                }

                if ( isset($return[$possibleNote]) && is_numeric($return[$possibleNote]['count']) ) {
                    $return['root_notes'][$possibleNote]['count'] ++;
                } else {
                    $return['root_notes'][$possibleNote]['count'] = 1;
                }
               
            } else { 
                //$this->debug("unknown ROOT note: $possibleNote");
            }           
        } // foreach
        //$this->debug($return);
        $return = $this->fixSharpFlat($return);
        return $return;
    } // function extractNotesFromText
    
                        
    public function getChordInterval($chord, $chordType = 'major') {
            
        if ( !strlen($chordType) ) {
            $chordType = 'major';
        }
        
        $startNote   = $this->extractNotesFromText($chord);
        $rootNote    = $startNote['root_notes'];
        $rootNote    = key($rootNote);                          
        $notesWalked = $this->walkNotes( $rootNote, $this->numberOfNotes );
        
        $return          = array();     
        if ( isset($this->chordIntervals[$chordType])  ) {
            
            //      Decide to show sharps or flats:
            $sharpOrFlat = 'sharp';
            if ( stristr($rootNote, '#') || stristr($rootNote, 'sharp') ) {
                $sharpOrFlat = 'sharp';
            } elseif (  stristr($rootNote, 'b')  || stristr($rootNote, 'flat') ) {
                $sharpOrFlat = 'flat';
            }           
            
            foreach ($this->chordIntervals[$chordType] as $y => $step) {
                if ( isset($notesWalked[$step]) ) {
                    $noteToShow = $notesWalked[$step];
                    
                    $noteNum = $this->allNotes[$noteToShow];                    
                    if ($sharpOrFlat == 'flat') {
                        $noteToShow = $this->allNotesFlipFlat[$noteNum];                    
                    } elseif ($sharpOrFlat == 'sharp') {
                        $noteToShow = $this->allNotesFlipSharp[$noteNum];                   
                    }                   

                    $return[] = $noteToShow;                    
                }
            } //foreach
        } 
        //$this->debug($return);
        $return = $this->fixSharpFlat($return);
        return $return;
    }
              
                        
    public function getScaleInterval($scale, $scaleType = 'major') {

        if ( !strlen($scaleType) ) {
            $scaleType = 'major';
        }
        
        $startNote   = $this->extractNotesFromText($scale);
        $rootNote    = $startNote['root_notes'];
        $rootNote    = key($rootNote);                          
        $notesWalked = $this->walkNotes( $rootNote, $this->numberOfNotes);
        
        $scaleTypes  = $this->getScaleNameRewrite($scaleType);      
        if ( is_string($scaleTypes) ) {
            $scaleType = $scaleTypes;
        }   
        $return          = array();     
        if ( isset($this->scaleIntervals[$scaleType]) ) {
            foreach ($this->scaleIntervals[$scaleType] as $y => $step) {
                if ( isset($notesWalked[$step]) ) {
                    $return[] = $notesWalked[$step];
                }               
            } //foreach
        }       
        //$this->debug($return);
        $return = $this->fixSharpFlat($return);
        return $return;
    }

    /*
     *  Applies the rule that no note name can be in a sequence twice
     *  by changing flats to sharps to avoid this.
     */
    protected function fixSharpFlat($noteArray) {

        $sharpFlat['b'] = '#';
        $sharpFlat['#'] = 'b';

        $noteArrayFlipped = array_flip($noteArray);

                        //$this->debug($noteArray);
        $count = 0;
        foreach ($noteArray as $key => $note) {

            // skip the first loop. The first note is mostly the ROOT note, don't change that one.
            if ($count) {
                if ( strlen($note) == 2 ) {
                    if( isset($noteArrayFlipped[ $note[0] ]) ) {

                        $alternativeName = $this->flatSharpLookup[$note];
                        $noteArray[$key] = $alternativeName;                        
/*
                        $this->debug("{$note}: {$note[0]} " 
                                        . 'This root note is already in the sequence. ' 
                                        . " replaced by {$noteArray[$key]}"
                                        . " root {$alternativeName[0]}"
                                        );
                              */          
                        if( isset($noteArrayFlipped[ $alternativeName[0] ]) ) {
                            //$this->debug('The alternative ' . " $alternativeName root note {$alternativeName[0]} is also present. What tot do?");
                            //$this->debug($noteArray);
                        } 

                    }
                }
            } // END skip the first loop


            $count ++;
        } // foreach
        //$this->debug($noteArray);

        return $noteArray;
    }
          

    protected function getGuitareStringRange($from, $to, $typeOfTuning) {   
        if ( !is_numeric($to) )  {
            $to = 188; // full piano range
        }
        
        if( in_array($typeOfTuning, $this->allowedInstrumentTypes) ) {
            $stringsForInstrument = $this->guitarStrings[$typeOfTuning];
            $from = 0;
            $to     = count($stringsForInstrument);
        }
        
        $result = array();
        if ( is_numeric($from) && isset($stringsForInstrument[$from]) ) {
            foreach ( $stringsForInstrument as $numString => $note ) {              
                if ( $numString >= $from && $numString <= $to ) {
                    $result[$numString] = $note;
                }
            } // foreach
        }
        $result = array_reverse($result, true);
        return $result;
    }   
    
  
    public function walkNotes( $startNote = 'C', $walkThisNumber = 12, $flatSharp = 'sharp' ) {

        if (!$walkThisNumber) {
            $walkThisNumber = $this->numberOfNotes;
        }

        if ( $startNote && isset($this->allNotes[$startNote]) ) {
            $realNoteId = $this->allNotes[$startNote];
            
            if ($flatSharp == 'sharp' || $flatSharp == '#') {
                $noteLookup = $this->allNotesFlipSharp;     
            }
            if ($flatSharp == 'flat' || $flatSharp == 'b') {
                $noteLookup = $this->allNotesFlipFlat;      
            }
            
            $return = array();
            $noteNumber = $realNoteId;
            $cummulative= 1;
            while( $walkThisNumber > 0 ) {          
                $walkThisNumber --;     
                if ( $noteNumber > count($noteLookup) ) {
                    $noteNumber = 1;
                }
                $return[$cummulative] = $noteLookup[$noteNumber];
                $noteNumber  ++;
                $cummulative ++;
            }
            return $return;             
        }               
    } // function walkNotes
    
    
  

    protected function debug( $input ) {
        if ( $this->debug === true ) { 
        
            $newLine = "\n";
            $htmlBr  = '<br />';
                
            if( is_string($input) || is_numeric($input) ) {
                echo "{$newLine}{$htmlBr}{$input}";
            } elseif ( is_array($input) || is_object($input) ) {
                echo "{$newLine}<pre>{$newLine}";
                print_r($input);
                echo "{$newLine}</pre>{$newLine}";
            } else {
                if ( $input ) {
                    echo "{$newLine}<pre>{$newLine}";
                    var_dump($input);
                    echo "{$newLine}</pre>{$newLine}";
                }
            }
        }
    }
    
    
    
    


    /*
     *      find other chords that contain one more or one less notes
     *      
            F with G => F add9 
            
     */
    public function getRelatedChords( $inputChord ) {
    
        
    
    }
    
    
    
}
?>