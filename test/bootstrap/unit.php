<?php

// Use the same bootstrap than the project

$normalProjectBootstrapFile = dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
$testProjectBootstrapFile = dirname(__FILE__).'/../../../testProject/test/bootstrap/unit.php';

if (file_exists($normalProjectBootstrapFile)) 
  include_once($normalProjectBootstrapFile);
elseif (file_exists($testProjectBootstrapFile)) 
  include_once($testProjectBootstrapFile);
else 
  throw new Exception("Can only be tested in a project context");