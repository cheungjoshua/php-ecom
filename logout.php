<?php

session_start();

$_SESSION = [];
setcookie('error_message', null, time() - 3600);
session_destroy();
header('Location: index.php');
exit();
