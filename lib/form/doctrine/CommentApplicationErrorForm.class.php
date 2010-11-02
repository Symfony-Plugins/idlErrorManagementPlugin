<?php
/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * CommentApplicationErrorForm, This form is use to allow user to comment a error
 *
 * @package    idlErrorManagementPlugin
 * @subpackage form
 * @author     David Jeanmonod  <david AT idael.ch>
 */
class CommentApplicationErrorForm extends ApplicationErrorForm {
  
  /**
   * @see sfForm::__construct()
   * @param ApplicationError      The error used to initialize default values
   * @param array                 An array of options
   * @param string                A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   */
  public function __construct($object = null, $options = array(), $CSRFSecret = null) {
    if ( ! $object instanceof ApplicationError || $object->isNew()  ){
      throw new Exception("As this form is to comment an existing error, you must create it with the an existing error object");
    }
    parent::__construct($object, $options, $CSRFSecret);
  }
  
  
  /**
   * @see sfForm::configure()
   */
  public function configure(){
    parent::configure();

    // Remove all fields except comment and severity
    foreach ($this as $name => $field){
      if (!in_array($name, array('comment', 'severity'))){
        unset($this[$name]);
      }
    }
    
    
    // Change the widget for comment
    $this->setWidget('comment', new sfWidgetFormTextarea());
    $this->setValidator('comment', new sfValidatorString(array('required' => true)));
    
    // Define the labels
    $this->getWidgetSchema()->setLabels(array(
      'comment'              => 'Comment about error',
      'severity'             => 'Severity of the error'
    ));
    
  }
  
}