<?php
include 'header.php';
?>
<div id="setup">
  <div class="jumbotron">
    <h1>Its running!</h1>
    <p>Now, lets configure the server so that you can start writing and deploying your apps!</p>
  </div>
  <h1>Issues <small>Refresh this page when you have fixed them.</small></h1>
  <div id="issues-list">
  </div>
</div>
<script type="text/javascript">
function getIssues() {
  $("#issues-list").html("");
  $.getJSON("?get_issues", function(settings) {
    if(settings.length == 0) {
      $("#issues-list").html("<h2>No detected issues, your installation is ready for action!</h2>");
      $("#setup").fadeOut(1000, function() {
        
      });
    } else {
      for(var s in settings) {
        $("#issues-list").append("<div class='alert alert-" + settings[s].type + "'><strong>" + settings[s].issue + "</strong> " + settings[s].description + "</div>");
      }
    }
  });
}
setTimeout(getIssues, 500);
</script>
<div id="done-install" style="display:none;">
</div>
<?php
include 'footer.php';
?>