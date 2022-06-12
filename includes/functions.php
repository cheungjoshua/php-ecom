<?php

require 'includes/connect.php';

define('SALT', '12345');
define('FILE_SIZE_LIMIT', 4000000);

function findUser($inputemail, $password)
{
    $found = false;
    $email = trim(filterInput($inputemail));
    $pass  = trim($password);

    $link  = connect();
    $hash  = md5($pass. SALT);

    $query = 'select * from users where email = "'.$email.'" and password = "'.$hash.'"';
    $result = mysqli_query($link, $query);

    if(mysqli_fetch_array($result))
    {
        $found = true;
    }
    
    mysqli_close($link);
    return $found;
}

function getUserName($email, $pass)
{
    $link = connect();
    $hash = md5($pass. SALT);

    $query = 'select f_name, l_name from users where email = "'.$email.'" and password = "'.$hash.'"';
    $result = mysqli_query($link, $query);

    if($result)
    {
        foreach($result as $line)
        {
            $username = ucwords($line['f_name'].' '.$line['l_name']);
        }
    }

    return $username;
}

function checkUserName($data)
{
    return preg_match('/^([a-z]{5,12})$/i', $data);
}

function filterInput($data)
{
    return preg_replace(array('/</', '/>/'), '', $data);
}

function checkSignUp($data)
{
    $valid = true;

    $f_name      = trim(filterInput($data['f_name']));
    $l_name      = trim(filterInput($data['l_name']));
    $email       = trim(filterInput($data['email']));
    $password    = trim($data['password']);
    $verify_pass = trim($data['verify_pass']);

    if( $f_name      == '' ||
        $l_name      == '' ||
        $password    == '' ||
        $email       == '' ||
        $verify_pass == '')
    {
        $valid = false;
    }
    if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
    {
        $valid = false;
    }
    elseif(!checkUserName($f_name) && !checkUserName($l_name)) 
    {
        $valid = false;
    }
    elseif($password != $verify_pass)
    {
        $valid = false;
    }

    return $valid;
}

function saveUser($data)
{
    $f_name   = trim(filterInput($data['f_name']));
    $l_name   = trim(filterInput($data['l_name']));
    $email    = trim(filterInput($data['email']));
    $password = trim($data['password']);
    $hash     = md5($password . SALT);

    $link     = connect();
    $query    = "insert into users(f_name, l_name, email, password) 
                values('$f_name', '$l_name', '$email', '$hash')";

    $success  = mysqli_query($link, $query);

    mysqli_close($link);
    return $success;
}

function getuserId($data)
{
    $name   = preg_split('/\s/', $data['username']);
    $f_name = $name['0'];
    $l_name = $name['1'];
    $link   = connect();
    $query  = "SELECT id FROM users WHERE f_name = '$f_name' AND l_name = '$l_name'";
    $result = mysqli_query($link, $query);
    foreach($result as $line)
    {
        $id = $line['id'];
    }
    return $id;
     
}

