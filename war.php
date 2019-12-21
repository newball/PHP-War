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

/*
 * This is the Deck, it takes in an array of cards
 * It also shuffles the deck once created, and can split the deck in half
 */
class Deck {
    
    public $deck;
    public $deck1;
    public $deck2;
    
    public function __construct($deck) {
        $this->deck = $deck;
        $this->shuffleDeck();
    }
    
    private function shuffleDeck() {
        shuffle ($this->deck);    
    }
    
    public function splitDeck() {
        $this->deck1 = array_slice($this->deck, 0,26);
        $this->deck2 = array_slice($this->deck, 26,26);
    }
}

/*
 * This is the Game, it handles all of the aspects of the game
 */
class Game {
    
    private $player1;
    private $player2;
    public $score;
    
    public function __construct($deck) {
        $this->player1 = $deck->deck1;
        $this->player2 = $deck->deck2;
        $this->score = new \stdClass(); // So that I don't have to deal with the PHP warning
        // Create the two properties of the player 1 and the player 2 scores
        $this->score->player1 = 0;
        $this->score->player2 = 0;
        $this->score->ties = 0;
    }
    
    public function playGame() {
        for ($i = 0; $i <= 25; $i++) {
            if ($this->player1[$i]->value > $this->player2[$i]->value) {
                $this->score->player1++;
                $this->output($this->player1[$i], $this->player2[$i], 'Player 1', $this->score);
            } elseif ($this->player1[$i]->value < $this->player2[$i]->value) {
                $this->score->player2++;
                $this->output($this->player1[$i], $this->player2[$i], 'Player 2', $this->score);
            } elseif ($this->player1[$i]->value == $this->player2[$i]->value) {
                $this->score->ties++;
                $this->output($this->player1[$i], $this->player2[$i], 'Tie', $this->score);
            }
        }
        
        if ($this->score->player1 > $this->score->player2) {
            echo '<p><strong>Player 1 Wins!</strong></p>';
        } elseif ($this->score->player1 < $this->score->player2) {
            echo '<p><strong>Player 2 Wins!</strong></p>';
        } elseif ($this->score->player1 == $this->score->player2) {
            echo "<p><strong>It's a tie! No one wins, except for the love in your heart.</strong></p>";
        }

    }
    
    private function output($card1, $card2, $result, $score) {
        $output = 'Player 1 has <strong>' . $card1->value . ' of ' . $card1->suit . '</strong>. ';
        $output .= 'Player 2 has <strong>' . $card2->value . ' of ' . $card2->suit . '</strong>. ';
        $output .= '<em>';
        if ($result == 'Player 1') {
            $output .= ' Player 1 wins this round!';
        } elseif ($result == 'Player 2') {
            $output .= ' Player 2 wins this round!';
        } elseif ($result == 'Tie') {
            $output .= " There's a tie! Nobody Wins";
        }
        $output .= '</em>';
        $output .= '<br>';
        $output .= '<strong>';
        $output .= 'Score-- ';
        $output .= 'Player 1: ' . $score->player1;
        $output .= ' / ';
        $output .= 'Player 2: ' . $score->player2;
        $output .= ' / ';        
        $output .= 'Ties: ' . $score->ties;
        $output .= '!</strong>';
        $output .= '<br>';
        
        echo $output;
        
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
// Create the Deck for war
$warDeck = new Deck($deck);
// Split the deck amongst each player
$warDeck->splitDeck();
// Stars the game
$game = new Game($warDeck);

$game->playGame();

//new Output ($game);

// Utility Function

class Output {
    
    public function __construct($var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}

?>