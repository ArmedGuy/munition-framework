<?php
class InstallController extends \framework\base\AppController {
  
  function home($scope) {
    if(isset($_GET['get_settings'])) {
      $this->get_settings();
    }
    self::render($scope, ["template" => "index"]);
  }
  function server_info($scope) {
    self::render(["template" => "serverinfo"]);
  }
  
  private function get_settings() {
    $settings = [
      "fs" => [
        "info" => [
        ],
        "critical" => [
          "write_tmp" => false,
          "write_webroot" => false
        ]
      ],
      "webserver" => [
        "info" => [
          "servertype" => [
          ]
        ],
        "critical" => [
          "rewrite" => false
        ],
        "warn" => [
          "subdir_install" => [
            
          ]
        ]
      ]
    ];
    self::render(["json" => json_encode($settings)]);
    exit;
  }
}