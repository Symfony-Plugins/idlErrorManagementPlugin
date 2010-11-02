<?php
/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * idlCommentApplicationErrorActions Actions related to comment error 
 * @package    idlErrorManagementPlugin
 * @author     David Jeanmonod  <david AT idael.ch>
 */
class idlCommentApplicationErrorActions extends sfActions {

  /**
   * Specific page with a form to allow user to comment the last error
   * @param sfWebRequest $request
   */
  public function executeCommentLastError(sfWebRequest $request) {

    try {
      // We try to retrieved the last error from the session
      $errorId = $this->getContext()->getStorage()->read(idlErrorManagement::LAST_ERROR_SESSION_KEY);
      $error = Doctrine::getTable('ApplicationError')->findOneBy('id', $errorId);
      
      // If there was no error, than means that we get here from a direct access, for exemple a redirect from the browser
      //  due to an ajax error. It's a pity that there is no error record, but the user can still provide some information
      //  so we create an empty error right now we create an 
      if ( ! $error ) {
        $error = new ApplicationError();
        $error->setMessage("Unkown error");
        $error->save(); 
      }
      
      // Create the form
      $this->form = new CommentApplicationErrorForm($error);
      $this->setLayout(false);
    }
    catch (Exception $e){
      throw new idlErrorManagementException($e);
    }
      
  }
  
  /**
   * Attach user comment on the last error 
   * @param sfWebRequest $request
   */
  public function executePostComment(sfWebRequest $request) {
    
    // Recareate the form and bind it with the posted value
    $error = $this->getRoute()->getObject();
    $form = new CommentApplicationErrorForm($error);
    $form->bind($request->getParameter($form->getName()));
    
    if ($form->isValid()){
      $form->save();
      $errorId = $this->getContext()->getStorage()->remove(idlErrorManagement::LAST_ERROR_SESSION_KEY);
      
      if (sfConfig::get('app_error_management_send_mail_on_user_comment', false)){
        idlErrorManagement::sendErrorByEmail($error);
      }
      
      $this->getUser()->setFlash("info","Report error success");
      $this->redirect(sfConfig::get('app_error_management_after_comment_route'));
    }
    else {
      // Redisplay
      $this->form = $form;
      $this->setTemplate('commentLastError');
      $this->setLayout(false);
    }
  }

}