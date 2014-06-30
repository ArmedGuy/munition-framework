<?php
namespace Munition;
class CLI {
    private $interrupt = false;
    private $buffer = [];

    public $prompt = "Munition> ";

    private $function_hooks = [];
    private function callHook($hook, $data) {
        if(isset($this->function_hooks[$hook])) {
            call_user_func($this->function_hooks[$hook], $data);
        }
        else
        {
            $this->writeLine("No hook found matching $hook");
        }
    }

    public function __construct() {
        $this->registerHook("cls", function() {
            $this->buffer = [];
        });
    }
    public function readLine() {
        $line = null;
        if (PHP_OS == 'WINNT') {
            echo $this->prompt;
            $line = stream_get_line(STDIN, 1024, PHP_EOL);
        } else {
            $line = readline($this->prompt);
        }
        return $line;
    }

    public function write($data) {
        echo $data;
    }
    public function writeLine($data) {
        $this->write(">>> " . $data . "\n");
    }
    public function run() {
        if($_SERVER["argc"] > 1) {
            $cmd = $_SERVER["argv"][1];
            $this->callHook($cmd, array_slice($_SERVER["argv"], 2));
        }

        while(!$this->interrupt) {
            $line = $this->readLine();
            // specific command
            if(strpos($line, ":") === 0) {
                $cmd = null;
                if(strpos($line, " ") !== false) {
                    $args = explode(" ", $line);
                    $cmd = $args[0];
                } else {
                    $cmd = $line;
                }
                $this->callHook($cmd, $line);
            } else {
                try {
                    $line = trim($line);
                    if(substr($line, -1) !== ";") {
                        $line .= ";";
                    }
                    array_push($this->buffer, "return ".$line);
                    $res = eval(implode("\n", $this->buffer));
                    array_pop($this->buffer);

                    $this->writeLine($res);
                }
                catch(\Exception $e) {
                    $this->writeLine("Exception: " . $e->getMessage());
                }
            }
        }
    }
    public function registerHook($name, $callback) {
        $this->function_hooks[$name] = $callback;
    }


}