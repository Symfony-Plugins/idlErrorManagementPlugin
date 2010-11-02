<?php
/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * This application error exception is the only one that is ignore by the application error manager.
 * This allow to send exception inside this module without starting an infinite loop
 * 
 * @package    idlErrorManagementPlugin
 * @author     David Jeanmonod  <david AT idael.ch>
 *
 */
class idlErrorManagementException extends Exception {
  
  const encapsulateMessagePrefix = "Encapsulated exception: "; 
  private $encapsulate;
  
  /**
   * Exception constructor
   * @param mixed $messageOrException    Can be construct with a normal message, or with an existing 
   *                                      exception, encapsulating existing exception allow to conserve
   *                                      there data, but prevent infinit loop in error management  
   * @param string $code                 Normal exception code
   */
  public function __construct($messageOrException, $code=null){
    // Possible to construct by encapsulating an existing exception
    if ($messageOrException instanceof Exception){
      $this->encapsulate = $messageOrException;
      parent::__construct(self::encapsulateMessagePrefix.$this->encapsulate->getMessage(), $this->encapsulate->getCode());
    }
    // Normal Exception
    else {
      parent::__construct($messageOrException, $code);
    }
  }
    
  /**
   * Used to forward every call to the exception itself or to the encaplusate one
   * @param string   $method
   * @param array    $arguments
   */
  public function __call($method, $arguments){
    if (isset($this->encapsulate)){
      return call_user_func_array(array($this->encapsulate, $method), $arguments);
    }
    throw new Exception("No method $method() define on idlErrorManagementException");
  }

}