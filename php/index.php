<?php


require_once "lib/db.php";
require_once "lib/functions.php";

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$GLOBALS['input'] = json_decode(file_get_contents('php://input'), true);
session_start();

$result = CheckForWinner();
if ($result != 0) {
    print json_encode(['winner' => $result]);
    PrepareNewGame();
exit;
} 
switch ($request[0]) {

    case 'GetPlayersHand':
        if ($method == 'GET') {

            $pid = FromTokenToPid();
            print ShowPlayerHand($pid);

        } else {
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;

    case 'AddPlayer':
        if ($method == 'POST') {
            
            if (!isset($GLOBALS['input']['name'])){
                header("HTTP/1.1 400 Bad Request");
                print('Enter username');
                exit();
            }
            
            $result = AddPlayerIntoGame();

            switch ($result['result']) {
                case 1:
                print json_encode(['errormesg' => "Max players"]);
                exit;
                case 2:
                print json_encode(['errormesg' => "Username exists"]);
                exit;
                case 3:
                print json_encode(['errormesg' => "Player already exists"]);
                exit;
                default:
                print json_encode(['success'=>"Player added"]);
                }
            
        } else {
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;

    case 'Play':
        if ($method == 'POST') {

            $pid = FromTokenToPid();
            $result = CanIPlay($pid);

            if ($result == 0) {
                print json_encode(['errormesg' => "Not your turn."]);
            } else {

                if (!isset($GLOBALS['input']['c1']) AND !isset($GLOBALS['input']['c2']) AND !isset($GLOBALS['input']['c3']) AND !isset($GLOBALS['input']['c4'])) {
                    print json_encode(['errormesg' => "You need to play something."]);
                    exit();
                }

                $result = IsMasterlocked();
                if ($result == 0) {
                    $result = PlayCards($pid);
                    if ($result == 1) {
                        print json_encode(['errormesg' => "Cards not in player's hand"]);
                        exit;
                    } 
                    for ($i = 1; $i <= 4; $i++) {
                        if ($i != $pid) {
                            SetObjected($i, 0);
                        }
                    }
                    FindPLayOrder($pid+1);
                    LockMasterPlay(1);
                } else {
                    print json_encode(['errormesg' => "You can't throw a new Figure."]);
                }
            }
        }else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }   
    break;

    case 'PlaySameRound':
        if ($method == 'POST') {

            $pid = FromTokenToPid();
            $result = CanIPlay($pid);
            if ($result == 1){

                $result = IsMasterlocked();
                if ($result == 1) { 

                    $result = HowManyObjected();
                    if  ($result < 4) {
                        print json_encode(['errormesg' => "Wait for the other playes to decide if they are objecting."]);
                    } else {
                        $s = ShowSay();
                        $pid = FromTokenToPid();
                        $result =  PlayCardsInSameRound($pid, $s);
                        if ($result == 1) {
                            print json_encode(['errormesg' => "Cards not in player's hand"]);
                            exit;
                        } else if ($result == 2){
                            for ($i = 1; $i <= 4; $i++) {
                                if ($i != $pid) {
                                    SetObjected($i, 1);
                                }
                            }
                            FindPLayOrder($pid+1);
                            print ('You skipped your turn!');
                            $result = EveryonePassed();
                            if ($result == 4){
                                $result = MakeNewRound();
                            }
                            exit;
                        }
                        for ($i = 1; $i <= 4; $i++) {
                            if ($i != $pid) {
                                SetObjected($i, 0);
                            }
                        }
                        FindPLayOrder($pid+1);
                    }
                } else {
                    print json_encode(['errormesg' => "You should play with a new Figure."]);
                    exit;  
                }     
            } else {
                print json_encode(['errormesg' => "Not your turn."]);
                exit; 
            }        
        }else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]); 
        }   
    break;

    case 'Objection':
        if ($method == 'POST') {
            
            $pid = FromTokenToPid();
            $result = HaveIObjected($pid);
            if ($result == 0){

                $pid = FromTokenToPid();    
                $result = iObject($pid);
                if ($result == 1) {
                    print json_encode(['bluff' => 'TRUE']);
                    echo "\n He gets all the cards back :)";
                } else if ($result == 0){
                    print json_encode(['bluff' => 'FALSE']);
                    echo "\n You get all the cards :(";
                }
                else{
                    print json_encode(['bluff' => NULL]);
                }     
            } else {
                print json_encode(['errormesg' => "You can't object now."]);
            }
        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }   
    break;

    case 'HeSaidWhat':
        if ($method == 'GET') {
            print json_encode(['mesg' => ShowSay()]);
        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;

    case 'ShowUsernames':
        if ($method == 'GET') {
            GetUsernames();
        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;
    
    case 'ShowGamesStatus':
        if ($method == 'GET') {
            GetGameStatus();
            print json_encode();
        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;
    
    ////////////////////////////////ONLY FOR TESTING///////////////////////////////////////////////

    case 'WhoPlaysNext': //maybe should be available to others
        if ($method == 'GET') {

            $result = ShowNextPlay();
            print $result;

        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;


    $result = EveryonePassed();
    if ($result == 4){
        echo ('$result MESA');
        $result = MakeNewRound();
        if ($result != 0) {
            print json_encode(['winner' => $result]);
        exit;
        } 
    }

    ////////////////////////////////////////////////////////////////////////////////////
    default:
        header("HTTP/1.1 404 Not Found");
        print json_encode(['errormesg' => "Not found."]);
        break;        
}
?>