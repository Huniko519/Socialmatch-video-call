<?php

if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}
if (isset($_SESSION['new_user'])) {
    unset($_SESSION['new_user']);
}
header('Location: ' . smoothLink('mobile.php?page=index'));
