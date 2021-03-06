<?php
session_start();
if ($_POST['bname'] == "" or ""==$_POST['pwort'] ) {
    header("Location:index.php?site=login&error=true");
    unset($_SESSION['username']);
    die();
}

//load users.csv into an array 'username'=>'password'
$user_file = @file('users.csv');//suppress warnings

if($user_file != null) {
    $users = array();
    for($i = 0; $i < count($user_file); $i++) {
        $line = explode(';',$user_file[$i]);
        $line[1] = str_replace("\r",'',$line[1]);
        $line[1] = str_replace("\n",'',$line[1]);
        $users[$line[0]] = $line[1];
    }

    if($users[$_POST['bname']] == md5($_POST['pwort']) and $users[$_POST['bname']] != '') {
        $_SESSION['username'] = $_POST['bname'];    //Anmeldung
        header("Location:index.php");
        die();
    }
}

//Wenn das User-File leer ist / die Credentials nicht gepasst haben
header("Location:index.php?site=login&error=true"); //Fehlermeldung
die();
