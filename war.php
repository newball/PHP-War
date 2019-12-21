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

class Card {
    
    public $suit;
    public $value;
    
    public function __construct($suit, $value) {
        $this->suit = $suit;
        $this->value = $value;
    }
}



?>