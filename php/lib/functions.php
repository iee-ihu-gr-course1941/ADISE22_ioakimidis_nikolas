<?php

function FromTokenToPid(){
    $session_id = session_id();
    //$session_id = '1';
    //header("HTTP/1.1 301 dada");
    global $mysqli;
    $sql = "call TokenToPid(?)";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $session_id);
    $st->execute();
    $res = $st->get_result();
    if (mysqli_num_rows($res) == 0) {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
    $result = $res->fetch_assoc();
    $pid = $result['Pid'];
    $st->close();
    return $pid;
}

function AddPlayerIntoGame(){
    $session_id = session_id();
    global $mysqli;
    $st = $mysqli->prepare("call AddPlayer (?, ?, @output)");
    $st->bind_param('ss', $GLOBALS['input']['name'], $session_id);
    $st->execute();
    $st->bind_result($result);
    $st->fetch();
    
    $st->close();
    return ['result' => $result];
}

function ShowPlayerHand($pid){
    global $mysqli;
    $sql = "call showhand(?)";
    $st = $mysqli->prepare($sql);
    $st->bind_param('i', $pid);
    $st->execute();
    $res = $st->get_result();
    $result = json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);

    $st->close();
    return $result;
}

function PlayCards($pid){
    global $mysqli;
    $sql =  'call Play(?,?,?,?,?,?)';
    $st = $mysqli->prepare($sql);
    $st->bind_param('iiiiis', $pid, $GLOBALS['input']['c1'], $GLOBALS['input']['c2'], $GLOBALS['input']['c3'], $GLOBALS['input']['c4'], $GLOBALS['input']['s']);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();
    $result = $result['@NotInHand'];

    $st->close();
    return $result;
}

function PlayCardsInSameRound($pid, $s){
    global $mysqli;
    $sql =  'call Play(?,?,?,?,?,?)';
    $st = $mysqli->prepare($sql);
    $st->bind_param('iiiiis', $pid, $GLOBALS['input']['c1'], $GLOBALS['input']['c2'], $GLOBALS['input']['c3'], $GLOBALS['input']['c4'], $s);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();
    $result = $result['@NotInHand'];

    $st->close();
    return $result;
}

function iObject($pid){
    global $mysqli;
    $sql =  'call Objection(?,?)';
    $st = $mysqli->prepare($sql);
    $st->bind_param('ii', $pid, $GLOBALS['input']['DoIt']);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();
    $result = $result['@Won'];

    $st->close();
    return $result;
}

////////////////// These are system functions and aren't used for requests //////////////////

function PrepareNewGame(){
    global $mysqli;
    $sql = "call PrepareGame()";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $st->close();

}

function BroomTheBoard (){
    global $mysqli;
    $sql = "call broom()";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $st->close();
}

function CheckForWinner() {
    global $mysqli;
    $sql = "CALL FindWinner()";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();
    $result = $result['@Winner'];

    $st->close();
    return $result;
}

function CheckWhoPlayedLast() {
    global $mysqli;
    $sql = "CALL WhoPlayedLast()";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();
    $result = $result['@Lastplayer'];

    $st->close();
    return $result;
}

function FindPLayOrder($pid){
    global $mysqli;
    $sql =  'call CalculatePlayOrder(?)';
    $st = $mysqli->prepare($sql);
    $st->bind_param('i', $pid);
    $st->execute();
    $st->close();
}

function CanIPlay($pid){
    global $mysqli;
    $sql =  'call AllowedToplay(?)';
    $st = $mysqli->prepare($sql);
    $st->bind_param('i', $pid);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();
    $result = $result['@Allowed'];

    $st->close();
    return $result;
}

function ShowSay(){
    global $mysqli;
    $sql = "CALL ShowSaid()";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();
    $result = $result['@Said'];

    $st->close();
    return $result;
}

function GetUsernames(){
    global $mysqli;
    $sql = "SELECT Username FROM players";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $res = $st->get_result();
    header('Content-type: application/json');
    print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);

    $st->close();
}

function GetGameStatus(){
    global $mysqli;
    $sql = "SELECT g.Status FROM gamedetails g";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $res = $st->get_result();
    header('Content-type: application/json');
    print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);

    $st->close();
}

function ShowNextPlay(){
    global $mysqli;
    $sql = "SELECT pid FROM gamestate WHERE PlayOrder = 1";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();

    $st->close();
    return $result['pid'];
}

function EveryonePassed(){
    global $mysqli;
    $sql = "SELECT count(*) FROM gamestate WHERE Passed = 1";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();

    $st->close();
    return $result['count(*)'];
}

function HowManyObjected(){
    global $mysqli;
    $sql = "SELECT count(*) FROM gamestate WHERE Objected = 1";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();

    $st->close();
    return $result['count(*)'];
}

function SetObjected($pid, $x){
    global $mysqli;
    $sql = "UPDATE gamestate SET Objected = $x WHERE pid = $pid";
    $st = $mysqli->prepare($sql);
    $st->execute();

    $st->close();
}

function HaveIObjected($pid){
    global $mysqli;
    $sql = "SELECT Objected FROM gamestate WHERE pid = $pid";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();

    $st->close();
    return $result['Objected'];
}

function LockMasterPlay($x){
    global $mysqli;
    $sql = "UPDATE gamedetails SET MasterLock = $x";
    $st = $mysqli->prepare($sql);
    $st->execute();

    $st->close();
}

function IsMasterlocked(){
    global $mysqli;
    $sql = "SELECT MasterLock FROM gamedetails";
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();

    $st->close();
    return $result['MasterLock'];
}

function MakeNewRound(){
    LockMasterPlay(0);
    SetObjected(1, 1); SetObjected(2, 1); SetObjected(3, 1); SetObjected(4, 1);
    BroomTheBoard();
    $result = CheckForWinner();

    return $result;
}
?>