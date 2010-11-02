<?php try { ?>
<html>
  <head>
    <link href="/idlErrorManagementPlugin/css/form.css" media="screen" type="text/css" rel="stylesheet">
  </head>
  <body>
    <div class="idl-application-error-content">
      <h1><?php echo __("The application generate an error and have been stopped", null, 'error_management'); ?></h1>
      
      <div class="idl-application-error-detail">
        <h2><?php echo __("Error Message", null, 'error_management'); ?>:</h2>
        <div class="idl-application-error-message">
          <p class="message"><?php echo $form->getObject()->getMessage(); ?></p>
        </div>
      </div>
      
      <div class="idl-application-error-report">
        <h2><?php echo __("Send a comment about this error", null, 'error_management'); ?></h2>
        <p><?php echo __("Thanks to describe in which context this error occurs (what did you doing at this time). This comment could help to fix the issue", null, 'error_management'); ?></p>
        <form action="<?php echo url_for("error_management_post_comment",$form->getObject());?>" method="post">
          <?php echo $form->renderHiddenFields(); ?>
          <BR>

          <?php echo __("Your comment", null, 'error_management'); ?>  
          <div class="idl-application-error-comment">
            <?php echo $form['comment']->renderError(); ?>
            <?php echo $form['comment']; ?>
          </div>
          <div id="severity">
            <?php echo __("Severity of the problem", null, 'error_management'); ?>
            <?php echo $form['severity']; ?>
          </div>
          <div class="idl-application-error-button">
            <?php echo link_to(__("Ignore", null, 'error_management'), sfConfig::get('app_error_management_after_comment_route')); ?>
            <input type="submit" name="next">
          </div>
        </form>
      </div>
    </div>
  </body>
</html>

<?php } 
catch (Exception $e){
  throw new idlErrorManagementException($e);
} ?>