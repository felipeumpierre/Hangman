<?php namespace Hangman;

class Word
{
	private $file = "words/list.json";
	private $index;
	private $content;

	/**
	 *
	 */
	public function __construct()
	{
		$json = file_get_contents( $this->file );
		$this->content = json_decode( $json );
	}

	/**
	 * @return mixed
	 */
	public function getRandomWord()
	{
		$this->index = array_rand( $this->content, 1 );

		return $this->content[ $this->index ];
	}

	/**
	 * Search and return
	 *
	 * @param $index
	 * @return string|bool
	 */
	public function getWordByIndex( $index )
	{
		$index--;

		if( isset( $this->content[ $index ] ) )
		{
			return $this->content[ $index ];
		}

		return false;
	}

	/**
	 * Get the index from the JSON array and add +1 before
	 * the return to use as an index, like a database and to
	 * more friendly to a GET request
	 *
	 * @return int
	 */
	public function getIndex()
	{
		$this->getRandomWord();

		return ++$this->index;
	}
}