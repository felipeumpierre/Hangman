<?php namespace Hangman;

class GameConfigurationsArray
{
	private $games;
	private $id;

	public function __construct()
	{
		$this->games = [];
	}

	public function load()
	{
		$this->validateId();

		if( isset( $this->games[ $this->id ] ) )
		{
			return $this->games[ $this->id ];
		}

		return null;
	}

	public function save( Game $game )
	{
		$this->validateId();

		$this->game[ $this->id ] = $game->getCurrent();
	}

	public function reset()
	{
		$this->validateId();

		$game = new Game( $this->id );
		$this->game[ $this->id ] = $game->getCurrent();
	}

	public function delete()
	{
		$this->validateId();

		unset( $this->game[ $this->id ] );
	}

	public function deleteAll()
	{
		unset( $this->game );
	}

	public function setId( $id )
	{
		$this->id = $id;

		return $this;
	}

	private function validateId()
	{
		if( empty( $this->id ) )
		{
			throw new \Exception( "You must inform the id" );
		}
	}
}