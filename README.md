
# ADISE22_ioakimidis_nikolas

## API URL
[http://users.iee.ihu.gr/~it185351/php/index.php](http://users.iee.ihu.gr/~it185351/php/index.php)

## API discription 

Used to join the game.
```
POST /AddPlayer 
body json
{
    "name":"Mpampis"
}
Player inputs his desired username to enter the match. Max 4 players, each with a unique token.
```

Used to play with a new "figure" of cards.
```
POST /Play 
body json
{
    "c1": 29,
    "c2": null,
    "c3": 3,
    "c4": 38,
    "s": "3"
}
The player chooses 1-4 cards by inputting their Cid and the figure he wants to say. S and at least one C fields is mandatory.
```
Used to object or pass.
```
POST /Objection  
body json
{
        "DoIt": 1
}
Player chooses if he wants to pass or object, by inputting 0 or 1. He is forced to take this action every time someone plays cards..
```
Used to play cards in the same round with the same Cid.
```
POST /PlaySameRound
{
    "c1": 29,
    "c2": 16,
    "c3": 3,
    "c4": 42
}
The player is forced to use the "Figure" the previous player said. In case he only give nulls, it means he passed his turn.
```
Returns what the previous player said he played.
```
GET /HeSaidWhat
{"mesh" :"Q"}
```

Returns the player who's next to play.
```
GET /WhoPlaysNext

```

Returns the usernames of the players.
```
GET /ShowUsernames

[
    {
        "Username": "Mpampis"
    },
    {
        "Username": "Takis"
    },
    {
        "Username": "Makis"
    }
]

```
Returns the current status of the game.
```
GET /ShowGamesStatus

[
    {
        "Status": "Waiting"
    }
]

```

Returns the cards in the player's hand.
```
GET /GetPlayersHand

[
{
"Cid": 5,
"Figure": "5",
"Class": "heart"
},
{
"Cid": 17,
"Figure": "4",
"Class": "diamond"
},
{
"Cid": 19,
"Figure": "6",
"Class": "diamond"
},
{
"Cid": 34,
"Figure": "8",
"Class": "club"
},
...
...
...
]

```
