<?php
/**
 * API Route to Hangman Game
 *
 * @author Felipe Pieretti Umpierre <felipeumpierre[at]hotmail[dot]com>
 */

use Hangman\GameConfirgurations;
use Hangman\Word;

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
	 */
    Route::get( "/", function() {

		\Illuminate\Support\Facades\Session::flush();

		$configuration = new GameConfirgurations();
		$game = $configuration->load();

		return Response::json( $game );

    } );

	/*
	 * --------------------------------------------------------------
	 * POST - Start new Game
	 * --------------------------------------------------------------
	 *
	 * This Route will generate a new game and save it in session
	 *
	 */
    Route::post( "/", function() {

		$word = new Word();
		$configuration = new GameConfirgurations();

		$game = $configuration->newGame( $word->getIndex() );
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
		$configuration = new GameConfirgurations();

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
		$op = \Illuminate\Support\Facades\Input::get( "char" );

		/**
		 * Check if the value that came from POST are not null, empty
		 * or even a string
		 */
		if( is_null( $op ) || empty( $op ) || !is_string( $op ) )
		{
			return Response::json( [ "status" => 400, "error_code" => "HAN06", "message" => "Parameter missing." ] );
		}

		$word = new Word();
		$configuration = new GameConfirgurations();

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
		$configuration->save( $game );

		dd( $game->getWordLetters() );

		/**
		 * If the player has tries left, the API will check the letter,
		 * otherwise, the response will be negative.
		 */
		if( $game->getTriesLeft() > 0 )
		{
			/**
			 * Check the letter that the player guessed
			 */
			$game->checkLetter( $op );

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

} );