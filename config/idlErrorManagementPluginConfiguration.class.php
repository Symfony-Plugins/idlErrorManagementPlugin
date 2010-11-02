<?php

/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * @package    idlErrorManagementPlugin
 * @subpackage config
 * @author     David Jeanmonod  <david AT idael.ch>
 */
class idlErrorManagementPluginConfiguration extends sfPluginConfiguration {

  
  /**
   * @see sfPluginConfiguration::initialize()
   */
  public function initialize() {

    // Check around HTTP_HOST to prevent using this plugin in CLI mode
    if ( ! isset($_SERVER['HTTP_HOST']) ) {
      return;
    }
    
    // Check that email is weel configure if we need it
    if (sfConfig::get('app_error_management_send_mail_on_error', false) || sfConfig::get('app_error_management_send_mail_on_user_comment', false)){
      if (sfConfig::get('app_error_management_email') == 'info@example.com' || sfConfig::get('app_error_management_email' == '')){
        throw new Exception("You must configure a valid address mail in app.yml under the key app_error_management_email");
      } 
    }
 
    // As we want to prevent cycle in error management, we must rethrow any exception as an idlErrorManagementException
    try {    
      // Request to be notify when an exception reach symfony controller, 
      $this->dispatcher->connect('application.throw_exception', array('idlErrorManagement', 'processApplicationError'));
      // configure the php error handler
      if ( isset($_SERVER['HTTP_HOST']) && sfConfig::get('app_error_management_record_php_error', false)){
        idlErrorManagement::registerPhpErrorListener();
      }
    }
    catch (Exception $e){
      throw new idlErrorManagementException($e);
    }
    
  }
}
