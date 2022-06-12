<?php 
session_start();

// var_dump($_FILES);
// var_dump($_SESSION);


require 'includes/functions.php';

$username = $_SESSION['username'];
$userId   = getuserId($_SESSION);

    if(!checkPost($_FILES))
    {
        $message = 'fail';
        header("Location: index.php?newpost=$message");
        exit();
    }
    else
    {
        savePost($_POST, $_FILES, $username, $userId);
        $message = 'success';
        header("Location: index.php?newpost=$message");
        exit();
    }



