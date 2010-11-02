<?php

/**
 *  Unit testing of the class CommentApplicationErrorForm located in
 *   lib/form/doctrine 
 */
$nbrOfTest = 7;
include_once(dirname(__FILE__).'/../../../../bootstrap/unit.php');
$t = new lime_test($nbrOfTest, new lime_output_color());
$t->ok(class_exists('CommentApplicationErrorForm'), "Autoloading of the class CommentApplicationErrorForm is working");

if ( ! class_exists('idlUnitTestingTools') ){
  $t->skip("The plugin idlDevTools must be active to run theses tests", $nbrOfTest-1);
}
else {
  // Create a temporary DB
  idlUnitTestingTools::createDoctrineDatabaseForModel(array('ApplicationError'));
  
  // Test around creation
  try { new CommentApplicationErrorForm(); $t->fail('->__construct() Must be construct with an ApplicationError object'); }
  catch(Exception $e) {$t->pass('->__construct() Must be construct with an ApplicationError object'); }
  try { new CommentApplicationErrorForm(new ApplicationError()); $t->fail('->__construct() Must be construct with an existing ApplicationError object'); }
  catch(Exception $e) {$t->pass('->__construct() Must be construct with an existing ApplicationError object'); }
  $error = new ApplicationError(); $error->setMessage('Error 123'); $error->save();
  $form = new CommentApplicationErrorForm($error);
  $t->ok($form instanceof CommentApplicationErrorForm, "Creation of the form is working");
  
  // Test arround data binding
  $form->bind(array());
  $t->is($form->isValid(), false, "Binding with empty values is invalid");
  $form->bind(array('comment'=>'My error comment'));
  $t->is($form->isValid(), true, "Binding with a comment is invalid");
  $form->save();
  $error = Doctrine::getTable('ApplicationError')->findOneByMessage('Error 123'); 
  $t->is($error->getComment(), 'My error comment', "->save() Have register the user comment to the error"); 
}

