<?php
namespace Hangman;

/**
 *
 *
 * @package App
 */
class Game
{
    const BUSY = 0;
    const FAIL = 1;
    const SUCCESS = 2;
    const ATTEMPTS = 11;

    protected $word;
    protected $triesLeft = ATTEMPTS;
    protected $triedLetters = [];
    protected $foundLetters = [];
    protected $status = [ "busy", "fail", "success" ];

    public function __construct( $word, $triesLeft = 0, array $triedLeft = [], array $foundLetters = [] )
    {
        $this->word = $word;
        $this->triesLeft = $triesLeft;
        $this->triedLetters = $triedLeft;
        $this->foundLetters = $foundLetters;
    }

    public function getCurrent()
    {
        return [
            "word" => $this->word,
            "tries_left" => $this->triesLeft,
            "tried_letters" => $this->triedLetters,
            "found_letters" => $this->foundLetters
        ];
    }

    public function checkLetter( $letter )
    {
        if( 0 == preg_match( "/^[a-z]$/", $letter ) )
        {

        }

        if( in_array( $letter, $this->triedLetters ) )
        {
            $this->triesLeft--;

            return false;
        }

        if( strpos( $this->word, $letter ) )
        {
            $this->foundLetters[] = $letter;
            $this->triedLetters[] = $letter;

            $this->triesLeft--;
        }

        $this->triedLetters[] = $letter;
        $this->triesLeft--;

        return false;
    }

    public function getWord()
    {
        return $this->word;
    }

    public function getTriesLeft()
    {
        return $this->triesLeft;
    }

    public function getTriedLetters()
    {
        return $this->triedLetters;
    }

    public function getFoundLetters()
    {
        return $this->foundLetters;
    }
}