<?php

session_start();
if(isset($_SESSION['role'])){
    if($_SESSION['role']=='admin') header('Location:admin/index.php');
    else header('Location:user/index.php');
}
else {
    header('Location: auth/login.php');
}
exit;

?>