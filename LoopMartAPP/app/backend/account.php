<?php
session_start();
//test id user is sing in
if (isset($_SESSION['username'])) {
    echo $_SESSION['username'];
    exit;
}
//no
echo 'Signin';