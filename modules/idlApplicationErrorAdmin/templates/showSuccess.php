<h1>Error detail</h1>

<?php echo link_to('Back to list', '@error_management_error_admin'); ?>
<br>
<table border=1>
  <tr>
    <th>Message</th>
    <td><h3><?php echo $error->getMessage(); ?></h3></td>
  </tr>
  <tr>
    <th>Type</th>
    <td><?php echo $error->getType(); ?></td>
  </tr>
  <tr>
    <th>Code</th>
    <td><?php echo $error->getCode(); ?></td>
  </tr>
  <tr>
    <th>Location</th>
    <td>
      <?php echo $error->getShortFilePath(); ?> on line 
      <b><?php echo $error->getLine(); ?></b>
    </td>
  </tr>
  <tr>
    <th>Module/action</th>
    <td>
      <?php echo $error->getModule(), '/', $error->getAction(); ?></b>
    </td>
  </tr>
  <tr>
    <th>URI</th>
    <td>
      <?php echo $error->getUri(); ?></b>
    </td>
  </tr>
  <tr>
    <th>User</th>
    <td>
      <b><?php echo $error->getUser(); ?></b><br>(<?php echo $error->getUserAgent(); ?>)
    </td>
  </tr>
  <tr>
    <th>User comment</th>
    <td>
      <?php if (strlen($error->getComment())>0): ?>
        <b>Severity: <?php echo $error->getSeverity(); ?></b><br>
        <?php echo $error->getComment(); ?>
      <?php else: ?>
        [not commented]
      <?php endif; ?>
    </td>
  </tr>  
  <tr>
    <th>Stack Trace</th>
    <td>
      <pre><?php echo $error->getTrace(); ?></pre>
    </td>
  </tr>
</table>
<br>
<?php echo link_to('Back to list', '@error_management_error_admin');
