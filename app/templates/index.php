<?php
include 'header.php';
?>
<div id="setup">
  <div class="jumbotron">
    <h1>Its running!</h1>
    <p>Now, lets configure the server so that you can start writing and deploying your apps!</p>
  </div>
  
  <h3>Easy configuration</h3>
  <p>
    Munition automatically checks your system to see if it is properly configured, and that it meets all the requirements.<br/>
    It will show you a list of issues and notices below. You need to fix all the critical ones before you can continue.
  </p>
  <input type="button" class="btn btn-primary" value="Check System" id="start-setup" />
  <input type="button" class="btn btn-success" value="No critical issues, click to finish the installation" style="display: none;" id="continue-btn" />
  <br/><br/>
  <h1 id="head-text"></h1>
  <div id="issues-list">
  </div>
</div>
<script type="text/javascript">
function getIssues() {
  $("#head-text").html("Loading");
  $.getJSON("?get_issues", function(settings) {
    $("#issues-list").html("");
    if(settings.length == 0) {
      $("#setup").fadeOut(1000, function() {
        $("#done-install").fadeIn(1000);
      });
    } else {
      var cr = 0;
      for(var s in settings) {
        if(settings[s].type == "danger") cr++;
        $("#issues-list").append("<div class='alert alert-" + settings[s].type + "'><strong>" + settings[s].issue + "</strong> " + settings[s].description + "</div>");
      }
      if(cr == 0) {
        $("#head-text").html("Other information");
        $("#continue-btn").show();
      } else {
        $("#head-text").html("Issues <input type='button' class='btn btn-info' value='Click to re-check' onclick='getIssues();'/>");
      }
    }
  });
}
$("#start-setup").click(function() {
  $(this).hide();
  getIssues();
});
$("#continue-btn").click(function() {
  $("#setup").fadeOut(1000, function() {
    $("#done-install").fadeIn(1000);
  });
});
</script>
<div id="done-install" style="display:none;">
  <h1>Your installation is ready for action!</h1>
  <p>Webserver is properly configured and all requirements have been met!</p>
  <hr/>
  <h2 class="text-center">What to do next?</h2>
  <div class="row">
    <div class="col-md-5">
      <h3>Clear out the default App</h3>
      <p>The default App is located at <code><?=MUNITION_ROOT."/app"; ?></code></p>
      <p>Simply clear all files from <code>app/public</code> <code>app/templates</code> and <code>app/controllers</code>.</p>
      <p>Then, reset the default AppRouter at <code>config/routes.php</code> by removing all <code>$this->...</code> calls.</p>
    </div>
    <div class="col-md-4">
      <h3>Follow a guide</h3>
      <p>There are many guides available on the official guide forums.</p>
      <p>If you are unsure on how to proceed after the installation, the <a href="http://forum.pie-studios.com/category/munition/howto">howto category</a> is a vital resource for learning how to build stuff with Munition.</p>
    </div>
    <div class="col-md-3">
      <h5>Useful Resources</h5>
      <ul class="nav">
        <li><a href="#">Documentation</a></li>
        <li><a href="http://forum.pie-studios.com/category/munition">Munition Forums</a></li>
      </ul>
    </div>
  </div>
</div>
<?php
include 'footer.php';
?>