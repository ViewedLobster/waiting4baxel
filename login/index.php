<?php

require("../common.php");

//
// phpCAS simple client
//

// import phpCAS lib
include_once('/home/fsekt/web/CAS/CAS.php');

// initialize phpCAS
phpCAS::client(CAS_VERSION_2_0,'login.kth.se',443,'');

// Unless you properly set upp SSL-verification
phpCAS::setNoCasServerValidation();


// If you want the redirect back from the login server to enter your application by some 
// specfic URL rather than just back to the current request URI, call setFixedCallbackURL.
// phpCAS::setFixedCallbackURL('http://myserver/my_entry_point.php');

// force CAS authentication
phpCAS::forceAuthentication();

$_SESSION['kthID'] = phpCAS::getUser();

$userRow = $userDB->getUser($_SESSION['kthID']);
if ($userRow == NULL) {
  header("Location: http://f.kth.se/axeltimer/login/set-nick.php");
} else {
  $_SESSION['userID'] = $userRow['id'];
  $_SESSION['nickname'] = $userRow['nickname'];
  header("Location: http://f.kth.se/axeltimer/");
}
print_r($_SESSION);

if (isset($_GET['logout'])) {
  unset($_SESSION);
  unset($_GET['logout']);
  phpCAS::logout();
}
/*
if ($userDB->getUserID($_SESSION['kthID']) == NULL) {
  header("Location: http://f.kth.se/axeltimer/login/set-nick.php");
} else {
  $_SESSION['userID'] = $userDB->getUserID($_SESSION['kthID']);
//  $_SESSION['nickname'] = $userDB->getUserNick($_SESSION['userID']);
  header("Location: http://f.kth.se/axeltimer/");
}
 */

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().

// logout if desired
/*
 if (isset($_REQUEST['logout'])) {
 phpCAS::logout();
}
 */

// for this test, simply print that the authentication was successfull
?>
<html>
<head>
 <title>phpCAS simple client</title>
</head>
<body>
 <h1>Successfull Authentication!</h1>
 <p>the user's login is <b><?php echo phpCAS::getUser(); ?></b>.</p>
 <p>phpCAS version is <b><?php echo phpCAS::getVersion(); ?></b>.</p>
 <p><a href="?logout=">Logout</a></p>
</body>
</html>
