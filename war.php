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
    public $suits = ['Diamonds','Spades','Clubs','Hearts'];

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
    public $score; // This is going to keep track of the score. See note below on thought process
    private $current_card; // This is going to hold the current card for each player.
    private $card_pool = array(); // These are the current cards in the card pool.
    
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
        
        // Sets up the current card properties
        $this->current_card = new \stdClass();
        $this->current_card->p1 = null;
        $this->current_card->p2 = null;        
    }
    
    // This handles playing of the game
    public function playGame()
    {
        
        while (count($this->player1) != 0 && count($this->player2) != 0) {
            // Pull the top card
            $this->topCard();
            
            // COmpare the results of the top cards
            $result = $this->compareCards();
            
            if ($result == 'Tie') {
                echo '<pre>';
                echo 'Tie';
                echo '</pre>';
                echo '<pre>';
                print_r($this->card_pool);
                echo '</pre>';

                $this->iDeclareWar();
            } else {
                echo '<pre>';
                print_r($this->card_pool);
                echo '</pre>';
                $this->distributeCards($result);
            }
            
            
                echo '<pre>Player1';
                var_dump($this->player1);
                echo '</pre>';

                echo '<pre>Player2';
                var_dump($this->player2);
                echo '</pre>';
        }
            
    }
    
    /*
     * This is going to pull the current top card
     */
     
    private function topCard()
    {
        $this->current_card->p1 = array_shift($this->player1);
        $this->current_card->p2 = array_shift($this->player2);
        
        array_push($this->card_pool, $this->current_card->p1, $this->current_card->p2);
                
    }
    
    /*
     * For when there is a tie and war needs to be declared
     */
    private function iDeclareWar() {
        for ($i = 0; $i < 3; $i++) {
            array_push($this->card_pool, array_shift($this->player1));
            array_push($this->card_pool, array_shift($this->player2));
        }
    }
        
    /*
     * Here we compare the cards against each other. Using this as a comparison engine.
     */
    private function compareCards()
    {   
        // Pull in the object and grab the values
        $card1 = $this->current_card->p1->value;
        $card2 = $this->current_card->p2->value;
        if ($card1 == $card2) {
            return "Tie";
        } elseif ($card1 == 1 || $card1 > $card2) {
            return "Player 1";
        } elseif ($card1 == 1 || $card1 < $card2) {
            return "Player 2";
        }
    }
    
    private function distributeCards($player) {
        for ($i = 0; $i < count($this->card_pool); $i++) {
            if ($player == 'Player 1') {
                array_push($this->player1, $this->card_pool[$i]);
            } elseif ($player == 'Player 2') {
                array_push($this->player1, $this->card_pool[$i]);
            }
        }
        
        // Empty the card array
        $this->card_pool = array();
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