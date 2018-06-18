<?php

// TODO
// what happens when two users take the same username

require("../common.php");

if (isset($_SESSION['userID'])) {
  header("Location: http://f.kth.se/axeltimer/");
} elseif (!isset($_SESSION['kthID'])) {
  header("Location: http://f.kth.se/axeltimer/login/");
}

if (isset($_POST)) {
  if (isset($_POST['nickname'])) {
    $userDB->addUser($_SESSION['kthID'], $_POST['nickname']);
    // $_SESSION['userID'] = $userDB->getUserID($_SESSION['kthID']);
    header("Location: http://f.kth.se/axeltimer/");
  }
}





?>

<!DOCTYPE html>

<html>

<body>
<form action="set-nick.php" method="post">
Input your desired nickname:
<input type="text" name="nickname">
<br />
<input type="submit" name="submit" value="Submit">
</body>
</html>
