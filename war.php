<?php

/*
 * PHP Version of the card game War
 * Creator: Leo Newball
 * Description: This is a version of the card game War, where a deck is shuffled and 
 * split between two players. Then, the top card of each deck battles against each others
 * where the higher number beats out the lower number.
 * 
 */

/* Crates the card object with two properties: suit, value
 * Usage:
 * Card (string suit, number value)
 */
 
/* Global Variables 
 *
 */
 
// The suits of the playing cards 
$suits = ['Diamonds','Kings','Spades','Clubs'];
// Deck array, to place all of the cards in later
$deck = array();

class Card {
    
    public $suit;
    public $value;
    
    public function __construct($suit, $value) {
        $this->suit = $suit;
        $this->value = $value;
    }
}

// Generator function to create cards, stars with number 1
function cardGenerator($numberOfCards) {
    for ($i = 1; $i <= $numberOfCards; $i++) {
        yield $i;
    }
}

// Look to iterate through the suits and card generation
foreach($suits as $suit) {
    foreach(cardGenerator(13) as $i ) {
        $deck[] = new Card($suit, $i);
    }
}


// Utility Function

class Output {
    
    public function __construct($var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}

?>