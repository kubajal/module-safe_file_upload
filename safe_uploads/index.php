<?php

require_once("../global/library.php");

use FormTools\Core;
use FormTools\Sessions;

Core::init();

// if this user is already logged in, redirect them to their own login page
if (!Core::$user->isLoggedIn()) {
    die("get lost script kiddies");
}

$db = Core::$db;
$account_id = Core::$user->getAccountId();
$file = $request["file"];

if(is_null($file) || empty($file)) {
  die("something went wrong");
}

$db->query("SELECT * FROM {PREFIX}module_safe_files WHERE file='" . $file . "'");
$db->execute();

$file_info = $db->fetchAll();

if(count($file_info) == 0)
  die("no info about this file");

$a = 1;