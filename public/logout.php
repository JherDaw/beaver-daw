<?php
require __DIR__ . '/../src/sesion.php';

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
