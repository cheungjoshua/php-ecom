<?php 
session_start();

require 'includes/functions.php';


if(isset($_GET['search']))
{
    $products = searchItems($_GET['search']);
}
elseif(isset($_SESSION['loggedin']))
{
    $products = getAllItems($_SESSION);
}
else
{
    $products= getAllItem_NU();
}

$cookie = json_decode($_COOKIE['recentViews'], true);
$viewed = getViewed($cookie);

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

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            <?php 
                if(isset($_SESSION['loggedin']))
                {
            ?>
                <button class="btn btn-default" data-toggle="modal" data-target="#newItem"><i class="fa fa-photo"></i> New Item</button>
                <a href="logout.php" class="btn btn-default pull-right"><i class="fa fa-sign-out"> </i> Logout</a>
            <?php     
                }
                else
                {
            ?>
                <a href="#" class="btn btn-default pull-right" data-toggle="modal" data-target="#login"><i class="fa fa-sign-in"> </i> Login</a>
                <a href="#" class="btn btn-default pull-right" data-toggle="modal" data-target="#signup"><i class="fa fa-user"> </i> Sign Up</a>
            <?php         
                }
            ?>             
                
            </div>
        </div>
<!-- Recently Viewed -->
        <div class="row">
            <div class="col-md-3">
                <h2 class="login-panel text-muted">
                    Recently Viewed
                </h2>
                <hr/>
            </div>
        </div>
        <div class="row">

            <?php 
            foreach($viewed as $item)
            {
            ?>
            <div class="col-md-3">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <?php echo $item['title']; ?>
                        <span class="pull-right text-muted">

                            <?php 
                            if(getuserId($_SESSION) == $item['user_id'])
                            {
                                echo '<a class="" href="session.php?from=delete&id='.$item['id'].'" data-toggle="tooltip" title="Delete item">
                                      <i class="fa fa-trash"></i>
                                      </a>';
                            }
                            ?>

                        </span>
                    </div>
                    <div class="panel-body text-center">
                        <p>
                            <a href="product.php?id=<?php echo $item['item_id']; ?>">
                                <img class="img-rounded img-thumbnail" src="products/<?php echo $item['picture']; ?>"/>
                            </a>
                        </p>
                        <p class="text-muted text-justify">
                            <?php echo $item['description']; ?>                        
                        </p>

                        <?php 
                        if(isset($_SESSION['loggedin']))
                        {
                            echo '<a class="pull-left" href="session.php?from=downvote&id='.$item['item_id'].'" data-toggle="tooltip" title="Downvote item">
                                    <i class="fa fa-thumbs-down"></i>
                                    </a>';
                        }
                        ?>  
                    </div>
                    <div class="panel-footer ">
                        <span><a href="mailto:<?php echo $item['email']; ?>" data-toggle="tooltip" title="Email seller">
                        <i class="fa fa-envelope"></i> <?php echo ucwords($item['f_name'].' '.$item['l_name']); ?></a></span>
                        <span class="pull-right">$<?php echo $item['price']; ?></span>
                    </div>
                </div>
            </div>
            <?php 
            }
            ?>
        </div>

        <div class="row">
            <div class="col-md-3">
                <h2 class="login-panel text-muted">
                    Items For Sale
                </h2>
                <hr/>
            </div>
        </div>
     <!-- search -->
        <div class="row">
            <div class="col-md-4">
                    <form class="form-inline" action="index.php" method="get" >
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-search"></i></div>
                                <input type="text" class="form-control" name="search" placeholder="Search" id="search"/>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-default" value="Search"/>
                        <button class="btn btn-default" data-toggle="tooltip" title="Shareable Link!" type="button"><i class="fa fa-share"></i></button>
                    </form>
                <br/>
            </div>
        </div>

        <div class="row">
        <!-- items -->
            <!-- Add one more if statment to check item post time less than 1 hr -->

            <?php 
            foreach($products as $product)
            {

            ?> 
            
            <div class="col-md-3">
            <?php
                if(checkPin($product['item_id'], $_SESSION))
                {
                    echo '<div class="panel panel-warning">';
                }elseif(!checkPin($product['item_id'], $_SESSION) || $_SESSION['loggedin'] != true)
                {
                    echo '<div class="panel panel-info">';
    
                }
            ?>                
                    <div class="panel-heading">
                        <!-- Show pin/unpin items -->
                        <?php 
                        if(checkPin($product['item_id'], $_SESSION) && $_SESSION['loggedin'])
                        {
                            echo ' <a class="" href="session.php?from=unpin&id='.$product['item_id'].'" data-toggle="tooltip" title="Unpin item">
                                   <i class="fa fa-dot-circle-o"></i>
                                   </a>';
                        }
                        elseif(!checkPin($product['item_id'], $_SESSION) && $_SESSION['loggedin'])
                        {
                            echo '<a class="" href="session.php?from=pin&id='.$product['item_id'].'" data-toggle="tooltip" title="Pin item">
                                  <i class="fa fa-thumb-tack"></i>
                                  </a>';
                        }
                        ?>
                        <!-- show items title  -->
                        <span>
                            <?php echo $product['title']; ?>
                        </span>
                        <span class="pull-right">
                        <!-- show item delete link -->
                        <?php 
                        if(getuserId($_SESSION) == $product['user_id'])
                        {
                            echo '<a class="" href="session.php?from=delete&id='.$product['item_id'].'" data-toggle="tooltip" title="Delete item">
                                  <i class="fa fa-trash"></i>
                                  </a>';
                        }                        
                        ?>   
                        </span> 
                    </div>
                    <div class="panel-body text-center">
                        <p>
                            <a href="product.php?id=<?php echo $product['item_id']; ?>">
                                <img class="img-rounded img-thumbnail" src="products/<?php echo $product['picture']; ?>"/>
                            </a>
                        </p>
                        <p class="text-muted text-justify">
                            <?php echo $product['description']; ?>      
                        </p>
                        <?php 
                        // Add check downvote table user_id is same as $_SESSION user ID or not
                        if(isset($_SESSION['loggedin']) && getuserId($_SESSION) != votedItem($product['item_id']))
                        {
                            echo '<a class="pull-left" href="session.php?from=downvote&id='.$product['item_id'].'" data-toggle="tooltip" title="Downvote item">
                                    <i class="fa fa-thumbs-down"></i>
                                    </a>';
                        }
                        ?>                    
                    </div>
                    <div class="panel-footer ">
                        <span><a href="mailto:<?php echo $product['email']; ?>" data-toggle="tooltip" title="Email seller">
                        <i class="fa fa-envelope"></i> <?php echo ucwords($product['f_name'].' '.$product['l_name']); ?></a></span>
                        <span class="pull-right">$<?php echo $product['price']; ?></span>
                    </div>
                </div>
            </div>

            <?php 
                
            }
            ?>
            
        </div>  
    </div>

</div>

<div id="login" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form role="form" method="post" action="redirect.php?from=login" name="login">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Login</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" type="password" name="password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Login!"/>
            </div>
        </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="newItem" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form role="form" method="post" action="newitem.php" name="newItem" enctype="multipart/form-data">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">New Item</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Title</label>
                    <input class="form-control" type="text" name="title">
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input class="form-control" type="text" name="price">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input class="form-control" type="text" name="description">
                </div>
                <div class="form-group">
                    <label>Picture</label>
                    <input class="form-control" type="file" name="picture">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Post Item!"/>
            </div>
        </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="signup" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form role="form" method="post" action="redirect.php?from=signup" name="signup">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Sign Up</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>First Name</label>
                    <input class="form-control" type="text" name="f_name">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input class="form-control" type="text" name="l_name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" type="password" name="password">
                </div>
                <div class="form-group">
                    <label>Verify Password</label>
                    <input class="form-control" type="password" name="verify_pass">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Sign Up!"/>
            </div>
        </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

</html>
