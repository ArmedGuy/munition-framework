<?php
include 'header.php';
?>
<div class="jumbotron">
  <h1>Its running!</h1>
  <p>Now, lets configure the server so that you can start writing and deploying your apps!</p>
</div>
<h1>Settings <small>Click on message to get more information</small></h1>
<div class="row settings-list">
  <div class="col-md-4">
    <h2>Filesystem <small>1/2</small></h2>
    <div id="fs-messages">
      <div class="alert alert-success"><strong>Write Permission:</strong> /tmp/</div>
      <div class="alert alert-danger"><strong>Write Permission:</strong> /tmp/</div>
    </div>
  </div>
  <div class="col-md-4">
    <h2>Webserver <small>0/2</small></h2>
    <div id="web-messages">
      <div class="alert alert-warning"><strong>Installed in subdirectory:</strong> /munition-framework/</div>
    </div>
  </div>
  <div class="col-md-4">
    <h2>PHP</h2>
    <div id="php-messages">
    </div>
  </div>
</div>
<h2>Messages</h2>
<?php
include 'footer.php';
?>