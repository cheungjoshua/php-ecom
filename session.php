<?php 
// need to add check session username and logged in prevent
session_start();

require 'includes/functions.php';

if($_GET['from'] == 'pin')
{
    if($_SESSION['loggedin'])
    {
        if(pinItem($_GET['id'], $_SESSION))
        {
        header('Location: index.php');
        exit();
        }
    }
    else
    {
        header('Location: index.php?pin=fail');
        exit();
    }
}
elseif($_GET['from'] == 'unpin')
{
    if($_SESSION['loggedin'])
    {
        if(unpinItem($_GET['id'], $_SESSION))
        {
        header('Location: index.php');
        exit();
        }
    }
    else
    {
        header('Location: index.php?unpin=fail');
        exit();
    }
}

if($_GET['from'] == 'delete')
{
    if($_SESSION['loggedin'])
    {
        if(deleteItem($_GET['id'], $_SESSION))
        {
        header('Location: index.php');
        exit();
        }
    }
    else
    {
        header('Location: index.php?delete=fail');
        exit();
    }
}

if($_GET['from'] == 'downvote')
{
    if($_SESSION['loggedin'])
    {   
        $userId = getuserId($_SESSION);
        
        if(downvote($_GET['id'], $userId))
        header('Location: index.php');
        exit();
    }
    else
    {
        header('Location:index.php?downvote=fail');
        exit();
    }
}