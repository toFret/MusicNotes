<?php


Class TAB extends MusicNotes {
        
    public $uniqueString;
    public $uniqueDiv;
        
        
    protected $debug = true;
    
    /*
     *  Returns a textual TAB for a bass Guitar
     */
    public function getBassGuitarTab( $options )  {
        
        //$this->debug($options);
        
        // apply default settings when not set in options:
        $defaultOptions['numstrings']           = 4; // because bass
        $defaultOptions['showNumbers']          = true;
        $defaultOptions['showDots']             = true;
        $defaultOptions['numberOfFretsToGet']   = 12;
        $defaultOptions['onlyThesePositions']   = array();
        $defaultOptions['notes']                = '';
        $defaultOptions['instrument']           = 'guitar';
        
        foreach ( $defaultOptions as $defaultOptionName => $defaultOption ) {   
            if ( !isset( $options[$defaultOptionName] ) ) {
                $options[$defaultOptionName] = $defaultOptions[$defaultOptionName];
            }       
        } // foreach
        

        //$firstString  = 1; // Standard low E = 1, low B = 0       
        //$lastString   = 4; // 4 = bass guitar / 6 = guitar
                

        $options['showNumbers'] = isset($options['showNumbers']) ? $options['showNumbers'] : true; 
        $options['showDots']    = isset($options['showDots'])   ? $options['showDots']    : true; 

        $newLine = "\n";
        //$newLine = "";
        //$htmlBr  = '<br />';
        $htmlBr  = '';
        $onlyTheseNotes     = array();

        if( in_array($options['instrument'], $this->allowedInstrumentTypes) ) {
        //$this->debug('instrument' . $options['instrument'] );
        //$this->debug($this->allowedInstrumentTypes );
            $instrumentType = $options['instrument'];
        }
                
        if ( isset($options['numberOfFretsToGet']) && is_numeric($options['numberOfFretsToGet']) ) {                
            $numberOfFretsToGet = $options['numberOfFretsToGet'];
        } else {
            $numberOfFretsToGet = 12;
        }
            
        $bassStrings = $this->getGuitareStringRange($firstString, $lastString, $instrumentType);
        
        
        if ( isset($options['notes']['onlyThesePositions']) ) {         
            $onlyTheseNotes = $options['notes'];
        } elseif ( isset($options['notes']) ) {
            $notes              = $this->extractNotesFromText($options['notes']);   
            $onlyTheseNotes     = isset($notes['root_notes']) ? $notes['root_notes'] : '';
        }
        if ( is_array($bassStrings) ) {
            /*
             *      Make something random
             */
            $uniqueId       = rand(1, 999);
            $uniqueId2      = rand(1, 999);
            $this->uniqueDiv    = $uniqueId * $uniqueId2 - strlen($uniqueId) - strlen($uniqueId2);          
                
            $TABhtml        = $htmlBr . $newLine;

            // create a top line for visual effects on top          
            //$TABhtml  .= $this->createTabLine('Topbar', $numberOfFretsToGet + 1); // compensation for          
            //$TABhtml  .= $htmlBr . $newLine ;

            foreach ($bassStrings as $stringId => $rNote) {
                $TABhtml    .= $this->createTabLine($rNote, $numberOfFretsToGet, $onlyTheseNotes, $stringId+1);
                $TABhtml    .= $htmlBr . $newLine;              
            }           
            // create an empty line for visual effects on the bottom            
            $TABhtml    .= ' &nbsp;';   
            $TABhtml    .= $htmlBr . $newLine ;
            
            if ( isset($options['showNumbers']) && $options['showNumbers'] ) {
                // create a line of numbers at every fret
                $TABhtml    .= $this->createTabLine('Numbers', $numberOfFretsToGet); 
                $TABhtml    .= $htmlBr . $newLine;
            }
        }
        return $TABhtml;        
    } // END function getBassGuitarTab
   
   

    public function getTabPlayer( $tablature, $javascriptIncluded = true, $options = array() ) {
        

        $tabsFound = $this->analyzeTab ( $tablature );              
        
        foreach($tabsFound['valid_blocks'] as $i => $tabFound) {
            /*  echo '<pre>';
            print_r($tabFound);
            echo '</pre>';  */
            $cleanedTabString = "\n" . implode("\n", $tabFound['tab_line_block']) . "\n";                   
            $cleanedTabString = "\n" . implode("\n", $tabFound['tab_line_block_interactive']) . "\n";                   
            
                        
            $options                                = $options;
            $options['numstrings']                  = $tabFound['number_of_strings'];
            $options['notes']['onlyThesePositions'] = $tabFound['positions'];
                                
            if ( isset($_POST['numberOfFretsToGet']) && is_numeric($_POST['numberOfFretsToGet']) )  {
                $options['numberOfFretsToGet']      = $_POST['numberOfFretsToGet'];
            } 
            if ( isset($_POST['showdots']) && $_POST['showdots'] )  {
                $options['showDots']                = $_POST['showdots'];
            }
            if ( isset($_POST['showstringnr']) && $_POST['showstringnr'] )  {
                $options['showNumbers']             = $_POST['showstringnr'];
            }
            
            $options['instrument']                  = isset($_POST['instrument']) ? $_POST['instrument'] : NULL;                    
                    
            $notesUsed = $this->extractNotesFromTab( $tabFound['positions'], $tabFound['open_notes']  );                            
                                                                    
            $templateObject = new Template();
            $templateObject->assign('tab_lines_interactive', $cleanedTabString );
            $templateObject->assign('notesUsed'     , $notesUsed );
            $templateObject->assign('songName'      , $songName);
            $templateObject->assign('songComment'   , implode("\n<br>",$tabFound['comments']) );
            $templateObject->assign('songPartNotes' , '<pre>' .$cleanedTabString . '</pre>');
            $templateObject->assign('tabString'     , $this->getBassGuitarTab($options));   
            $templateObject->assign('tabObject'     , $tab);
            $templateObject->assign('options'       , $options);
            
            
            if (  isset($_POST['print_button']))    {
                $templateObject->assign('dontShowControls', true);
                // Load some print css or something
                // define area to print (exclude sidebars and pics etc)
            }
            
                            
            
            $templateObject->assign('showInput', true);
            if ( isset($_POST['showInput']) && !$_POST['showInput'] )   {
                $templateObject->assign('showInput', false);
            }
            
            if(!$javascriptIncluded) {
                // include only once. this is a fragile way to only include javascript code once.
                $templateObject->assign('javascript'    , 'fretboard_control');
                $javascriptIncluded = true;
            }
            
            if ($options['numstrings'] > 2 ) {
                $templateObject->display('tabs/oneTab.html', false);
            }
                                                                            
        } // foreach
    }
   
    
    /*
     *  Returns a textual TAB for a  Guitar
     */
    public function getGuitarTab( $options = array() )  {
                                
        $bassStrings    = $tab->getGuitarStrings();         

    }       

    public static function extractNotesFromTab($positions, $openStrings) {
        
        foreach( $positions as $stringNum => $position ) {          
            $startNote     = $openStrings[$stringNum];          
            $noteWalker    = new NoteWalk($startNote);
            $targetNotes[] = $noteWalker->pickNote( (int)$position );   
        }       
        foreach ( $targetNotes as $i => $targetNote ) {
            unset($targetNotes[$i]);
            $targetNotes[$targetNote] = $targetNote;        
        }       
        
        return $targetNotes;
    }



    public static function getTabDelimiters () {
            
        $tabLineDef['lineStart']        = ':';          
        $tabLineDef['delimiter']        = '|';          
        $tabLineDef['fret']             = '_';          
        $tabLineDef['dotFret']          = '&nbsp;';         
        $tabLineDef['noteClickOpen']    = '<span>';             
        $tabLineDef['noteClickClose']   = '</span>';  
            
        // TODO: 
        //$tabLineDef['leftActionOpen']   = '<span onclick="switchSharpFlat();return true;">';
        $tabLineDef['leftActionOpen']   = '';
        $tabLineDef['leftActionClose']  = '';
                
        $tabLineDef['rightActionOpen']  = '';               
        $tabLineDef['rightActionClose'] = '';   
            
            
        return $tabLineDef;
    }

                
    public function createTabLine( $rootNote, $numNotes = 24, $onlyTheseNotes = NULL, $stringIdentifier = NULL, $uniqueDiv = NULL ) {
    
        $tabDel = $this->getTabDelimiters();
        
        $lineStart       = $tabDel['lineStart'];
        $delimiter       = $tabDel['delimiter'];
        $fret            = $tabDel['fret'];
        $dotFret         = $tabDel['dotFret'];
        $noteClickOpen   = $tabDel['noteClickOpen'];
        $noteClickClose  = $tabDel['noteClickClose'];
        
        $leftActionOpen  = $tabDel['leftActionOpen'];
        $leftActionClose = $tabDel['leftActionClose'];      
        
        $rightActionOpen = $tabDel['rightActionOpen'];
        $rightActionClose= $tabDel['rightActionClose'];
                        
            
        /*
         *      Make something random and unique enough
         */
        $uniqueId               = rand(1, 999);
        $uniqueId2              = rand(1, 999);
        $this->uniqueString     = ( $uniqueId * $uniqueId2 ) + (strlen($uniqueId) + strlen($uniqueId2) ); 
                
                
        /*
         *  Some options
         */     
        $showDots       = false;    
        $showNumbers    = false;    
        $showTopbar     = false;    
        $fullString     = '';
        
        
        /*
         *  Catch "special" roots notes that are not notes, but display helpful rows of characters along the fretboard. 
         */
        if ($rootNote == 'Dots') {
            $showDots   = true; 
            $rootNote   = 'A'; // fake a note, otherwise getAllNotesFrom won't return anything
            $fullString = $dotFret . $dotFret . $dotFret;
        
        } elseif ($rootNote == 'Numbers') {
            $showNumbers    = true; 
            $rootNote   = 'A'; // fake a note, otherwise getAllNotesFrom won't return anything
            $fullString = '0' . $dotFret . $dotFret;
        
        } elseif ($rootNote == 'Topbar') {
            $showTopbar     = true; 
            $rootNote   = 'A'; // fake a note, otherwise getAllNotesFrom won't return anything
            $fullString = '_';
        } else {
            $numNotes ++;       
        }       
        
        
        
        $notesOnString = $this->walkNotes($rootNote, $numNotes);
        
        // Add a line with one fretboard dot on the 3rd, 5th, 7th, 9th and 2 on the 12th fret               
        $tSt[1] = 0 ;
        $tSt[2] = 0 ;
        $tSt[3] = 1 ;
        $tSt[4] = 0 ;
        $tSt[5] = 1 ;
        $tSt[6] = 0 ;
        $tSt[7] = 1 ;
        $tSt[8] = 0 ;
        $tSt[9] = 1 ;
        $tSt[10]= 0 ;
        $tSt[11]= 0 ;
        $tSt[12]= 2 ;               
            
        $cnt = 1;
        foreach ( $notesOnString as $noteId => $note ) {
            
            if ( !isset($cntDots) || !is_numeric($cntDots) )    {$cntDots = 0; }
            if ( $cntDots >= 12 )                               {$cntDots = 0; }
            $cntDots ++;
                    
            $fullStringDot = $fullString;
            if( $tSt[$cntDots])  {
                $cntTmp = $cnt;
                if ( $cntTmp >= 12 ) {$cntTmp = 1; }
                $tickDot =  str_repeat('.', $tSt[$cntTmp]);
            } else {
                $tickDot =  $dotFret;
            }       
            $fullStringDot .=   $dotFret.$dotFret . $tickDot . $dotFret;

            if ($tSt[$cntDots] != 2) {
                // Dont use this extra one if there are two dots.
                $fullStringDot .=   $dotFret;
            }               
            $fullStringDot .= $dotFret;  // delimiter   
                                    
                
            if ($showNumbers) {

                $fullString .= "<span>" 
                                            . $dotFret  
                                            . $dotFret 
                                            . $cnt  
                                            . $dotFret
                                    ;

                if (strlen($cnt) != 2) {
                    // Dont use this extra one if there are two dots.
                    $fullString .=  $dotFret;
                }               
                $fullString .= $dotFret
                                    . "</span>";     // delimiter   
                
            } elseif ($showTopbar) {

                $dotFretBottom = '_';

                $tick = "<span>$dotFretBottom</span>";          
                    
                $fullString .=  $dotFretBottom.$dotFretBottom . $tick . $dotFretBottom;
        
                if ( !isset($cntTopbar) || !is_numeric($cntTopbar) ) { $cntTopbar = 0; }
                if ( $cntTopbar > 12 ) $cntTopbar = 1;  
                $cntTopbar ++;  
                if (strlen($cntTopbar) != 2) {
                    // Dont use this extra one if there are two dots.
                    $fullString .=  $dotFretBottom;
                }               
                $fullString .= $dotFretBottom ;  // delimiter   
                
            } elseif ($showDots) {
                
                $fullString = $fullStringDot;
            } else {
            
                $realNoteId = $this->allNotes[$note];
                $flatAlt    = $this->allNotesFlipFlat[$realNoteId];
                $sharpAlt   = $this->allNotesFlipSharp[$realNoteId];
                    
                    
                if ($flatAlt == $sharpAlt)  {
                    $classnameNote = $sharpAlt;
                } else {
                    $classnameNote = $flatAlt.' '.$sharpAlt;
                }           
                
/*
$fretNumber = $noteId - 1;
$this->debug(' = = === == ==rootNote: ' . $rootNote);
$this->debug( 'String Number: ' . $stringIdentifier);   
$this->debug( 'noteId: ' . $noteId);    
$this->debug( 'Fret Number: ' . $fretNumber);
$this->debug( '$note: ' . $note);       
//$this->debug( $onlyTheseNotes);       
    */
                $show = true;
                if( !is_string($onlyTheseNotes) && isset($onlyTheseNotes['onlyThesePositions']) && is_array($onlyTheseNotes['onlyThesePositions']) ) {
                    $show = false;
                    if( isset($onlyTheseNotes['onlyThesePositions'][$stringIdentifier][$noteId - 1]) ) {
                        $show = true;                       
                    }
                } else {                
                    if ( is_array($onlyTheseNotes) && count($onlyTheseNotes) ) {
                        $show = false;
                        if ( isset($onlyTheseNotes[$flatAlt]) || isset($onlyTheseNotes[$sharpAlt]) ) {                  
                            $show = true;
                        }
                    } // if
                }           
                    
                        
                /*
                    Define javascript actions on elements here.
                */
                $noteClickOpen  =  '<span 
                        id="'.$stringIdentifier.'_'.($cnt-1).'_'.$rootNote.'_'.$flatAlt. '_' .  $this->uniqueString. '_' .  $this->uniqueDiv . '"';
                        
                $addToClassName = '';
                if ($show) {
                    $addToClassName = ' visible_note ';
                }                       
                        
                if($cnt == 1) {
                        $noteClickOpen  .= '
                        class="'.$classnameNote.' rootString '.$addToClassName.'" ';
                } else {
                        $noteClickOpen  .= '
                        class="'.$classnameNote.'  '.$addToClassName.'" ';
                }
                $noteClickOpen  .= '
                onclick="noteClick(this, \''.$note.'\');">';        
                
                if($cnt == 1) {
                    $fullString         = '';
                    if ( $show ) {
                                
                        if(isset($onlyTheseNotes['onlyThesePositions'])) {
                            $show = false;
                            if( !is_string($onlyTheseNotes) && isset($onlyTheseNotes['onlyThesePositions'][$stringIdentifier][$noteId - 1]) ) {
                                $fullString .= 
                                    $noteClickOpen  . $note . $lineStart. $noteClickClose;
                            }
                        } else {
                            if (isset($onlyTheseNotes[$flatAlt]) || isset($onlyTheseNotes[$sharpAlt]) ) {
                                $fullString .= 
                                    $noteClickOpen  . $note . $lineStart. $noteClickClose;
                            }                       
                        }                                           
                        
                    } else {
                        
                        $fullString     .= 
                                $noteClickOpen   . $fret.$lineStart. $noteClickClose;
                    }
                    $fullString         .= $delimiter;
                    
                } else {                                    
                    $cellStartString    = $leftActionOpen  . $fret.$fret . $leftActionClose ;
                    $cellEndString  = $rightActionOpen . $fret.$fret . $rightActionClose;
                        
                    $showNoteAsString = $fret.$fret;
                    if ( $show ) {
                        if (isset($onlyTheseNotes[$flatAlt])) {
                            $showNoteAsString = $flatAlt;
                        }   
                        if (isset($onlyTheseNotes[$sharpAlt])) {
                            $showNoteAsString = $sharpAlt;
                        }
                        if (!is_string($onlyTheseNotes) && isset($onlyTheseNotes['onlyThesePositions'][$stringIdentifier][$noteId - 1])) {
                            $showNoteAsString = $sharpAlt;
                        }
                        
                    } else {
                        $showNoteAsString = $fret.$fret;
                    }                                   
                    $strlen = strlen($showNoteAsString);
                    if ( $strlen == 2 ) {
                        $cellEndString  = $rightActionOpen . $fret . $rightActionClose;
                    }
            
                    if($cellStartString == '  ' &&  $showNoteAsString == '  ' && isset($fullStringDot) ) {              
                        $showNoteAsString = '. ';               
                    } 
                                                    
                    $fullString .= 
                                $noteClickOpen  
                                    .  $cellStartString 
                                    .   $showNoteAsString 
                                    .  $cellEndString   
                                    .  $delimiter   
                                . $noteClickClose;
                } //else
            } // else
            $cnt ++;
        } // foreach ( $notesOnString as $noteId => $note ) 
        
        return $fullString;             
    } // function createTabLine
    
       
    
    /*
            Cut one big tab into pieces, and get a fretboard per piece
    */
    public function parseMultiTab( $tabString ) {          
        return $this->parseTab($tabString, true);           
    }   
    
    
    
    public function isValidTabLine($line) {
        
        
        $line = str_replace(' ' , '', $line);
        // Todo: write better function
                

        //  Try to positively detect a tab line...  
        if ( isset($this->allNotes[strtoupper($line[0])])  ) {
            // This line has a possible NOTE name as first character
            if ( $line[1] == '|' || $line[1] == '-' || $line[1] == ':' || ( $line[1] == ' ' && $line[2] == '|'  ) ) {
                return true;
            }
        } elseif( (md5($line[0]) == 'd81fd9b26fd0e89a61b65229aac286e5' || md5($line[0]) == 'e1e1d3d40573127e9ee0480caf1283d6') ) {                      
            /*
            $this->debug(utf8_decode($line[0]) );
            $this->debug(md5($line[0]) );
            $this->debug(md5($line[1]) );
            */
            return true;
        } elseif ( ( $line[0] == '|' && ($line[1] == '-' ) || $line[1] == '-' || $line[count($line)-1] == '|' ) ) {
            return true;
        
        } elseif ($line[0] == '-' &&  $line[3] == '-') {
            return true;
        
        } else {
            
            $lineLength = strlen($line);
            $currentIndex = 0;
            while( $lineLength > $currentIndex ) {

                $requiredForTab[] = '-';                    
                $requiredForTab[] = '|';                
                $requiredForTab[] = 'x';                
                $requiredForTab[] = '=';                
                $requiredForTab[] = '/';                
                
                if ( is_numeric( $line[ $currentIndex ] ) ) {
                    $votesForYes[] =  $line[ $currentIndex ];
                } elseif ( in_array( $line[ $currentIndex ], $requiredForTab ) ) {
                    $votesForYes[] =  $line[ $currentIndex ];
                } else {
                    $votesForNo[] =  $line[ $currentIndex ];                
                }
                    /*
                    $this->debug('$line: ');
                    $this->debug($line);
                    $this->debug('$votesForNo: ');
                    $this->debug($votesForNo);
                    $this->debug('$votesForYes: ');
                    $this->debug($votesForYes);
                            */
                $currentIndex   ++;
            } // while
            
            if( stristr($line, '-') && stristr($line, '|') ) {
                if ( count($votesForYes) > count($votesForNo) ) {
                    return true;
                }
            }
        }       
        return false;
    }
    
    
    public function getOpenNoteFromLine($line, $stringNumReverse = NULL ) {         
        // Gets the NOTE of the fret, if it is the first, or first two characters.
        if( isset($this->allNotes[strtoupper($line[0])]) ) {
            $openNote = strtoupper($line[0]);
                
            // add sharps or flats, if any
            if ( strtolower($line[1]) == 'b' || $line[1] == '#' ) {
                $openNote .= $line[1];
            }
            //$this->debug($openNote);
            return $openNote;           
        }
    }
    

            

    public function getNotesAndFretPostions ( $tabStringIn ) {
        $tabInfo = $this->analyzeTab ( $tabStringIn );
        if(isset($tabInfo['valid_blocks'])) {
            foreach ( $tabInfo['valid_blocks'] as $i => $tabBlock ) {
                foreach ($tabBlock['tab_line_block'] as $numberOfString => $tabline ) {                 
                    $positions[$numberOfString] = $this->extract_numbers($tabline, true);                   
                }   
                return $positions;
            } // foreach
        }   
    }
    
    public function analyzeTab ( $tabStringIn ) {

        $tabStringIn = str_replace("\t", "\n", $tabStringIn );
        $tabStringIn = str_replace("\n\n\n\n\n\n\n\n", "\n", $tabStringIn );
        $tabStringIn = str_replace("\n\n\n\n\n", "\n", $tabStringIn );
        $tabStringIn = str_replace("\n\n\n", "\n", $tabStringIn );
        $tabStringIn = str_replace("\n\n", "\n", $tabStringIn );
        $tabStringIn = strip_tags($tabStringIn);

        // This needs to be done. Don't know why.
        $tabStringIn = "\n" . $tabStringIn . "\n" ; 
    
        $explodeByOptions[] = "\n";
        //$explodeByOptions[] = "<br>";
        //$explodeByOptions[] = "<br />";
        
        $possibleTabLines = array();
        $collectedInfo = array();
        //$collectedInfo['input_string'] = $tabStringIn;    
        
        foreach ( $explodeByOptions as $explodeBy  ) {
            $possibleTabLines += explode($explodeBy, $tabStringIn);
        }

        $validLineMode  = false;
        $validBlocksIndex = 0;
        $validLinesInaRow = 0;
        
        $defaultRootNote[1] = 'E';      
        $defaultRootNote[2] = 'A';      
        $defaultRootNote[3] = 'D';      
        $defaultRootNote[4] = 'G';      
        $defaultRootNote[5] = 'B';      
        $defaultRootNote[6] = 'E';      
        
        foreach ( $possibleTabLines as $possibleTabLine ) {
            
            if ($this->isValidTabLine($possibleTabLine) ) {
                $validLinesInaRow ++;               
                
                $openNoteOnString = $this->getOpenNoteFromLine($possibleTabLine, $validLinesInaRow);                        
                if ( !strlen($openNoteOnString) ) {
                    $openNoteOnString = $defaultRootNote[$validLinesInaRow];
                }   
                
                $openNotesInBlock[]     = $openNoteOnString ? strtoupper($openNoteOnString) : '';               
                $tabLinesInBlock[]  = trim($possibleTabLine); // Show the tabline as it is              
                
                $validLineMode      = true; 
            } else { // NOT isValidTabLine($possibleTabLine
                
                if($validLineMode) {
                    // The previous line was a VALID TAB line, and this one isn't, reset things.                
                    $validLinesInaRow = 0;
                    
                    // we collected an array of previous TAB lines:
                    $collectedInfo['valid_blocks'][$validBlocksIndex]['comments']               = $commentLines;
                    $collectedInfo['valid_blocks'][$validBlocksIndex]['tab_line_block']         = $this->createStringOrder($tabLinesInBlock);
                    $collectedInfo['valid_blocks'][$validBlocksIndex]['number_of_strings']  = count($tabLinesInBlock);
                    $collectedInfo['valid_blocks'][$validBlocksIndex]['open_notes']             = $this->createStringOrder($openNotesInBlock);
                    
                    
                    foreach ( $collectedInfo['valid_blocks'][$validBlocksIndex]['tab_line_block'] as $stringNum => $validLine ) {                                           
                        $collectedInfo['valid_blocks'][$validBlocksIndex]['tab_line_block_interactive'][$stringNum] = $this->getInterActiveTabLine($validLine, $stringNum);
                    }
                    //$collectedInfo['valid_blocks'][$validBlocksIndex]['tab_line_block_interactive'] = $interactiveTabLine;
                    
                    // Reset / increase counters etc.
                    $tabLinesInBlock    = array();
                    $openNotesInBlock = array();
                    $commentLines       = array();
                    $validBlocksIndex ++;                   
                } else {
                    //  Previous line was also invalid, log the lines in between as comments
                    $commentLines[] = $possibleTabLine; 
                }
                        
                $validLineMode = false; 
            }
        //$this->debug($collectedInfo['valid_blocks']);
        }  // foreach


        if($validLineMode) { 
            // The very LAST line was a VALID TAB line, 
            // we collected the last array of TAB lines:
            $collectedInfo['lastcomment']               = $commentLines;
            
            $collectedInfo['valid_blocks'][$validBlocksIndex]['comments']               = $commentLines;
            $collectedInfo['valid_blocks'][$validBlocksIndex]['tab_line_block']         = $this->createStringOrder($tabLinesInBlock);
            $collectedInfo['valid_blocks'][$validBlocksIndex]['number_of_strings']  = count($tabLinesInBlock);
            $collectedInfo['valid_blocks'][$validBlocksIndex]['open_notes']             = $this->createStringOrder($openNotesInBlock);
            

            foreach ( $collectedInfo['valid_blocks'][$validBlocksIndex]['tab_line_block'] as $stringNum => $validLine ) {                                           
                $collectedInfo['valid_blocks'][$validBlocksIndex]['tab_line_block_interactive'][$stringNum] = $this->getInterActiveTabLine($validLine, $stringNum);
            }           
            
            //$this->debug($validBlocksIndex);
            // Reset counters etc.
            $tabLinesInBlock = array();
            $validBlocksIndex = 0;          
        }       


        if(isset($collectedInfo['valid_blocks'])) {
            foreach ( $collectedInfo['valid_blocks'] as $i => $tabBlock ) {                     
                foreach ($tabBlock['tab_line_block'] as $numberOfString => $tabline ) {
                    $collectedInfo['valid_blocks'][$i]['positions'][$numberOfString] = $this->extract_numbers($tabline, true);
                }
            } // foreach
        }
            
            
            
        /*
            Check if the root note are defined
            otherwise use defaults depending on number of strings
        */
        

        foreach ( $collectedInfo['valid_blocks'] as $i => $block  ) {
        

            if ( $block['number_of_strings'] ) {
    
    
                                /*
                                *   Decide the most used notes a add a html container for it
                                */
                                foreach ( $block['positions'] as $lineNumber => $fretPositions ) {
                                
                                    foreach ( $fretPositions as $fretNumber => $numberOfTimesPlayed ) {
                                        
                                    }       
                                }
                
                //$this->debug($block);
                //if ( !strlen( trim( $openNote ) ) ) {
                    foreach($block['open_notes'] as $stringNumber => $openNote ) {
                        //$openNoteOnString = $this->getOpenNoteFromLine($possibleTabLine);
                        //$standarRootNote = $this->getStandardTabRootNote($block['number_of_strings'], $stringNumber);
                        //$this->debug('USe default note '. $standarRootNote .' for string no: ' . $stringNumber . ' on a ' . $block['number_of_strings'] . ' strings');
                        //$this->debug('USe default openNoteOnString '. $openNoteOnString .' for string no: ' . $stringNumber . ' on a ' . $block['number_of_strings'] . ' strings');
                        //$this->debug('USe default openNote '. $openNote .' for string no: ' . $stringNumber . ' on a ' . $block['number_of_strings'] . ' strings');
                        $standarRootNote = $openNote;
                        $collectedInfo['valid_blocks'][$i]['open_notes'][$stringNumber] = $standarRootNote;
                    }
                //}
            }
        }
        //$this->debug($collectedInfo);
        
        return $collectedInfo;  
    }
    
   public function getInterActiveTabLine( $tabline, $stringNum ) {

        $maxStringIndex = strlen($tabline);
        
        $outputLine = '';
        $countStart = 0;
        while( $countStart < $maxStringIndex) {
            
            $fretNumClass = '';
            $fretNumber = -1;
            $doubleFretNumber = '';
            if ( is_numeric($tabline[$countStart]) ) {
                // This is a number of a fret, at first assume it is a single digit.
                $fretNumber = $tabline[$countStart];                
                /*
                 *      Handle double digits but with a certain maximum
                 */
                if ( isset($tabline[$countStart+1]) && is_numeric($tabline[$countStart+1]) ) {
                    // The next character is also a number, assume for now that they belong together
                    $doubleFretNumber = (int)$tabline[$countStart] . $tabline[$countStart+1];
                }
                if ( isset($tabline[$countStart-1]) && is_numeric($tabline[$countStart-1]) ) {
                    // The next character is also a number, assume for now that they belong together
                    $doubleFretNumber = (int)$tabline[$countStart-1] . $tabline[$countStart];
                }               
                if ( isset($doubleFretNumber) && is_numeric($doubleFretNumber) ) {
                    if ( $doubleFretNumber > 9 && $doubleFretNumber < 27 ) {
                        $fretNumber = $doubleFretNumber;
                    }
                }       
                $fretNumClass       = " fretnum_{$fretNumber} ";                
            }
            $fretNumonMouseOver     = " onClick=\"highlightNote( this, {$stringNum}, {$fretNumber} );\" ";
            //$fretNumonMouseOut    = " onMouseOut=\"removeRuler( this );\" ";
            
            $outputLine .= "<span class=\"stringnum_{$stringNum} index_{$countStart} {$fretNumClass}\"  {$fretNumonMouseOut} {$fretNumonMouseOver}>" . $tabline[$countStart] . '</span>';
            $countStart ++;
        }
     /*
        $this->debug("\n\n<br><br>");
        $this->debug('$tabline BEFORE');
        $this->debug($tabline);
        $this->debug('$tabline AFTER');
        $this->debug($outputLine);
    */
        return $outputLine;   
   
   }
    
    
    public function createStringOrder($stringOrderFromTab) {
        //  Set the last element of an array to 1 and count up, to match fretboard string positions.        
        if (is_array($stringOrderFromTab)) {
            $arrayLength = count($stringOrderFromTab);
            foreach ( $stringOrderFromTab as $element ) {
                $newOrder[ $arrayLength ] = $element;
                $arrayLength --;
            }
            //$this->debug($newOrder);
            return $newOrder;
        }
        
    }
    

    public function extract_numbers($string, $uniqueOnly = false) {
       preg_match_all('/([\d]+)/', $string, $match);

        if ($uniqueOnly) {
            if ( is_array($match[0]) && count($match[0]) ) {
                $uniques = array();
                foreach($match[0] as $numberFound) {
                    
                    if ( $numberFound > 26 ) {

                        // This could be a 75757 2323 type of line, assume all are under 10.
                        $digitsFound = str_split($numberFound);
                                                
                        foreach ($digitsFound as $digit) {
                            if( !isset($uniques[$digit]) ) $uniques[$digit] = 0;  
                            $uniques[$digit] ++;    
                        }
                        
                    } else {
                        if( !isset($uniques[$numberFound]) ) $uniques[$numberFound] = 0;  
                        $uniques[$numberFound] ++;                  
                    }
                                            
                }
                return $uniques;
            }   
        }
       return $match[0];
    }

    

    
}
?>