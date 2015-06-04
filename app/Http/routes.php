<?php

Route::group( [ "prefix" => "games" ], function() {

    Route::get( "/", function() {
        return Response::json( [ "teste" ] );
    } );

    /*
     * Start new game
     */
    Route::post( "/", function() {
        $game = new Game();
    } );

    Route::get( "/{id}", function( $id ) {
        dd( $id );
    } );

    Route::post( "/{id}", function( $id ) {
        
    } );

} );