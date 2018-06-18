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

unset($_SESSION['kthID']);
unset($_SESSION['userID']);
unset($_SESSION['nickname']);
phpCAS::logout();

?>
