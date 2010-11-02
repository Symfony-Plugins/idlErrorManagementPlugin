<b>
  <?php echo link_to($application_error->getMessage(), 'error_management_error_admin_edit', $application_error) ?>
</b>
<br>
<i>
  <?php echo $application_error->getShortFilePath(); ?> on line 
  <b><?php echo $application_error->getLine(); ?></b>
</i>
  