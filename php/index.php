<?php


require_once "lib/db.php";
require_once "lib/functions.php";

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$GLOBALS['input'] = json_decode(file_get_contents('php://input'), true);
session_start();

/////
//print HowManyObjected();
//print ShowSay();
/////

//$result = MakeNewRound();
// if ($result != 0) {
//     print json_encode(['winner' => $result]);
//     exit;
// } 

if (CanIPlay(FromTokenToPid()) == 1) {
    print ("It's your turn to play cards!!!\n");
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
                echo('Enter username');
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
                    //json_encode(['Said' => "ShowSay()"]);
                } else {
                    print json_encode(['errormesg' => "You can not throw a new Figure."]);
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
                        }
                        for ($i = 1; $i <= 4; $i++) {
                            if ($i != $pid) {
                                SetObjected($i, 0);
                            }
                        }
                        FindPLayOrder($pid+1);
                        //json_encode(['Said' => "ShowSay()"]);
                    }
                } else {
                    print json_encode(['errormesg' => "You should play with a new Figure :)"]);
                    exit;  
                }     
            } else {
                print json_encode(['errormesg' => "It's not your turn to play."]);
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
                print json_encode(['errormesg' => "You have already objected!"]);
            }
        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }   
    break;

    case 'HeSaidWhat':
        if ($method == 'GET') {
            print ShowSay();
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

    ////////////////////////////////////////////////////////////////////////////////////
    default:
        header("HTTP/1.1 404 Not Found");
        print json_encode(['errormesg' => "Not found."]);
        break;
}
?>