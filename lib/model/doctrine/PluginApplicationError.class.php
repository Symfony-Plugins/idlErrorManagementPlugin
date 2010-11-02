<?php
/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * PluginApplicationError, reprents error object that are going to be record
 *
 * @package    idlErrorManagementPlugin
 * @subpackage model
 * @author     David Jeanmonod  <david AT idael.ch>
 */
abstract class PluginApplicationError extends BaseApplicationError {
  
  /**
   * Update properties with an exception object
   * @param Exception $e
   */
  public function updateWithException($e) {
    $this->fromArray(array(
      'message' => $e->getMessage(),
      'file' => $e->getFile(),
      'line' => $e->getLine(),
      'trace' => $e->getTraceAsString(),
      'type' => get_class($e),
      'code' => $e->getCode()
    ));
  }

  /**
   * Update properties with the symfony context
   * @param sfContext  An instance of sfContext
   */
  public function updateWithContext($context = null) {
    if ($context==null && !sfContext::hasInstance()){
      return;
    }
    $c = is_null($context) ? sfContext::getInstance() : $context;
    $this->fromArray(array(
      'module' => $c->getModuleName(),
      'action' => $c->getActionName()
    ));
    if (is_object($r = $c->getRequest())){
      $this->fromArray(array(
        'uri' => $r->getUri(),
        'user_agent' => $r->getHttpHeader('User-Agent')
      ));
    }
  }
  
  /**
   * Update properties with symfony user
   * @param sfUser  An instance of sfUser
   */
  public function updateWithUser($user = null) {
    $username = '-';
    try {
      $user = is_null($user) ? sfContext::getInstance()->getUser() : $user;
      foreach (array('getUsername', 'getName', '__toString') as $method){
        if (method_exists($user, $method)){
          $username = (string) $user->$method();
          break;
        }
      }
    }
    catch (Exception $e){} 
    $this->setUser($username);
  }
  
  /** 
   * Remove the base dir from the file name
   */
  public function getShortFilePath(){
    if ( strpos($this->getFile(),sfConfig::get('sf_root_dir')) !== false ){
      return substr($this->getFile(),strlen(sfConfig::get('sf_root_dir')));
    }
    return $this->getFile();
  }
  
  /**
   * Return a formated description of the error
   */
  public function getFormattedDescription(){
    $desc = "";
    foreach ( $this->toArray() as $name => $value){
      if (isset($value) && strlen($value)>0) {
        $desc .= str_pad($name."\n", strlen($name)*2  , "-") ."\n$value\n\n";
      }
    }
    return $desc;
  }
  
}