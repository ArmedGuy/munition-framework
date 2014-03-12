<?php
class InstallController extends \framework\base\AppController {
  
  function home($scope) {
    if(isset($_GET['get_issues'])) {
      $this->get_issues();
    }
    self::render($scope, ["template" => "index"]);
  }
  function verify_rewrite() {
    self::render(["nothing" => true]);
  }
  function server_info($scope) {
    self::render(["template" => "serverinfo"]);
  }
  
  private function get_issues() {
    $issues = [];
    
    
    
    if(!is_writable(MUNITION_ROOT . "/app/public/")) {
      $issues[] = [
        "type" => "danger",
        "issue" => "Missing File Permissions",
        "description" => "Munition was unable to access the directory <code>".MUNITION_ROOT."/app/public/</code><br/>Please check that the directory is writable."
      ];
    }
    if(MUNITION_WEBPATH != "/") {
      $issues[] = [
        "type" => "warning",
        "issue" => "Installed in subdirectory",
        "description" => "Munition is currently installed in the subdirectory <code>".MUNITION_WEBPATH."</code>.<br/> If this is how you intend to run Munition, then ignore this. Otherwise please move the Munition Framework to your webroot at <code>{$_SERVER['DOCUMENT_ROOT']}</code>"
      ];
    }
    
    $crouter = $this->try_own_url(MUNITION_WEBPATH . "/verify_rewrite");
    $publicdir = $this->try_own_url(MUNITION_WEBPATH . "/app/public/css/style.css");
    $privfolder = $this->try_own_url(MUNITION_WEBPATH . "/framework/munition.php");
    if($crouter != "200" || $publicdir != "200" || $privfolder == "422") {
      $desc = "";
      switch(MUNITION_WEBSERVER) {
        case "nginx":
          $desc = "<h4>Configuring Rewrite Rules on Nginx</h4><p>Place the following Nginx Rewrite Rules in your webserver(or virtualhost) configuration<br/><pre>rewrite ^(".MUNITION_WEBPATH."app/public/.*)$ $1 last;\nrewrite ^.*$ index.php last;</pre>";
          break;
        case "apache":
          $desc = "<h4>Configuring Rewrite Rules on Apache</h4><p>Place the following Apache Rewrite Rules in your webserver(or virtualhost) or main .htaccess configuration<br/><pre>Options +FollowSymlinks\nRewriteEngine On\nRewriteBase ".MUNITION_WEBPATH."\nRewriteRule ^".substr(MUNITION_WEBPATH, 1)."index\\.php$ - [L] <br/>RewriteCond %{REQUEST_URI} !app/public/.*$\nRewriteRule . ".MUNITION_WEBPATH."index.php [L]</pre>";
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
    
    self::render(["json" => json_encode($issues)]);
    exit;
  }
  
  private function try_own_url($path) {
    $url = "http" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "" && $_SERVER['HTTPS'] != "off") ? "s" : "");
    $url .="://" . $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'] ."/". $path;
    @file_get_contents($url);
    list($http, $code, $status) = explode(" ", $http_response_header[0]);
    return $code;
  }
}