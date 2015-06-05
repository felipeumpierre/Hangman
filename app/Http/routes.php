<?php
/**
 * API Route to Hangman Game
 *
 * @author Felipe Pieretti Umpierre <felipeumpierre[at]hotmail[dot]com>
 */

use Hangman\GameConfigurations;
use Hangman\Word;
use Illuminate\Support\Facades\Input;

/*
 * --------------------------------------------------------------
 * API ROUTE
 * --------------------------------------------------------------
 */
Route::group( [ "prefix" => "hangman" ], function() {

	/*
	 * --------------------------------------------------------------
	 * Get - Overview
	 * --------------------------------------------------------------
	 *
	 * This Route will return all the games saved.
	 *
	 */
    Route::get( "/", function() {

		$configuration = new GameConfigurations();

		/**
		 * Load all the games saved
		 */
		$game = $configuration->load();

		return Response::json( $game );

    } );

	/*
	 * --------------------------------------------------------------
	 * POST - Start new Game
	 * --------------------------------------------------------------
	 *
	 * This Route will generate a new game and save it in session.
	 *
	 */
    Route::post( "/", function() {

		$word = new Word();
		$configuration = new GameConfigurations();

		/**
		 * Create a new Game with the index of the word chosen randomly.
		 */
		$game = $configuration->newGame( $word->getIndex() );

		/**
		 * Set the word in the Game for some basic configurations
		 */
		$game->setWord( $word->getWordByIndex( $game->getId() ) );

		/**
		 * Save the Game
		 */
		$configuration->generateSessionNameWithIndex( $game->getId() )->save( $game );

		return Response::json( $game->apiResponse() );

    } );

	/*
	 * --------------------------------------------------------------
	 * GET - JSON Response
	 * --------------------------------------------------------------
	 *
	 * This Route will return the response for each try. If the player
	 * won or be hanged, the response will alert.
	 *
	 */
    Route::get( "/{id}", function( $id ) {

		$word = new Word();
		$configuration = new GameConfigurations();

		$game = $configuration->generateSessionNameWithIndex( $id )->load();

		/**
		 * Check if the game has been loaded correctly or if the player
		 * is not trying to access this ROUTE before generate a new Game
		 */
		if( is_null( $game ) )
		{
			return Response::json( [ "status" => 400, "error_code" => "HAN01", "message" => "Game not found" ] );
		}

		/**
		 * Set the word for check from the index that came as parameter
		 */
		$game->setWord( $word->getWordByIndex( $game->getId() ) );

		/**
		 * Check if the player has won
		 */
		if( $game->hasWon() )
		{
			return Response::json( [ "status" => 200, "success_code" => "HAN04", "message" => "Player won the game." ] );
		}
		/**
		 * Check if the player was hanged
		 */
		else if( $game->hasHanged() )
		{
			return Response::json( [ "status" => 200, "success_code" => "HAN05", "message" => "Player hanged.", "correct_word" => $game->getWord() ] );
		}

		return Response::json( $game->apiResponse() );

    } );

	/*
	 * --------------------------------------------------------------
	 * POST - Guessing the Word
	 * --------------------------------------------------------------
	 *
	 * This Route will get the $id to find which word the user is
	 * trying to guess.
	 *
	 */
    Route::post( "/{id}", function( $id ) {

		/**
		 * Get the POST parameter - char
		 */
		$op = Input::get( "char" );

		/**
		 * Check if the value that came from POST are not null, empty
		 * or even a string
		 */
		if( is_null( $op ) || empty( $op ) || !is_string( $op ) )
		{
			return Response::json( [ "status" => 400, "error_code" => "HAN06", "message" => "Parameter missing." ] );
		}

		$word = new Word();
		$configuration = new GameConfigurations();

		/**
		 * Load the game saved
		 */
		$game = $configuration->generateSessionNameWithIndex( $id )->load();

		/**
		 * Check if the game has been loaded correctly or if the player
		 * is not trying to access this ROUTE before generate a new Game
		 */
		if( is_null( $game ) )
		{
			return Response::json( [ "status" => 400, "error_code" => "HAN01", "message" => "Game not found" ] );
		}

		/**
		 * Set the word for check from the index that came as parameter
		 */
		$game->setWord( $word->getWordByIndex( $game->getId() ) );

		/**
		 * If the player has tries left, the API will check the letter,
		 * otherwise, the response will be negative.
		 */
		if( $game->getTriesLeft() > 0 )
		{
			/**
			 * Check the letter that the player guessed
			 */
			$checkLetterReturn = $game->checkLetter( $op );

			if( !is_bool( $checkLetterReturn ) )
			{
				return Response::json( [ "status" => 400, "error_code" => "HAN08", "message" => $checkLetterReturn ] );
			}

			/**
			 * Save the new result in session
			 */
			$configuration->save( $game );
		}
		else
		{
			return Response::json( [ "status" => 400, "error_code" => "HAN03", "message" => "Exceeded attempts" ] );
		}

		return Response::json( [ "status" => 200 ] );

    } );

	/*
	 * --------------------------------------------------------------
	 * GET - Reset Game
	 * --------------------------------------------------------------
	 *
	 * This Route will reset a saved Game to the default values.
	 *
	 */
	Route::get( "/reset/{id}", function( $id ) {

		$configuration = new GameConfigurations();

		/**
		 * Load the game saved
		 */
		$game = $configuration->generateSessionNameWithIndex( $id )->load();

		/**
		 * Check if the game has been loaded correctly or if the player
		 * is not trying to access this ROUTE before generate a new Game
		 */
		if( is_null( $game ) )
		{
			return Response::json( [ "status" => 400, "error_code" => "HAN01", "message" => "Game not found" ] );
		}

		/**
		 * Reset the Game to the default values
		 */
		$configuration->reset();

		return Response::json( [ "status" => 200, "success_code" => "HAN07", "message" => "Game was reset." ] );

	} );

	/*
	 * --------------------------------------------------------------
	 * GET - Delete Game saved
	 * --------------------------------------------------------------
	 *
	 * This Route will delete a saved Game.
	 *
	 */
	Route::get( "/delete/{id}", function( $id ) {

		$configuration = new GameConfigurations();

		/**
		 * Load the game saved
		 */
		$game = $configuration->generateSessionNameWithIndex( $id )->load();

		/**
		 * Check if the game has been loaded correctly or if the player
		 * is not trying to access this ROUTE before generate a new Game
		 */
		if( is_null( $game ) )
		{
			return Response::json( [ "status" => 400, "error_code" => "HAN01", "message" => "Game not found" ] );
		}

		/**
		 * Reset the Game to the default values
		 */
		$configuration->delete();

		return Response::json( [ "status" => 200, "success_code" => "HAN07", "message" => "Game was reset." ] );

	} );

	Route::delete( "/delete", function() {

		$configuration = new GameConfigurations();
		$configuration->deleteAll();

		return Response::json( [ "status" => 200, "success_code" => "HAN09", "message" => "All Games saved were deleted." ] );

	} );

} );