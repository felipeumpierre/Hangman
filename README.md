# Hangman Game

Hangman is an `API` developed in Laravel 5.

## Installation

Clone this repo
```
git clone https://github.com/gremio10/Hangman
```

Install composer dependency
```
composer install
```

## Optimize

To optimize laravel, run ```php artisan optimize``` on the root of the project

## Usage

To get a overview of all Games
```
GET - localhost/hangman/games
```

To create a new Game
```
POST - localhost/hangman/games
```

To guess a letter
```
POST - localhost/hangman/games/{id}
```

To check status of the Game, such as found letters, tries left, tried letters and status
```
GET - localhost/hangman/games/{id}
```

To reset a Game
```
GET - localhost/hangman/games/reset/{id}
```

To delete a Game
```
GET - localhost/hangman/games/delete/{id}
```

To delete all Games saved
```
GET - localhost/hangman/games/delete/all
```