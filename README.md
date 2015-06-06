# Hangman Game

Hangman is an `API` developed in Laravel 5.

## Installation

Clone this repo

```git clone https://github.com/gremio10/Hangman```

Install composer dependency

```composer install```

## Optimize

To optimize laravel, run ```php artisan optimize``` on the root of the project

## Usage

To get a overview of all Games

```GET - localhost/hangman/hangman```

To create a new Game

```POST - localhost/hangman/hangman```

To guess a letter

```POST - localhost/hangman/hangman/{id}```

To check status of the Game, such as found letters, tries left, tried letters and status

```GET - localhost/hangman/hangman/{id}```

To reset a Game

```GET - localhost/hangman/hangman/reset/{id}```

To delete a Game

```GET - localhost/hangman/hangman/delete/{id}```

To delete all Games saved

```DELETE - localhost/hangman/hangman/delete```