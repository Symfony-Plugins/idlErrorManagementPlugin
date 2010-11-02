<?php
/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * idlErrorManagementToolsActions Provide symfony tools for error management
 * 
 * @package    idlErrorManagementTools
 * @author     David Jeanmonod  <david AT idael.ch>
 */
class idlErrorManagementToolsActions extends sfAjaxActions {

  /**
   * This action allow to convert any error 404 to a standard ApplicationError
   * To use it, just configure the setting.yml with 
   * 
   *  .settings
   *    error_404_module:       idlErrorManagementTools          # To be called when a 404 error is raised
   *    error_404_action:       convert404ToApplicationError     # Or when the requested URL doesn't match any route
   *    
   * @param sfWebRequest $request
   */
  public function executeConvert404ToApplicationError(sfWebRequest $request){
    
    // Initialy I wanted to do that: 
    
    //  throw new sfStopException(
    //    "The user action generate an error 404. The url he try to access was [".
    //    $request->getUri().
    //    "]. You can find more information the the logs"
    //  );
    
    // But apparently it's already to late in the symfony process to throw a normal error
    //  so we have to create the error recording by our self
    
    
    // Create the error
    $error = new ApplicationError();
    $error->setMessage(
      "The user generate an error 404. The url he try to access was [".
      $request->getUri()."]"
    );
    $error->setUri($request->getUri());
    $error->setType('Page not found');
    $error->setCode(404);
    $error->updateWithUser($this->getUser());
    $error->save();
    idlErrorManagement::registerErrorForComment($error);
    
    if ( idlErrorManagement::redirectToCommentFormIfRequire() ){
      return sfView::NONE;
    }
    else {
      $this->getResponse()->setStatusCode(500);
      return $this->renderText("Error 404: Wrong address: ".$request->getUri());
    }
   
  }
}