<?php
class InstallController extends \framework\base\AppController {
  
  function __construct() {
    $this->before_action([$this, "filter_all_actions"]);
    
    
    $this->before_action([$this, "filter_some_actions"], "test_filters1");
    
    $this->before_action([$this, "filter_allbutsome_actions"],
    ["not" => [
      "test_filters2",
      "home",
      "verify_rewrite"
    ]]);
    
  }
  function home($scope) {
    if(isset($_GET['get_issues'])) {
      $this->get_issues();
    }
    self::render($scope, ["template" => "index"]);
  }
  function verify_rewrite() {
    self::render(["nothing" => true]);
  }
  
  // Test actions
  function test_filters1($scope) {
    self::render([403, "nothing" => true]);
  }
  function test_filters2($scope) {
    self::render([403, "nothing" => true]);
  }
  
  private function get_issues() {
    $issues = [];
    
    $this->check_write_perms($issues);
    $this->check_pdo($issues);
    $this->check_rewrite_rules($issues);
    
    if(MUNITION_WEBPATH != "/") {
      $issues[] = [
        "type" => "warning",
        "issue" => "Installed in subdirectory",
        "description" => "Munition is currently installed in the subdirectory <code>".MUNITION_WEBPATH."</code>.<br/> If this is how you intend to run Munition, then ignore this. Otherwise please move the Munition Framework to your webroot at <code>{$_SERVER['DOCUMENT_ROOT']}</code>"
      ];
    }
    
    self::render(["json" => json_encode($issues)]);
    exit;
  }
  
  private function try_own_url($path) {
    $url = "http" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "" && $_SERVER['HTTPS'] != "off") ? "s" : "");
    $url .="://" . $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'] ."/". $path ."?verify_install=true";
    @file_get_contents($url);
    list($http, $code, $status) = explode(" ", $http_response_header[0]);
    return $code;
  }
  
  private function check_rewrite_rules(&$issues) {
    $crouter = $this->try_own_url(MUNITION_WEBPATH . "/verify_rewrite");
    $publicdir = $this->try_own_url(MUNITION_WEBPATH . "/app/public/index.html");
    $privfolder = $this->try_own_url(MUNITION_WEBPATH . "/framework/munition.php");
    
    if($crouter != "200" || $publicdir != "200" || $privfolder == "422") {
      $desc = "";
      switch(MUNITION_WEBSERVER) {
        case "nginx":
          $desc = "<h4>Configuring Rewrite Rules on Nginx</h4><p>Place the following Nginx Rewrite Rules in your webserver(or virtualhost) configuration, then restart/reload the nginx server.<br/><pre>rewrite ^(".MUNITION_WEBPATH."app/public/.*)$ $1 last;\nrewrite ^.*$ index.php last;</pre>";
          break;
        case "apache":
          $desc = "<h4>Configuring Rewrite Rules on Apache</h4><p>Place the following Apache Rewrite Rules in your webserver(or virtualhost) or main .htaccess configuration, then restart/reload the Apache server.<br/><pre>Options +FollowSymlinks\nRewriteEngine On\nRewriteBase ".MUNITION_WEBPATH."\nRewriteRule ^".substr(MUNITION_WEBPATH, 1)."index\\.php$ - [L] <br/>RewriteCond %{REQUEST_URI} !app/public/.*$\nRewriteRule . ".MUNITION_WEBPATH."index.php [L]</pre>";
          break;
        case "lighttpd":
          $desc = "TODO";
          break;
        default:
          $desc = "<h4>Configuring Rewrite Rules for your server</h4><p>We could not automatically detect what webserver you are running, but hopefully it supports rewriting! The rules needed for Munition are described as follows: <br/>Redirect any URL except <code>".MUNITION_WEBPATH."app/public/*</code> to <code>".MUNITION_WEBPATH."index.php</code>";
          break;
      }
      
      $issues[] = [
        "type" => "danger",
        "issue" => "Rewrite Rules not configured",
        "description" => "Rewrite Rules are not properly configured. Without these the App cannot handle incoming requests via the AppRouter.<br><br/>" . $desc
      ];
    }
  }
  
  private function check_pdo(&$issues) {
    if(!class_exists('PDO')) {
      $issues[] = [
        "type" => "danger",
        "issue" => "Missing PDO Library",
        "description" => "PDO is the latest Database library for PHP, and the most efficient one as well. Munition requires it for its <code>DbModel</code>.<br/>Please download and install it.<br/><br/>PS. You should NOT use anything but PDO for connecting to databases."
      ];
    }
  }
  
  private function check_write_perms(&$issues) {
    if(!is_writable(MUNITION_ROOT . "/app/public/")) {
      $issues[] = [
        "type" => "danger",
        "issue" => "Missing File Permissions",
        "description" => "Munition was unable to access the directory <code>".MUNITION_ROOT."/app/public/</code><br/>Please check that the directory is writable."
      ];
    }
  }
  
  
  protected function filter_all_actions($scope) {
    return $scope;
  }
  protected function filter_some_actions($scope) {
    self::render([422, "nothing" => true]);
  }
  protected function filter_allbutsome_actions($scope) {
    self::render([422, "nothing" => true]);
  }
}