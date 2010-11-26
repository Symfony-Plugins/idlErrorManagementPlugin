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
    
  public function executeShow(){
    $this->error = $this->getRoute()->getObject();
  }
  
}
