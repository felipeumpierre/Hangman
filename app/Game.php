<?php namespace Hangman;

class Game
{
	const TRIES = 11;
	const BUSY = 0;
	const FAIL = 1;
	const SUCCESS = 2;

	public $id;
    private $word;
    public $triesLeft;
	public $triedLetters = [];
	public $foundLetters = [];
	private $foundLettersHelper = [];
	public $foundString;
	public $status;
	private $statusInformation = [ "busy", "fail", "success" ];

	/**
	 * Start the Game values
	 *
	 * @param int $id - id from the word
	 * @param int $triesLeft - tries that the player has yet
	 * @param array $triedLeft - array of words that the player has inserted
	 * @param array $foundLetters - array of words that the player has guessed
	 * @param string $foundString - string with the format of dots for the words not guessed yet
	 * @param int $status - status of the game
	 * @param array $foundLettersHelper - helper for the foundLetters
	 */
    public function __construct( $id, $triesLeft = self::TRIES, array $triedLeft = [], array $foundLetters = [], $foundString = "", $status = self::BUSY, $foundLettersHelper = [] )
    {
		$this->id = $id;
        $this->triesLeft = $triesLeft;
        $this->triedLetters = $triedLeft;
        $this->foundLetters = $foundLetters;
		$this->foundString = $foundString;
		$this->status = $status;
		$this->foundLettersHelper = $foundLettersHelper;

		$this->generateWithDotsWord();
    }

	/**
	 * Return the current status of the values in Class
	 *
	 * @return array
	 */
    public function getCurrent()
    {
        return [
            "id" => $this->id,
            "tries_left" => $this->triesLeft,
            "tried_letters" => $this->triedLetters,
            "found_letters" => $this->foundLetters,
			"found_string" => $this->restoreFoundedToString(),
			"status" => $this->status,
			"found_letters_helper" => $this->foundLettersHelper,
        ];
    }

	/**
	 * Return the API response for the Route
	 *
	 * @return array
	 */
	public function apiResponse()
	{
		return [
			"id" => $this->id,
			"tries_left" => $this->triesLeft,
			"tried_letters" => $this->triedLetters,
			"found_letters" => $this->restoreFoundedToString(),
			"status" => $this->statusInformation[ $this->status ],
		];
	}

	/**
	 * Check if the letter that came from POST matches with the word
	 * in Game
	 *
	 * @param string $letter - letter inserted by the player
	 * @return bool|string
	 */
    public function checkLetter( $letter )
    {
		/*
		 * Force the letter to lowercase
		 */
		$letter = strtolower( $letter );

		/*
		 * Validation, if the value is not a string
		 * of a-z, false is returned
		 */
        if( 0 == preg_match( "/^[a-z]$/", $letter ) )
        {
			return sprintf( "The values accepted are [a-z] - `%s` given.", $letter );
        }

		if( $this->hasWon() || $this->hasHanged() )
		{
			return false;
		}

		/*
		 * If the letter guessed exists in the word,
		 * it will generate the found and found string
		 * values for the Class object
		 */
        if( false !== strpos( $this->word, $letter ) )
        {
			$this->generateFoundLetter( $letter );

			return true;
        }

		$this->triedLetter( $letter );

		/*
		 * Remove one try from the player
		 */
		$this->triesLeft--;

        return false;
    }

	/**
	 * Set the word to handle in this object
	 *
	 * @param $word
	 */
	public function setWord( $word )
	{
		$this->word = $word;

		$this->generateWithDotsWord();
	}

	/**
	 * Return the id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Return the word
	 *
	 * @return string|null
	 */
    public function getWord()
    {
        return $this->word;
    }

	/**
	 * Return the total of tries left
	 *
	 * @return int
	 */
    public function getTriesLeft()
    {
        return $this->triesLeft;
    }

	/**
	 * Return the list of tried letters
	 *
	 * @return array
	 */
    public function getTriedLetters()
    {
        return $this->triedLetters;
    }

	/**
	 * Return the list of found letters
	 *
	 * @return array
	 */
    public function getFoundLetters()
    {
        return $this->foundLetters;
    }

	/**
	 * Return the list of each letter from the word
	 *
	 * @return array
	 */
	public function getWordLetters()
	{
		return str_split( $this->word );
	}

	public function setStatus( $status )
	{
		$this->status = $status;
	}

	/**
	 * Check if the player has won
	 *
	 * @return bool
	 */
	public function hasWon()
	{
		if( 0 === count( array_diff( $this->getWordLetters(), $this->foundLetters ) ) )
		{
			$this->setStatus( self::SUCCESS );

			return true;
		}

		return false;
	}

	/**
	 * Check if the player was hanged
	 *
	 * @return bool
	 */
	public function hasHanged()
	{
		if( $this->triesLeft == 0 )
		{
			$this->setStatus( self::FAIL );

			return true;
		}

		return false;
	}

	/**
	 * Check if the letter guessed is not already in the tried list
	 *
	 * @param string $letter
	 * @return bool
	 */
	private function triedLetter( $letter )
	{
		if( !in_array( $letter, $this->triedLetters ) )
		{
			$this->triedLetters[] = $letter;
		}
	}

	/**
	 * Add to the list of found letter in the correct id
	 *
	 * @param string $letter
	 */
	private function generateFoundLetter( $letter )
	{
		if( !in_array( $letter, $this->triedLetters ) )
		{
			foreach( $this->getWordLetters() as $key => $val )
			{
				if( ( $this->getWordLetters()[ $key ] == $letter ) )
				{
					$this->foundLetters[ $key ] = $this->foundLettersHelper[ $key ] = $letter;
				}
			}

			$this->generateWithDotsWord();
		}

		/**
		 * Check if the player has won or was hanged
		 */
		$this->hasWon();
		$this->hasHanged();

		$this->triedLetter( $letter );
	}

	/**
	 * Generate the dots for the others ides from the list
	 */
	private function generateWithDotsWord()
	{
		foreach( $this->getWordLetters() as $key => $val )
		{
			if( !isset( $this->foundLettersHelper[ $key ] ) )
			{
				$this->foundLettersHelper[ $key ] = ".";
			}
		}
	}

	/**
	 * Restore the list of found letters to string
	 *
	 * @return string
	 */
	private function restoreFoundedToString()
	{
		ksort( $this->foundLettersHelper );

		return $this->foundString = implode( "", $this->foundLettersHelper );
	}
}