
# ADISE22_ioakimidis_nikolas

## API URL
[http://users.iee.ihu.gr/~it185351/php/index.php](http://users.iee.ihu.gr/~it185351/php/index.php)

## API discription

Used to join the game.
```
POST /AddPlayer 
body json
{
    "name":"example name"
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
Παίζεις σύμφωνα με τον χαρακτήρα του προηγούμενου.
```
POST /PlaySameRound
{
    "c1": 29,
    "c2": 16,
    "c3": 3,
    "c4": 42
}
Το ίδιο με το play αλλά χωρίς το s.
```
Επιστρέφει τι είπε ο προηγούμενος παίκτης.
```
GET /HeSaidWhat
{"mesg": Κ}
```

Επιστρέφει ποίος παίκτης είναι επόμενος να παίξει.
```
GET /WhoPlaysNext
```

Επιστρέφει τα usernames των παικτών του παιχνίδιού.
```
GET /ShowUsernames

[
    {
        "Username": "test1"
    },
    {
        "Username": "test1"
    },
    {
        "Username": "test1"
    }
]

```
Επιστρέφει το status του παιχνιδιού.
```
GET /ShowGamesStatus

[
    {
        "Status": "Waiting"
    }
]

```

Εμφανίζει τα φύλλα που έχεις στο χέρί.
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
},...
]

```
