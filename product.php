<?php
session_start();

require 'includes/functions.php';

// Get Item ID
$id = $_GET['id'];

// Item won't show on index.php recently viewed if it's older than 1hr
if(getItemInfo($id))
{
    // Create array for recently view in index page
    if(array_key_exists('recentViews', $_COOKIE))
    {
        $cookie = $_COOKIE['recentViews'];
        $cookie = json_decode($cookie, true);

        if(in_array($id, $cookie))
        {
            $cookie = array_diff($cookie, array($id));

        }
        if(count($cookie) >= 4)
        {
            $cookie = array_slice($cookie, 1);
            array_push($cookie, $id);
            $cookie = json_encode($cookie);
            setcookie('recentViews', $cookie);
        }
        else
        {
        array_push($cookie, $id);
        $cookie = json_encode($cookie);
        setcookie('recentViews', $cookie);
        }
    }
    else 
    {
        $cookie = array();
        array_push($cookie, $id);
        $cookie = json_encode($cookie);
        setcookie('recentViews', $cookie);
    }
}


// Get item info from database
$itemInfo = getItemInfo($id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>COMP 3015</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div id="wrapper">

    <div class="container">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h1 class="login-panel text-center text-muted">
                    COMP 3015 Final Project
                </h1>
                <hr/>
            </div>
        </div>
<!-- Show item info from database -->
        <div class="row">
    <?php
        foreach($itemInfo as $item)
        {
    ?>
            <div class="col-md-offset-3 col-md-6">
                <div>
                    <p>
                        <a class="btn btn-default" href="index.php">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </p>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <?php echo $item['title'];?> 
                    </div>
                    <div class="panel-body text-center">
                        <p>
                            <img class="img-rounded img-thumbnail" src="products/<?php echo $item['picture'];?>"/>
                        </p>
                        <p class="text-muted text-justify">
                            <?php echo $item['description'];?>
                        </p>
                    </div>
                    <div class="panel-footer ">

                    <?php 
                   
                        $userInfo = getUserInfo($item['user_id']);
                        foreach($userInfo as $user)
                        {
                    ?>
                        <span><a href="mailto:<?php echo $user['email']; ?>">
                        <i class="fa fa-envelope"></i> <?php echo ucwords($user['f_name'].' '.$user['l_name']); ?></a></span>                        
                    <?php
                        }
                    ?> 
                        <span class="pull-right">$<?php echo $item['price'];?></span>
                    </div>
                </div>
            </div>
    <?php
        }
    ?>

            
        </div>

    </div>

</div>


</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
