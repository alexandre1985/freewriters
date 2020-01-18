<?php
$token = bin2hex(random_bytes(32));

session_start();
$_SESSION['csrf_token'] = $token;
