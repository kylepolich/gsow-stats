<?php
  $headers = apache_request_headers();
?>
<div>
<p><strong>You are not authorized to access this page.</strong></p>
<p>When reporting this error, please include the following:</p>
<tt>
<ul>
<li>Host: <strong><?php echo($_SERVER['SERVER_NAME']); ?></strong></li>
<li>Requested path: <strong><?php echo($_SERVER['REQUEST_URI']); ?></strong></li>
<li>Identity: <strong><tt><?php echo($_SERVER["REMOTE_USER"]); ?></tt></strong></li>
</ul>
</tt>
</div>
