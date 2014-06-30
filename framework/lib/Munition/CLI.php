<?php
namespace Munition;
class CLI {
    private $interrupt = false;
    private $buffer = [];

    public $prompt = "Munition> ";

    private $function_hooks = [];

    public function __construct() {
        $this->registerHook("cls", function() {
            $this->buffer = [];
        });
    }
    public function readline() {
        $line = null;
        if (PHP_OS == 'WINNT') {
            echo $this->prompt;
            $line = stream_get_line(STDIN, 1024, PHP_EOL);
        } else {
            $line = readline($this->prompt);
        }
        return $line;
    }
    public function run() {
        while(!$this->interrupt) {
            $line = $this->readline();
            // specific command
            if(strpos($line, ":") === 0) {
                $cmd = null;
                if(strpos($line, " ") !== false) {
                    $args = explode(" ", $line);
                    $cmd = $args[0];
                } else {
                    $cmd = $line;
                }
                if(isset($this->function_hooks[$cmd])) {
                    $this->function_hooks[$cmd]($line);
                }
            } else {
                try {
                    $line = trim($line);
                    if(substr($line, -1) !== ";") {
                        $line .= ";";
                    }
                    array_push($this->buffer, "return ".$line);
                    $res = eval(implode("\n", $this->buffer));
                    array_pop($this->buffer);

                    echo ">>> " . $res . "\n";
                }
                catch(\Exception $e) {
                    echo ">>> Exception: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    public function registerHook($name, $callback) {
        $this->function_hooks[$name] = $callback;
    }


}