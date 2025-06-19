<?php

function cek_login()
{
    if (!isset($_SESSION['role'])) {
        header('Location: login.php');
        exit();
    }
}

function cek_role($allowed_roles = [])
{
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header('Location: unauthorized.php');
        exit();
    }
}