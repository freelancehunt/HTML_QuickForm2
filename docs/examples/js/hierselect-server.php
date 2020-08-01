<?php
/**
 * Usage example for HTML_QuickForm2 package: AJAX-backed hierselect element, AJAX server
 */

if (!class_exists('HTML_AJAX_Server', true)) {
    require_once 'HTML/AJAX/Server.php';
}
require_once '../support/hierselect-loader.php';

$server = new HTML_AJAX_Server();
$server->registerClass(new OptionLoader(), 'OptionLoader', ['getOptionsAjax']);
$server->handleRequest();
?>