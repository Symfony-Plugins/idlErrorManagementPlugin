<?php

/**
 *  Unit testing of the class idlErrorManagement located in
 *   lib 
 */
include_once(dirname(__FILE__).'/../../bootstrap/unit.php');
$t = new lime_test(3, new lime_output_color());
$t->ok(class_exists('idlErrorManagement'), "Autoloading of the class idlErrorManagement is working");

$t->ok(is_string($path = idlErrorManagement::getPathToPhpErrorHandlingScript()), "->getPathToPhpErrorHandlingScript() return a string");
unlink($path);
idlErrorManagement::createPhpErrorHandlingScript();
$t->ok(is_file($path), "->createPhpErrorHandlingScript() create the script");

