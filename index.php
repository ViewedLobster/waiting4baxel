<?php
error_reporting(0);
// echo "some debug info: <br />";

require('common.php');
$_SESSION['startTime'] = NULL;
// We now access the database with the $userDB variable (UserDB class)

function isActive($sessionRowIn){
    if ($sessionRowIn['startTime'] < Time() - 7200) {
        return False;
    } elseif ($sessionRowIn['startTime'] < Time()) {
        return True;
    } else {
        return False;
    }
}

if (!isset($_SESSION['userID'])) {
  header("Location: http://f.kth.se/axeltimer/login");
}

$userID = $_SESSION['userID'];
$sessionRow = $userDB->getActiveSession($userID);
$totalTime = $userDB->getTotalTimeByUserID($userID);

// print_r($_SESSION);

// echo "UserID = {$userID}, ";
// TODO
//
// If $_POST not empty then handle accordingly
//
// If the user exists, then move on
// Else add user to user database
//
//
// If the user has active session, then set the $_SESSION variable accordingly
// Else let be
//
// Fix the dynamic html output
//
// add isActive function
//
// Check if you need to shut down connection to database
//
$setStart = False;
$setStop = False;

if(isset($_POST['stopwatchToggle'])){
    if($_POST['stopwatchToggle'] == "Starta väntan"){
        $showStopwatch = True;

        // echo "Tryckt på start, ";
        // Checks for session data, if active stores start time, else deletes
        if ($sessionRow) {
            if (isActive($sessionRow)) {
                // echo "Hämtad: Aktiv session!, ";
                $_SESSION['startTime'] = $sessionRow['startTime'];
            } else {
                $userDB->deleteSession($sessionRow['id']);
                $_SESSION['startTime'] = Time();
                $userDB->startSession($userID, $_SESSION['startTime']);
            }
        } else {
            $_SESSION['startTime'] = Time();
            $userDB->startSession($userID, $_SESSION['startTime']);
        }
    } elseif ($_POST['stopwatchToggle'] == "Sluta vänta") {
        $showStopwatch = False;
        // echo "Tryckt på stop, ";
        if ($sessionRow) {
            if (isActive($sessionRow)) {
                // echo "hämtat aktiv session, ";
                // echo $sessionRow['id'];
                $sessionRow['stopTime'] = Time();
                $userDB->addToTotalTime($sessionRow['startTime'],
                         $sessionRow['stopTime'], $userID);
                $userDB->finishSession($sessionRow['stopTime'], $sessionRow['id']);
                $_SESSION['startTime'] = NULL;
            } else {
                $userDB->deleteSession($sessionRow['id']);
                $_SESSION['startTime'] = NULL;
            }
        }
    }
} else {
    // echo "Ingen knapp tryckt på, ";
    $noPOST = True;
    if ($sessionRow) {
        if (isActive($sessionRow)) {
            // echo "Hämtat aktv session, ";
            $_SESSION['startTime'] = $sessionRow['startTime'];
        } else {
            $userDB->deleteSession($sessionRow['id']);
            $_SESSION['startTime'] = NULL;
        }
    }
}

$table = $userDB->getTop10();
$currentTime = Time();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>Väntar ru eller?</title>

    <!-- Bootstrap -->
    <link href="bsdist/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'> 
    <link rel="stylesheet" href="timer.css" type="text/css" media="all" />
  </head>
  <body>
    <div class="container">

      <div class="row">
        <div class="col-md-8 centered-col">
          <h1>Vänta på Axel</h1>
          
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 centered-col">
        <p id="alert">Inloggad som: <?php echo $_SESSION['nickname'] ?>. Du har totalt väntat i <?php echo $totalTime; ?> <a href="http://f.kth.se/axeltimer/logout/">Logga ut</a></p>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8 centered-col">
          <h1 id="timer">0 minuter : 0 sekunder</h1>
<?php
if ($_SESSION['startTime'] != NULL) {
    echo("<script>
            document.addEventListener('DOMContentLoaded', function(){ start({$_SESSION['startTime']},{$currentTime});});

        </script>
        ");
}
?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 centered-col">
          <center>
            <form action="index.php" method="post">
<?php
if ($_SESSION['startTime'] == NULL) {
    echo("<input type=\"submit\" name=\"stopwatchToggle\" value=\"Starta väntan\">");
} else {
    echo("<input type=\"submit\" name=\"stopwatchToggle\" value=\"Sluta vänta\">");
}
?>

            </form>
          <!--button id="toggleTimer" class="btn btn-success" type="button">Starta/stoppa timer</button -->
          </center>
        </div>
      </div>

      <div class="row sub-button">
        <div class="container">
        <table id="top10" class="table">
          <thead>
            <tr>
              <th>Rank:</th>
              <th>Användare:</th>
              <th>Total tid:</th>
            </tr>
          </thead>
          <tbody id="top10body">
            <?php echo $table; ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bsdist/js/bootstrap.min.js"></script>
  </body>
<script type="text/javascript">
var startTimeMillis;
var timeDiffMillis;

function start(startTimeSeconds, currentServerTimeSeconds){
    var date = new Date();
    currentLocalTimeMillis = date.getTime();
    timeDiffMillis = currentLocalTimeMillis - currentServerTimeSeconds*1000;
    startTimeMillis = startTimeSeconds*1000;
    countup();
}

function countup(){
    var date = new Date();
    document.getElementById("timer").innerHTML = convertToTime(date.getTime() - startTimeMillis - timeDiffMillis);
    setTimeout(countup, 1000);
}

/*
var w;

function startStopwatch(){
    if(!typeof(Worker) !== "undefined") {
        if(typeof(w) === "undefined") {
            w = new Worker("stopwatch.js")
        }
        document.getElementById("status").innerHTML = "Du väntar på Axel."
            w.onmessage = function(event) {
                a     document.getElementById("countup").innerHTML = convertToTime(event.data);
            };
    } else {
        document.getElementById("countup").innerHTML = "Error: no support for web workers";
    }

}
 
function stopStopwatch(){
    w.terminate();
    w = undefined;
    document.getElementById("status").innerHTML = "Du väntar inte på Axel."
        document.getElementById("countup").innerHTML = "00:00";
}

    function readCookie(){
  var cookieList = document.cookie.split(";");
  var obj = {};
  for ($i = 0; $i < cookieList.length(); $i++) {

  }
}
 */

function convertToTime(milliseconds){
    var minutes = Math.floor(milliseconds/60000);
    var seconds = Math.floor((milliseconds % 60000)/1000);
    return minutes.toString() + " minuter : " + seconds.toString() + " sekunder";
}

</script>

</html>


