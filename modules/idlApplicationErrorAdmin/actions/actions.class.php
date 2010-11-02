<?php
/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../lib/idlApplicationErrorAdminGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/idlApplicationErrorAdminGeneratorHelper.class.php';

/**
 * idlApplicationErrorAdmin actions.
 */
class idlApplicationErrorAdminActions extends autoIdlApplicationErrorAdminActions {
  
  /* 
  //Prototype of error display
  public function executeListDetail(){
    $error = $this->getRoute()->getObject();
    $code = $error->getCode();
    $text = "Detail of the error";
    $name = $error->getType();
    $message = $error->getMessage();
    $traces = explode("\n",$error->getTrace());
    $template = sfException::getTemplatePathForError('html', true);
    ob_start();
    include $template;
    return $this->renderText(ob_get_clean());
  }
  */
}
