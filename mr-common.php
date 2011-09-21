<?php
require_once 'config';

$db_file = dirname(__FILE__) . '/reader.db';
try
{
    $db = new PDO("sqlite:$db_file");
}
catch (PDOException $ex)
{
    die("Open database failed: " . $ex->getMessage());
}

ini_set('session.save_handler', 'sqlite');
ini_set('session.save_path', dirname(__FILE__) . '/session.db');
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7);
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);
session_start();

function destroySession()
{
    $_SESSION = array();
    session_destroy();
}

function logout()
{
    destroySession();
    die("Please <a href='mr-login.php'>login</a> first.");
}

function dieOnDb()
{
    $error = $db->errorInfo();
    die('database error: ' . $error[2]);
}

function saveConfig($key, $value)
{
    global $db;

    $key = $db->quote($key);
    $value = $db->quote($value);
    $stmt = $db->query("select value from config where key=$key");
    if ($stmt === false)
        dieOnDb();
    elseif ($stmt->fetch(PDO::FETCH_NUM) === false)
    {
        $stmt->closeCursor();
        $db->exec("insert into config values ($key, $value)");
    }
    else
    {
        $stmt->closeCursor();
        $db->exec("update config set value=$value where key=$key");
    }
}

function fetchConfig($key)
{
    global $db;

    $key = $db->quote($key);
    $stmt = $db->query("select value from config where key=$key");
    if ($stmt === false)
        dieOnDb();
    $result = $stmt->fetch(PDO::FETCH_NUM);
    if ($result === false)
        return null;
    else
        return $result[0];
}

?>
