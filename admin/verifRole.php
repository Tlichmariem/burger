<?php 
session_start();

if(!isset($_SESSION["user_role"]) && $_SESSION["user_role"] != "admin"){
   header('../index.php'); 
}