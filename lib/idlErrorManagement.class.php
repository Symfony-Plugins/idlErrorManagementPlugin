<?php
/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * idlErrorManagement, Main class providing tools to manage error
 *
 * @package    idlErrorManagementPlugin
 * @author     David Jeanmonod  <david AT idael.ch>
 */
class idlErrorManagement {
  
  const LAST_ERROR_SESSION_KEY = "idl_application_error_last_id";
  const LAST_REDIRECT_TS_SESSION_KEY = "idl_application_error_last_redirect_ts";
  
  
  /**
   * Function that is call by symfony when an error occurs
   *  The call have been registred to the event dispatcher in the /config/config.php file of this plugin
   * @param $event  The symfony event, that provide acces to the error detail
   */
  public static function processApplicationError(sfEvent $event){
    
    // Get the exception, if it's comming from this plugin, we should do nothing, as this risk to create
    //  an infinite loop of error creation & redirection 
    $exception = $event->getSubject();
    if ($exception instanceof idlErrorManagementException || strstr($exception->getFile(),'idlErrorManagementPlugin') ){
      self::tryToLogError("Bug in the plugin idlErrorManagement, please contact devlopper. Detail: ".$e->getMessage());
      return false;
    }
    
    try {
    
      // Create the error object and fill-in informations
      $error = new ApplicationError();
      $error->updateWithException($exception);
      $error->updateWithContext();
      $error->updateWithUser();

      // Save to DB if require
      $saveSuccess = false;
      if (sfConfig::get('app_error_management_record_exception_to_db', false)){
        $saveSuccess = $error->trySave();
      }
      
      // If requested, send the error by email
      if ( sfConfig::get('app_error_management_send_mail_on_error', false)){
        self::sendErrorByEmail($error);
      }
      
      // Store the error id in the session for user comment
      if ( $saveSuccess && sfConfig::get('app_error_management_ask_user_to_comment', false)){
        self::registerErrorForComment($error);
        return self::redirectToCommentFormIfRequire();
      }
      
      // We return false, so then symfony will play the normal error handling
      return false;
    
    }
    
    // On any error, we rethrow with the specifc idlApplicationException as we want to prevent cycle in error management
    catch (Exception $e){
      self::tryToLogError("Exception occurs while processing processApplicationError(). Detail: ".$e->getMessage());
    }
  }

