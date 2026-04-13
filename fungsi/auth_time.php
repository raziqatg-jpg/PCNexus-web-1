<?php
    session_start();
    //belum masuk
    if (!isset($__SESSITION ['login'])|| $_SESSION['login'] !== true) {
        header("location: login.php");
        exit;
    }

    //batas waktu login(15 hari)
    $max_time = 15 * 24 * 60 * 60;

     if (!isset($__SESSITION ['login_time'])) {
        if (time() - $_SESSION['login_time'] > $max_time){
            // session 
            session_unset();
            session_destroy();

            header("location: login.php>popup=session_experred");
            exit;
        }
     } else {
        session_destroy();
        header("location:login.php");
        exit;
     }
?>