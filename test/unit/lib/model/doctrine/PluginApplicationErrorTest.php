<?php

/**
 *  Unit testing of the class PluginApplicationError located in
 *   lib/model/doctrine 
 */
$nbrOfTest = 8;
include_once(dirname(__FILE__).'/../../../../bootstrap/unit.php');
$t = new lime_test($nbrOfTest, new lime_output_color());
$t->ok(class_exists('PluginApplicationError'), "Autoloading of the class PluginApplicationError is working");

if ( ! class_exists('idlUnitTestingTools') ){
  $t->skip("The plugin idlDevTools must be active to run theses tests", $nbrOfTest-1);
}
else {
  // Create a temporary DB
  idlUnitTestingTools::createDoctrineDatabaseForModel(array('ApplicationError'));
 
  // Test updateWithException
  $error = new ApplicationError();
  $error->updateWithException(new Exception("Error 404", 404));
  $t->is($error->getMessage(), "Error 404", "->updateWithException() Save the exception message");
  $t->is($error->getFile(), __FILE__, "->updateWithException() Save the file where exception get raise");
  $t->is($error->getLine(), 21, "->updateWithException() Save the exception line");
  $t->is($error->getType(), "Exception", "->updateWithException() Save the exception class name");
  
  // Test updateWithContext
  include_once sfConfig::get('sf_symfony_lib_dir').'/../test/unit/sfContextMock.class.php';
  $error->updateWithContext(new sfContext());
  $t->is($error->getModule(), 'module', "->updateWithContext() Save the module name");
  $t->is($error->getAction(), 'action', "->updateWithContext() Save the action name");

  // Test getFormattedDescription
  $t->ok(strlen($error->getFormattedDescription())>0, "->getFormattedDescription() Return a well formated description");
  
}
