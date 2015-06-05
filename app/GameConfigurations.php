<?php namespace Hangman;

use Session;

/**
 * Class GameConfigurations
 *
 * @package Hangman
 */
class GameConfigurations
{
	private $sessionName;
	private $id;

	/**
	 * Start the session name
	 */
	public function __construct()
	{
		$this->sessionName = "hangman";
	}

	/**
	 * Create a new Game
	 *
	 * @param int $id
	 * @return Game
	 */
	public function newGame( $id )
	{
		return new Game( $id );
	}

	/**
	 * Check if exists a Game saved then load it, otherwise, return null
	 *
	 * @return array|Game|null
	 */
	public function load()
	{
		if( Session::has( $this->sessionName ) )
		{
			$session = Session::get( $this->sessionName );

			/**
			 * If isset, means that is one object, otherwise, are multiple objects
			 *
			 * ----
			 * Could not think in a better verification for this :/
			 */
			if( !isset( $session[ "id" ] ) )
			{
				$game = array();

				foreach( $session as $key => $val )
				{
					$game[] = new Game( $val[ "id" ], $val[ "tries_left" ], $val[ "tried_letters" ], $val[ "found_letters" ], $val[ "found_string" ], $val[ "status" ], $val[ "found_letters_helper" ] );
				}

				return $game;
			}
			else
			{
				return new Game( $session[ "id" ], $session[ "tries_left" ], $session[ "tried_letters" ], $session[ "found_letters" ], $session[ "found_string" ], $session[ "status" ], $session[ "found_letters_helper" ] );
			}
		}

		return null;
	}

	/**
	 * Save the Game in session
	 *
	 * @param Game $game - Game object
	 */
	public function save( Game $game )
	{
		Session::put( $this->sessionName, $game->getCurrent() );
	}

	/**
	 * Reset this session Game
	 */
	public function reset()
	{
		$this->delete();

		$game = new Game( $this->id );

		Session::put( $this->sessionName, $game->getCurrent() );
	}

	/**
	 * Delete this session Game
	 */
	public function delete()
	{
		Session::forget( $this->sessionName );
	}

	/**
	 * Delete all Games saved
	 */
	public function deleteAll()
	{
		Session::flush();
	}

	/**
	 * Generate the session name to add and array of session
	 * to multiple games
	 *
	 * @param $id
	 * @return $this
	 */
	public function generateSessionNameWithIndex( $id )
	{
		$this->id = $id;
		$this->sessionName = sprintf( "%s.%d", $this->sessionName, $this->id );

		return $this;
	}
}