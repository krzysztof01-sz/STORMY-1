<?php
require_once "../classes/AdminAuth.php";
require_once "../classes/Menu.php";
require_once "../classes/PageSettings.php";

$adminAuth = new AdminAuth();

@$adminAuth->controlAccess($_POST['adminPassword']);

$settings = new PageSettings("../settings/default.json");
$page = new Page($settings, "..");

$page->renderHead();

if (isset($_GET['firstTime']))
   $adminAuth->handleFirstTimeLogging($_GET['firstTime']);

$adminAuth->renderPrompt();
$adminAuth->renderLoggingForm();

$page->renderFooter();