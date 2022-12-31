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
            //echo($pid);
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
                  print json_encode(['success'=>"Players added"]);
                }
            
        } else {
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;

    case 'Play':
        if ($method == 'POST') {

            $pid = FromTokenToPid();
           // echo $pid;
            
            $result = PlayCards($pid);
            //echo $result;   

            if ($result == 1) {
                print json_encode(['errormesg' => "Cards not in player's hand"]);
                exit;
            }
            
        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;

    case 'Objection':
        if ($method == 'POST') {

        $pid = FromTokenToPid();    
        $result = iObject($pid);
            
        if ($result == 1) {
            print json_encode(['bluff' => 'TRUE']);
            echo 'He gets all the cards back :)';
        } else {
            print json_encode(['bluff' => 'FALSE']);
            echo 'You get all the cards :(';
        }

        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;

////////////////////////////////ONLY FOR TESTING///////////////////////////////////////////////
    case 'Vroom':
        if ($method == 'POST') {

        BroomTheBoard();
            
        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;

    case 'Prepare':
        if ($method == 'POST') {

        PrepareNewGame();
            
        } else {    
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg' => "Method $method not allowed here."]);
        }
    break;

    case 'Winner':
        if ($method == 'GET') {

        $result = CheckForWinner();
        echo($result);

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
                print json_encode(['winner' => '0']);
        }

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