function checkPost($file)
{
    return true;
    $file_extensions = array("image/png", "image/jpg");

    if($file['picture']['size'] < FILE_SIZE_LIMIT && 
    in_array( $file['picture']['type'], $file_extensions))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function savePost($data, $file, $username, $id)
{
    $title   = $data['title'];
    $price   = $data['price'];
    $desc    = $data['description'];
    $picture = md5($username.time());
    
    $moved   = move_uploaded_file($file['picture']['tmp_name'],
                'products/'.$picture);

    if($moved)
    {
        $link   = connect();
        $query  = "INSERT INTO items(title, price, `description`, picture, `user_id`)
                  VALUES('$title', '$price', '$desc', '$picture', $id)";
        $result = mysqli_query($link, $query);

        mysqli_close($link);
        return $result;
    }

    return true;
}

function getAllItem_NU()
{
    $link  = connect();
    $query = "select items.id as item_id, title, price, picture, description, post_time, down_vote, 
                users.id as user_id, email, f_name, l_name 
                from items 
                inner join users 
                where items.user_id = users.id 
                and down_vote <5
                and post_time >= NOW()- INTERVAL 1 HOUR
                ";
    $products = mysqli_query($link, $query);

    mysqli_close($link);
    return $products;
}

function getAllItems($user)
{
    $userId = getuserId($user);
    $link   = connect();
    $query  = "select items.id as item_id, title, price, picture, description, post_time, down_vote, 
                users.id as user_id, email, f_name, l_name 
                from items 
                inner join users 
                where items.user_id = users.id 
                and down_vote <5
                and post_time >= NOW()- INTERVAL 1 HOUR
                order by case 
                when items.id in (select item_id from pin where user_id = $userId) 
                then -1 else items.id end, items.id";
    
    $products = mysqli_query($link, $query);

    mysqli_close($link);
    return $products;
}

function getUserInfo($id) 
{
    $link   = connect();
    $query  = "SELECT * FROM users WHERE id = $id ORDER BY f_name, l_name";
    $result = mysqli_query($link, $query);

    mysqli_close($link);
    return $result;
}

function getItemInfo($id)
{
    $link   = connect();
    $query  = "SELECT * FROM items WHERE id = $id                 
                AND post_time >= NOW()- INTERVAL 1 HOUR";
    $result = mysqli_query($link, $query);
    if(mysqli_num_rows($result) === 0)
    {
        return false;
    }
    mysqli_close($link);
    return $result;
}

function getViewed($cookie)
{   
    $ids   = implode(',', $cookie);
    $link  = connect();
    $query = "select items.id as item_id, title, price, picture, description, post_time, down_vote, pin,
                users.id as user_id, email, f_name, l_name 
                from items 
                inner join users 
                where items.user_id = users.id 
                and   items.id in ({$ids})
                order by f_name, price desc";
    

    $result = mysqli_query($link, $query);

    mysqli_close($link);
    return $result;
}

function pinItem($itemId, $user)
{   
    $userId = getuserId($user);
    $link   = connect();
    $query  = "INSERT INTO pin (item_id, `user_id`)
                VALUES ('$itemId', '$userId')";
    $pin    = mysqli_query($link, $query);

    mysqli_close($link);
    return $pin;
}

function unpinItem($itemId, $user)
{
    $userId = getuserId($user);
    $link   = connect();
    $query  = "DELETE FROM pin WHERE item_id = $itemId and `user_id` = $userId";
    $pin    = mysqli_query($link, $query);

    mysqli_close($link);
    return $pin;
}

function deleteItem($itemId, $user)
{
    $userId  = getuserId($user);
    $link    = connect();
    $query   = "DELETE FROM items WHERE id = $itemId and `user_id` = $userId";
    $delete  = mysqli_query($link, $query);

    mysqli_close($link);
    return $delete;
}

function searchItems($data)
{
    $link   = connect();
    $query  = "select items.id as item_id, title, price, picture, description, post_time, down_vote, pin,
                users.id as user_id, email, f_name, l_name 
                from items 
                inner join users 
                on items.user_id = users.id 
                where (title like '%{$data}%')
                or (description like '%{$data}%');";
    $result = mysqli_query($link, $query);

    mysqli_close($link);
    return $result;
}

function downvote($itemId,$userId)
{
    $link     = connect();
    $downvote = "INSERT INTO down_vote(item_id, `user_id`)
                VALUES ('$itemId', '$userId')";

    $result   = mysqli_query($link, $downvote);

    $updateItem = "UPDATE items SET down_vote = down_vote +1 WHERE id = $itemId";
    mysqli_query($link, $updateItem);
    
    mysqli_close($link);
    return $result;
}

function itemVoteNum($id)
{
    $link   = connect();
    $query  = "SELECT count(id) FROM down_vote WHERE item_id = $id";
    $result = mysqli_query($link, $query);
    foreach($result as $line)
    {
        $count = $line['count(id)'];
    }
    mysqli_close($link);
    return $count;
}

function votedItem($id)
{
    $link   = connect();
    $query  = "SELECT `user_id` FROM down_vote WHERE item_id = $id";
    $result = mysqli_query($link, $query);

    foreach($result as $line)
    {
        $item_id = $line['user_id'];
    }
    mysqli_close($link);
    return $item_id;
}



function checkPin($itemId, $user)
{
    $userId = getuserId($user);
    $found  = false;
    $link   = connect();
    $query  = "select user_id from pin where item_id = $itemId";
    $result = mysqli_query($link, $query);

    while($row = mysqli_fetch_array($result))
    {
        if($row['user_id'] == $userId)
        {
            $found = true;
        }
    }
    mysqli_close($link);
    return $found;
}