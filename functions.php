<?php
session_start();
define('USER_LOGIN', 'admin');
define('USER_PASS', 'pass');
function login($login, $password)
{
    if ($login == USER_LOGIN && $password == USER_PASS) {
        $_SESSION['admin'] = $login;
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        return true;
    }
    return false;
}
function isAdmin()
{
    if (!empty($_SESSION['ip']) && $_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
        session_destroy();
        return false;
    }
    return !empty($_SESSION['admin']);
}
function getAdminName()
{
    return !empty($_SESSION['admin']) ? $_SESSION['admin'] : null;
}
function logout()
{
    session_destroy();
}
function isPost()
{
    return !empty($_POST);
}
function getParamGet($name, $defaultValue = null)
{
    return !empty($_GET[$name]) ? $_GET[$name] : $defaultValue;
}
function getParamPost($name, $defaultValue = null)
{
    return !empty($_POST[$name]) ? $_POST[$name] : $defaultValue;
}
function getBaseUrl()
{
    $dir = dirname($_SERVER['REQUEST_URI']);
    if (!empty($dir) && $dir[strlen($dir) - 1] != '/') {
        $dir .= '/';
    }
    return $dir;
}
function getUrl($fileName = 'index')
{
    return getBaseUrl() . $fileName . '.php';
}
function location($fileName)
{
    header('Location: ' . getUrl($fileName));
}