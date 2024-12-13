<?php
session_start();
//test id user is sing in
if (isset($_SESSION['user_id'])) {
    echo $_SESSION['name'];
    exit;
}
//no
echo 'Signin';