<?php

/**
 *  Unit testing of the class idlErrorManagementException located in
 *   lib/exception 
 */
include_once(dirname(__FILE__).'/../../../bootstrap/unit.php');
$t = new lime_test(7, new lime_output_color());
$t->ok(class_exists('idlErrorManagementException'), "Autoloading of the class idlErrorManagementException is working");


// Basic exception
$e = new idlErrorManagementException('Message 123');
$t->ok($e instanceof idlErrorManagementException, 'Creation of idlErrorManagementException with a text message is working');
$t->is($e->getMessage(), 'Message 123', '->getMessage() Return the text message');

// Encaplusated exception
class MyEx extends Exception {
  public function myMethod() {return "Toto";}
} 
$e1 = new MyEx('Message 345');
$e2 = new idlErrorManagementException($e1);
$t->ok($e2 instanceof idlErrorManagementException, 'Creation of idlErrorManagementException with an encapsulated exception is working');
$t->is($e2->getMessage(), idlErrorManagementException::encapsulateMessagePrefix.'Message 345', '->getMessage() Return the message of the encapulated exception, with a prefix');
$t->is($e2->getCode(), $e1->getCode(), '->getCode() Return the code of the encapulated exception');
$t->is($e2->myMethod(), "Toto", '->myMethod() Forward call to specific method to the encapsulated exception');