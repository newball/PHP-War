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

class Card
{    
    public $suit;
    public $value;
    
    public function __construct($suit, $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }
}

/*
 * This is the Deck. It creates a deck of 52 cards by suit.
 * It performs several important functions.
 * 1) Deck->shuffleDeck() shuffles the deck.
 * 2) Deck->splitDeck() splits the deck in half.
 * It also shuffles the deck once created, and can split the deck in half
 */
class Deck
{
    
    public $cards = array(); // Set up as an empty array, to hold any number of cards we wish to hold
    public $deck; // the whole deck
    public $deck1; // player 1's deck
    public $deck2; // player 2's deck
    public $suits = ['Diamonds','Kings','Spades','Clubs'];

    public function __construct()
    {
        foreach($this->suits as $suit) {
            foreach($this->cardGenerator(13) as $i ) {
                $this->cards[] = new Card($suit, $i);
            }
        }
            
        $this->deck = $this->cards;
    }
    
    // Shuffles the array of cards
    public function shuffleDeck()
    {
        shuffle ($this->deck);    
    }
    
    /* Splits the deck in half into two separate arrays
     * Note: perhaps in a future version, doing something more complex such as alternating every other element to the other player
     * However splitting the array in half felt just as safe, being that the deck was shuffled earlier
    */
    public function splitDeck()
    {
        $this->deck1 = array_slice($this->deck, 0,26);
        $this->deck2 = array_slice($this->deck, 26,26);
    }

    // Generator function to create cards, stars with number 1
    private function cardGenerator($numberOfCards)
    {
        for ($i = 1; $i <= $numberOfCards; $i++) {
            yield $i;
        }
    }
}

/*
 * This is the Game, it handles all of the aspects of the game
 * Game->playGame() plays the game
 * Game->output() handles all of the reporting for the game
 */
class Game
{
    private $player1; // This is going to hold players 1's deck
    private $player2; // This is going to hold players 2's deck
    public $score; // This is going to keep track of the score. See note below on thought proccess
    
    public function __construct($deck)
    {
        $this->player1 = $deck->deck1;
        $this->player2 = $deck->deck2;
        
        /*
         * Note: I considered placing the score in the player1/player2 property, however I wanted to keep reporting
         * on the ties that occurred (so that I could also ensure that I did the math properly
         */
        $this->score = new \stdClass(); // So that I don't have to deal with the PHP warning
        // Create the two properties of the player 1, player 2 scores and the amount of ties
        $this->score->player1 = 0;
        $this->score->player2 = 0;
        $this->score->ties = 0;
    }
    
    // This handles playing of the game
    public function playGame()
    {
        
        /* Iterate through the array of cards, compare them, if the value is higher for a player, add to their score and then
         * send the necessary information to output so that we can display the results
         */
         
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
        
        /* We are simply reporting the final results here.
         * This could be thrown into a separate method, but that felt excessive
        */
        if ($this->score->player1 == $this->score->player2) {
            echo "<p><strong>It's a tie! No one wins, except for the love in your heart.</strong></p>";
        } elseif ($this->score->player1 > $this->score->player2) {
            echo '<p><strong>Player 1 Wins!</strong></p>';
        } else {
            echo '<p><strong>Player 2 Wins!</strong></p>';
        }
    }
    
    /* This handles outputting of the statements. For the purpose of keeping things simple,
     * I created one long string statement and added all of the tags, etc through them
     */
    private function output($card1, $card2, $result, $score)
    {
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

// Create the Deck for war
$warDeck = new Deck();

// Shuffles the deck
$warDeck->shuffleDeck();

// Split the deck amongst each player
$warDeck->splitDeck();

// Starts the game
$game = new Game($warDeck);

// Plays the game
$game->playGame();