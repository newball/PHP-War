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
 * Game->playGame() - starts playing the game
 * Game->topCard() - handles pulling the top card from the deck
 * Game->iDeclareWar() - handles pulling 3 cards in the case of a tie i.e. I Declare War!
 * Game->compareCards() - handles the comparison of each card
 * Game->distributeCards() - distributes cards to the winner
 * Game->cardValue() - takes a card and returns the actual value (Ace, Jack, Queen, King)
 * Game->output() - handles all of the reporting for the game
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
            
        // Sets up the current card properties
        $this->current_card = new \stdClass();
        $this->current_card->p1 = null;
        $this->current_card->p2 = null;        
    }
    
    // This handles playing of the game
    public function playGame()
    {
        // Play the game, while each deck has cards 
        while (count($this->player1) != 0 && count($this->player2) != 0) {
            // Pull the top card
            $this->topCard();
            
            // Compare the results of the top cards
            $result = $this->compareCards();
            
            // Check for a tie first, if there is a tie, add 3 cards to the card pool
            // Otherwise, distribute the cards to the winner.
            if ($result == 'Tie') {
                $this->iDeclareWar();
            } else {
                $this->distributeCards($result);
            }
            
            // Display the results
            $this->output($this->current_card->p1, $this->current_card->p2, $result);
        }
    }
    
    /*
     * This is going to pull the current top card
     */
    private function topCard()
    {    
        /* 
         * Check each deck size to see if there's more than 1 card left. If there is, pull the top card
         * from each deck. If there isn't, use the remaining card as the matching card.
         * This idea comes from a variation of the rules set of War, see: https://www.pagat.com/war/war.html#two
         */
        if (count($this->player1) >= 1 && count($this->player2) >= 1 ) {
            // Assign each card to each player
            $this->current_card->p1 = array_shift($this->player1);
            $this->current_card->p2 = array_shift($this->player2);
            // Add each card to the card pool
            array_push($this->card_pool, $this->current_card->p1, $this->current_card->p2);
        } elseif (count($this->player1) == 1) {
            // Assign the next top card for player 2 as their current card
            $this->current_card->p2 = array_shift($this->player2);
            // Add that card to the pool of cards
            array_push($this->card_pool, $this->current_card->p2);
        } elseif (count($this->player2) == 1) {
            // Assign the next top card for player 1 as their current card
            $this->current_card->p1 = array_shift($this->player1);
            // Add that card to the pool of cards
            array_push($this->card_pool, $this->current_card->p1);
        }
    }
    
    /*
     * For when there is a tie and war needs to be declared
     * Usage: $this->iDeclareWar()
     */
    private function iDeclareWar()
    {    
        // Grab the next three cards and push them into the card pool
        for ($i = 0; $i < 3; $i++) {
            
            /* Check to see if there are 4 cards left in either deck the reason why the number 4
             * is to account for the 3 cards that would be pulled and then the last card to be displayed
             * face up.
             */
            if (count($this->player1) >= 4) {
                array_push($this->card_pool, array_shift($this->player1));
            }

            if (count($this->player2) >= 4) {
                array_push($this->card_pool, array_shift($this->player2));
            }
        }
    }
        
    /*
     * Here we compare the cards against each other. Using this as a comparison engine.
     * Usage: $this->compareCards()
     * Returns: string (Tie | Player 1 | Player 2)
     */
    private function compareCards()
    {   
        // Pull in the object and grab the values
        $card1 = $this->current_card->p1->value;
        $card2 = $this->current_card->p2->value;
        
        // Checks for a tie first, if not a tie then check to see if the card is an Ace.
        // If the card is an Ace, or that player has a higher value that player wins,
        if ($card1 == $card2) {
            return "Tie";
        } elseif ($card1 == 1 || $card1 > $card2) {
            return "Player 1";
        } elseif ($card2 == 1 || $card1 < $card2) {
            return "Player 2";
        }
    }
    /*
     * Distribute the cards to the winner.
     * Usage: $this->distributeCards(string 'Player 1' | 'Player 2')
     */
    private function distributeCards($player) 
    {
        for ($i = 0; $i < count($this->card_pool); $i++) {
            if ($player == 'Player 1') {
                array_push($this->player1, $this->card_pool[$i]);
            } elseif ($player == 'Player 2') {
                array_push($this->player2, $this->card_pool[$i]);
            }
        }
        
        // Empties the card pool array
        $this->card_pool = array();
    }

    /*
     * This assigns a value string to the value of cards that aren't 2 - 10.
     * If value is 1, 11 - 13 it will return a string of the corresponding card.
     * Otherwise it simply returns the value
     * Usage: $this->cardValue(integer $card_value);
     * Returns: int || string (Ace | Jack | Queen | King)
     */
    private function cardValue($card_value)
    {    
        switch ($card_value) {
            case 1:
                return 'Ace';
                break;
            case 11:
                return 'Jack';
                break;
            case 12:
                return 'Queen';
                break;
            case 13:
                return 'King';
                break;
            default:
                return $card_value;
        }
    }

    /* This handles outputting of the statements. For the purpose of keeping things simple,
     * I created one long string statement and added all of the tags, etc through them
     * Usage: $this->output(object card, object card, string result (Player 1 | Player 2 | Tie)
     */
    private function output($card1, $card2, $result)
    {
        $output = '<p>';
        $output .= 'Player 1 has <strong>' . $this->cardValue($card1->value) . ' of ' . $card1->suit . '</strong>. ';
        $output .= '<br>';
        $output .= 'Player 2 has <strong>' . $this->cardValue($card2->value) . ' of ' . $card2->suit . '</strong>. ';
        $output .= '<br>';
        $output .= '<em>';
        if ($result == 'Player 1') {
            $output .= ' Player 1 wins this round!';
        } elseif ($result == 'Player 2') {
            $output .= ' Player 2 wins this round!';
        } elseif ($result == 'Tie') {
            $output .= " There's a tie! I! De-Clare! War!";
        }
        $output .= '</em>';
        $output .= '</p>';
        $output .= '<strong>';
        $output .= 'Player 1 has ' . count($this->player1) . ' cards';
        $output .= ' / ';
        $output .= 'Player 2 has ' . count($this->player2) . ' cards';
        $output .= '</strong>';
        
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