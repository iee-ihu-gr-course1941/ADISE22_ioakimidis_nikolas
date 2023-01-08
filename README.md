
# ADISE22_ioakimidis_nikolas

## API URL
[http://users.iee.ihu.gr/~it185351/php/index.php](http://users.iee.ihu.gr/~it185351/php/index.php)



## How to install
It's only an API so there isn't really an installation but pick the version of "adise basi.txt" you want (preferably the last one) and copy paste it on your database. Then take the files inside the php folder and edit the db.php with your database's credentials.

## How to play
First the user must add his username and join the game using the AddPlayer request.
After all 4 players join, the first player will use the play request and input the Cid of the cards he wants to play along with the Figure of his choosing.
After that's done the rest of the players decide if they want to object/call his bluff, or not.
If they decide they want to raise an objection they will use the Object request and input the value 1 into the DoIt field, anything else is considered a pass. Should be noted that objection uses a first in first served, meaning whoever gets to request first will also have his request executed before the other's. However all the players need to object (either with DoIt 1 or 0)
After the objections are over, the new player will play his cards, either with a new Figure or the same, depending on the outcome. This will repeat until we have a winner.

## How the code works
The game only starts when all 4 players join. The gamedetails.status becomes "waiting" if there are 1-3 players and "playing" if all 4 are present.
The play order at the start is decided by who joins first. The first player can make a "Master play", meaning he can say any figure he wants when he plays, however this action cannot be passed. 
Whenever someone makes a Master play the following things happen:



<ol type="A">
  <li>The gamedetails.masterlock becomes 0 so the next player has to use the PlaySameRound.</li>
  <li>His gamestate.objected becames 1 so he can't say bluff on himself. For the 3 others however gamestate.objected becomes 0 so they can object if they want.</li>
  <li> All the gamestate.passed become 0, since some player's plan might have changed.</li>
  <li>The new play order is calculated.</li>
  <li>His gamestate.bluff changes according to his play/say.</li>
</ol>

Afterwards all of the other 3 players have to choose if they are going to object or not. If they don't, a message appears. If they do object, the master lock becomes 0 and the winner gets to play a new Figure. Meanwhile the loser takes all the cards from the board.

If nobody objects, the next player gets his turn. He can either pass or play his cards but has to say the same Figure the "master player" said.
If he passes, all the gamestate.objected become 1 so nobody can object him and the next player gets to play(with the same Figure). All the gamestate.passed also becomes 0 for the same reason above.

This will go on until either someone objects and gamedetails.masterlock gets unlocked or if all 4 players pass their turn.
If they all pass their turn the following things will happen:

<ol type="A">
    <li>Calculate the new play order.</li>
    <li>The gamedetails.masterlock gets unlocked.</li>
    <li>Everyone's passed and objected are reset to 0.</li>
    <li>The gamestate.objected of the next to play player becomes 1.</li>
    <li>The board is "broomed", meaning all the cards with cards.pid 0 now get the value NULL.</li>
</ol>

This pattern will go on until one player gets rid of all the cards in his hand(win). When that happens the game is finished and the winner is shown on the screen. The gamedetails table also gets updated: The status of the game becomes finished and the winning player's ID goes into Result as P1, P2 etc.
At any time in the game, the players can request to see the cards in their hand, what the previous player said he played, the current status of the game, whose turn is to play and the usernames of the current players.

## API description.

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
...,
...,
...
]

```

| Syntax | Description |
| ----------- | ----------- |
| Header | Title |
| Paragraph | Text |
