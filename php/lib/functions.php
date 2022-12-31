<?php

function FromTokenToPid(){
    $session_id = session_id();
    //$session_id = '';
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
    // print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
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


function iObject($pid){
    global $mysqli;
    $sql =  'call Objection(?)';
    $st = $mysqli->prepare($sql);
    $st->bind_param('i', $pid);
    $st->execute();
    $result = $st->get_result()->fetch_assoc();
    $result = $result['@Won'];

    $st->close();
    return $result;
}



////////////////// These functions aren't used for requests //////////////////

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


?>