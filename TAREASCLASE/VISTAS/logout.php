<?php
session_start();
session_unset();
session_destroy();
header("Location: inicio1.php");
exit;
