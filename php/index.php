<?php


require_once "lib/db.php";
require_once "lib/functions.php";

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$GLOBALS['input'] = json_decode(file_get_contents('php://input'), true);
session_start();

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
        $pid = FromTokenToPid();
        $result = CanIPlay($pid);
        if ($result == 0){
        
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Not your turn."]);
            
        } else {
            if ($method == 'POST') {
                $result = PlayCards($pid);
                if ($result == 1) {
                    print json_encode(['errormesg' => "Cards not in player's hand"]);
                    exit;
                }
            } else {    
                header("HTTP/1.1 400 Bad Request");
                print json_encode(['errormesg' => "Method $method not allowed here."]);
            }
        }    
    break;

    case 'Objection':
        if ($method == 'POST') {

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
            echo "\n You didnt object!";
        }

        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;
    ////////////////////////////////ONLY FOR TESTING///////////////////////////////////////////////
    case 'Vroom':
        if ($method == 'POST') {BroomTheBoard();}
    break;

    case 'Prepare':
        if ($method == 'POST') {PrepareNewGame();} 
    break;

    case 'Winner':
        if ($method == 'GET') {
            $result = CheckForWinner();

            switch ($result) {
                case 1:
                    print json_encode(['winner' => '1']);
                    break;
                case 2:
                    print json_encode(['winner' => '2']);
                    break;
                case 3:
                    print json_encode(['winner' => '3']);
                    break;
                case 4:
                    print json_encode(['winner' => '4']);
                    break;
                default:
                    print json_encode(['winner' => '0/kanis']);
            }
        }
    break;

    case 'PlayedLast':
        if ($method == 'GET') {

            $result = CheckWhoPlayedLast();
            //echo($result);

            switch ($result) {
                case 1:
                    print json_encode(['last' => '1']);
                    break;
                case 2:
                    print json_encode(['last' => '2']);
                    break;
                case 3:
                    print json_encode(['last' => '3']);
                    break;
                case 4:
                    print json_encode(['last' => '4']);
                    break;
            }
        }    
    break;
    ////////////////////////////////////////////////////////////////////////////////////
    default:
        header("HTTP/1.1 404 Not Found");
        print json_encode(['errormesg' => "Not found."]);
        break;
}
?>