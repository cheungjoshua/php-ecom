<?php


require 'includes/functions.php';

if($_GET['from'] == 'login')
{    
    $email    = trim(filterInput($_POST['email']));
    $password = trim(filterInput($_POST['password']));

    if(findUser($email, $password)){
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = getUserName($email, $password);
        header('Location: index.php?username='.$_SESSION['username']);
        exit();
    }
    else
    {
        header('Location: index.php?loggin=error');
        exit();
    }
} 
elseif($_GET['from'] == 'signup') 
{
    if(checkSignUp($_POST) && saveUser($_POST))
    {
        session_start();
        $firstname = trim(filterInput($_POST['f_name']));
        $lastname  = trim(filterInput($_POST['l_name']));
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = ucwords($firstname.' '.$lastname);
        header('Location: index.php?username='.$_SESSION['username']);
        exit();
    }
    else 
    {
        header('Location: index.php?signup=error');
        exit();
    }
}