  /**
   * Process the redirect to the comment form if require
   * @return boolean    If the redirect as been done
   */
  public static function redirectToCommentFormIfRequire(){
      
    //  In the case of an ajax call, it's the browser to will have to do manulaly the redirect 
    //   By making a redirect to the route provided by the function idlErrorManagement::getRouteToCommentLastError()
    if ( sfContext::getInstance()->getRequest()->isXmlHttpRequest() ){
      return false;
    }
      
    // Redirect to the form to comment if require
    if ( sfConfig::get('app_error_management_ask_user_to_comment', false) ) {
      
      // Check is a redirecting cycle is currently running
      if (self::isRedirectingCycleDetected()){
        return false;
      }
      
      // Before redirecting, we write down the timestamp in session. See self::isRedirectingCycleDetected() for info
      sfContext::getInstance()->getStorage()->write(self::LAST_REDIRECT_TS_SESSION_KEY, time());
      sfContext::getInstance()->getController()->redirect(self::getRouteToCommentLastError());
      return true; // This return true is going to stop the symfony exception handling as we have manage a redirect
    }
    else {      
      return false;
    }    
  }
  
  
  /**
   * Return true if a cycle is currently detected
   */
  public static function isRedirectingCycleDetected(){
    $lastTS = sfContext::getInstance()->getStorage()->read(self::LAST_REDIRECT_TS_SESSION_KEY, 0);
    $interval = time() - $lastTS;
    return $interval < 3; // Less than 3 second, we can guess that's a cycle
  }
 
  
  /**
   * Register the provided error in session to allow redirect on the comment form
   * @param ApplicationError $error
   */
  public static function registerErrorForComment($error){
    sfContext::getInstance()->getStorage()->write(self::LAST_ERROR_SESSION_KEY, $error->getId());
  } 
 
 
  /**
   * Send a description of the exception by email
   * @param ApplicationError $error
   */
  public static function sendErrorByEmail($error){
    
    try {
      // Get env name
      $env = 'n/a';
      if ($conf = sfContext::hasInstance()){
        $env = sfContext::getInstance()->getConfiguration()->getEnvironment(); 
      }
      
      // Send the mail
      sfContext::getInstance()->getMailer()->composeAndSend(
        sfConfig::get('app_error_management_email_from'),
        sfConfig::get('app_error_management_email_to'),
        "ERROR on ".$_SERVER['HTTP_HOST']."[$env] - ".$error->getMessage(),
        $error->getFormattedDescription()
      );
    }
    catch(Exception $e){
      self::tryToLogError("Exception occurs while processing sendErrorByEmail(). Detail: ".$e->getMessage());
    }
    
  }
  
  
  /**
   * Return a route that will allow the user t5o comment the error
   */
  public static function getRouteToCommentLastError(){
    return '@error_management_comment_last';
  }
  
  
  /**
   * After calling this function, the plugin should also work with the PHP error 
   */
  public static function registerPhpErrorListener(){

    // Create a php script that will be call on PHP error
    $file = self::getPathToPhpErrorHandlingScript();
    if ( ! file_exists($file) ){
      self::createPhpErrorHandlingScript();
    }
    
    // Register the function that is in the temporary file, to the PHP Engine
    include_once($file);
    $env = sfConfig::get('sf_environment');
    register_shutdown_function("idlPhpErrorRecorderForEnv$env");
  }
  
  
  /**
   * Return the path to the error handling file
   * @return string   Path
   */
  public static function getPathToPhpErrorHandlingScript(){
    $cacheDir = sfConfig::get('sf_app_cache_dir', sfConfig::get('sf_cache_dir')).DIRECTORY_SEPARATOR."idlErrorManagementPlugin";
    return $cacheDir.DIRECTORY_SEPARATOR."idlPhpErrorRecorder.php";
  }
  
  
  /**
   * Create a script to handle PHP errors, as at this moment symfony will be dead, the script 
   * should be working standalone, that's why the database access is hard coded inside
   */
  public static function createPhpErrorHandlingScript(){
    
    // Create the folder if need
    $file = self::getPathToPhpErrorHandlingScript();
    $dir = dirname($file);
    if( !is_dir($dir) ){
      $umask = umask(0000);
      mkdir($dir, 0777, true);
      umask($umask);
      if (!is_dir($dir) ){
        throw new idlErrorManagementException("Impossible to create the directory $path, please check the filesystem permissions.");
      }
    }
      
    // Retrived parameters for script generation
    $env = sfConfig::get('sf_environment');
    $sessionKey = self::LAST_ERROR_SESSION_KEY;
    $doctrinePath = sfConfig::get('sf_symfony_lib_dir').'/plugins/sfDoctrinePlugin/lib/vendor/doctrine/Doctrine.php';
    $dbConfig = sfYaml::load(sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR.'databases.yml');
    $dbConfig = $dbConfig[$env]['doctrine']['param'];
    $dsn = $dbConfig["dsn"];
    $user = $dbConfig['username'];
    $password = isset($dbConfig['password']) ? "'".$dbConfig['password']."'" : 'null';    
    
    // Prepare the file by injecting the following code
    $code = <<<CODE
<?php
  function idlPhpErrorRecorderForEnv$env(){
    if (\$error = error_get_last()){
      
      // Don't save plugin error to prevente infinite loop if a redirect is manage in JS client side
      if (strstr(\$error['file'],'idlErrorManagementPlugin') ){
        return;
      }
      
      // Connect to the database using doctrine abstraction layer
      require_once('$doctrinePath');
      spl_autoload_register(array('Doctrine', 'autoload'));
      \$pdo = new PDO('$dsn', '$user', $password);
      \$conn = Doctrine_Manager::connection(\$pdo);
      \$conn->execute(
        "INSERT INTO `application_error` (`type`, `file`, `line`, `code`,  `message`, `user_agent`, `uri`, `created_at`) 
        VALUES (
          'PHP error',
          '".\$error['file']."',
          '".\$error['line']."',
          '".\$error['type']."',
          '".\$error['message']."',
          '".(isset(\$_SERVER['HTTP_USER_AGENT'])?\$_SERVER['HTTP_USER_AGENT']:'')."',
          '".(isset(\$_SERVER['REQUEST_URI'])?\$_SERVER['REQUEST_URI']:'')."',
          '".date("Y-m-d H:i:s")."'
        )"
      );
      
      // Place the id to session
      \$id = \$conn->lastInsertId();
      if (isset(\$id) && \$id > 0) {
        \$_SESSION['$sessionKey'] = \$id;
      }
      
      // Redirect to comment form
      // TODO, but it's difficult as we don't now if header are already send
    }
  }
?>
CODE;
    
    // Create the file and validate
    file_put_contents($file, $code);
    if ( ! file_exists($file) ){
      throw new idlErrorManagementException("Impossible to create the script to catch the PHP error. Please check the permissions to create $file");
    }    
  }
  
  public static function tryToLogError($msg){
    if (sfContext::hasInstance()){
      sfContext::getInstance()->getLogger()->err("idlErrorManagementException error: ".$msg);
    }
  }
}