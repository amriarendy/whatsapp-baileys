<?php
include_once("./helpers/connection.php");
include_once("./helpers/function.php");

toastr_set("mixin-success", "Your logout, session destroyed!");
session_destroy();
redirect("login.php");
