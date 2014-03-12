<?php
namespace framework\base;

class DbModelQuery {

  public static $db = null;
  
  private $className = null;
  private $table = "";
  
  public $query = null;
  
  private $result = null;
  
  private $primary = "id";
  
  private $nullresult;
  
  
  public function __construct($table, $class) {
    $this->nullresult = new DbModelResult(null, null);
    
    $this->className = $class;
    $this->table = $table;
    
    $this->reset();
  }
  
  private function reset() {
    $this->result = null;
    $this->query = [
      "command" => "SELECT",
      "columns" => "*",
      "table" => $this->table,
      "where" => [
      ],
      "order" => [
      ],
      "values" => [
      ],
      "limit" => "",
      "offset" => "",
      "groupby" => "",
      "having" => ""
    ];
  }
  public function __get($name) {
    switch($name) {
      case "all": // execute query and return results
        $this->query["limit"] = ""; // ensure limit is unset
        $this->execute();
        return $this->result;
      case "first":
        $r = $this->first(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return $this->nullresult;
        }
      case "last":
        $r = $this->last(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return $this->nullresult;
        }
      case "take":
        $r = $this->take(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return $this->nullresult;
        }
      default:
        throw new MunitionDbException("Unknown value :" . $name);
        break;
    }
  }
  public function first($num) {
    $this->query["command"] = "SELECT";
    $this->query["limit"] = $num;
    $this->query["offset"] = "";
    $this->query["order"] = [ $this->primary => "ASC" ];
    $this->query["groupby"] = "";
    $this->query["having"] = [];
    $this->execute();
    return $this->result;
  }
  public function last($num) {
    $this->query["command"] = "SELECT";
    $this->query["limit"] = $num;
    $this->query["offset"] = "";
    $this->query["order"] = [ $this->primary => "DESC" ];
    $this->query["groupby"] = "";
    $this->query["having"] = [];
    $this->execute();
    return $this->result;
  }
  public function take($num) {
    $this->query["command"] = "SELECT";
    $this->query["limit"] = $num;
    $this->query["offset"] = "";
    $this->query["order"] = [];
    $this->query["groupby"] = "";
    $this->query["having"] = [];
    $this->execute();
    return $this->result;
  }
  public function create($params) {
    $this->reset();
    $this->query["command"] = "INSERT INTO";
    $this->query["columns"] = implode(",", array_keys($params));
    $this->query["values"] = array_values($params);
    $this->execute("insert");
    return $db->result;
  }
  public function destroy() {
    $this->query["command"] = "DELETE FROM";
    $this->execute("delete");
    return $db->result;
  }
  
  public function where() {
    if(func_num_args() == 0) return $this;
    
    if(is_string(func_get_arg(0))) {
      $cust = ["(" . func_get_arg(0) . ") _&&_"];
      $params = func_get_args();
      unset($params[0]);
      foreach($params as $p) {
        $cust[] = $p;
      }
      $this->query["where"][] = $cust;
      return;
    }
    if(is_array(func_get_arg(0))) {
      foreach(func_get_arg(0) as $key=>$val) {
        $k = $this->obj($key);
        if(is_array($val)) { // USE IN
          $in = ["(" . $k . " IN ". $this->vlist($val) .") _&&_"];
          foreach($val as $v) {
            $in[] = $v;
          }
          $this->query["where"][] = $in;
        } else {
          $this->query["where"][] = ["(" . $k . " = ?) _&&_", $val];
        }
      }
    }
    return $this;
  }
  
  public function where_not() {
    if(func_num_args() == 0) return $this;
    
    if(is_string(func_get_arg(0))) {
      $cust = ["NOT (" . func_get_arg(0) . ") _&&_"];
      $params = func_get_args();
      unset($params[0]);
      foreach($params as $p) {
        $cust[] = $p;
      }
      $this->query["where"][] = $cust;
      return;
    }
    if(is_array(func_get_arg(0))) {
      foreach(func_get_arg(0) as $key=>$val) {
        $k = $this->obj($key);
        if(is_array($val)) { 
          $in = ["(" . $k . " NOT IN ". $this->vlist($val) .") _&&_"];
          foreach($val as $v) {
            $in[] = $v;
          }
          $this->query["where"][] = $in;
        } else {
          $this->query["where"][] = ["(" . $k . " != ?) _&&_", $val];
        }
      }
    }
    return $this;
  }
  
  public function select() {
    if(func_num_args() == 0) {
      $this->query["columns"] = "*";
    } else {
      $columns = [];
      foreach(func_get_args() as $a) {
        if(is_array($a)) {
          foreach($a as $c=>$as) {
            if(is_numeric($c)) {
              $columns[] = $this->obj($as);
            } else {
              $columns[] = $this->obj($c) . " AS " . $this->obj($as);
            }
          }
        } else {
          if($a == "*") {
            $columns = ["*"];
            break;
          } else {
            $columns[] = $this->obj($a);
          }
        }
      }
      $this->query["columns"] = implode(",", $columns);
    }
    return $this;
  }
  
  public function order() {
    if(func_num_args() == 0) {
      $this->query["order"] = [ $this->primary => "ASC" ];
    } else {
      foreach(func_get_args() as $a) {
        if(is_array($a)) {
          foreach($a as $obj => $order) {
            $this->query["order"][$obj] = $order;
          }
        } else {
          list($obj, $order) = explode(" ", trim($a));
          $this->query["order"][$obj] = $order;
        }
      }
    }
    return $this;
  }
  
  public function limit($l) {
    $this->query["limit"] = $l;
    return $this;
  }
  
  public function offset($o) {
    $this->query["offset"] = $o;
    return $this;
  }
  
  public function group($grp) {
    $this->query["groupby"] = $grp;
    return $this;
  }
  
  public function having() {
    if(func_num_args() == 0) return $this;
    $h = func_get_args();
    $hv = [$h[0]];
    unset($h[0]);
    foreach($h as $v) {
      $hv[] = $v;
    }
    $this->query["having"] = $hv;
    
  }
  
  private function compileQuery() {
    $q = [
      "query" => "",
      "parameters" => []
    ];
    switch($this->query["command"]) {
      case "SELECT":
        $query = ["SELECT"];
        $query[] = $this->query["columns"];
        $query[] = "FROM";
        $query[] = "`" . $this->query["table"] . "`";
        if(count($this->query["where"]) > 0) {
          $last = count($this->query["where"]) - 1;
          $query[] = "WHERE";
          foreach($this->query["where"] as $i=>$w) {
            if($i == $last) {
              $query[] = str_replace("_&&_", "", $w[0]);
              foreach(array_diff($w, array($w[0])) as $p) {
                $q["parameters"][] = $p;
              }
            } else {
              $query[] = str_replace("_&&_", "AND", $w[0]);
              foreach(array_diff($w, array($w[0])) as $p) {
                $q["parameters"][] = $p;
              }
            }
          }
        }
        if($this->query["groupby"] != "") {
          $query[] = "GROUP BY";
          $query[] = $this->obj($this->query["groupby"]);
        }
        if(count($this->query["having"]) > 0) {
          $query[] = "HAVING";
          $h = $this->query["having"][0];
          $query[] = $h[0];
          foreach(array_diff($h, array($h[0])) as $p) {
            $q["parameters"][] = $p;
          }
        }
        if(count($this->query["order"]) > 0) {
          $o = [];
          foreach($this->query["order"] as $obj=>$ord) {
            $o[] = "ORDER BY ".$this->obj($obj) . " ".$ord;
          }
          $query[] = implode(",", $o);
        }
        if($this->query["limit"] != "") {
          $query[] = "LIMIT";
          $query[] = $this->query["limit"];
        }
        if($this->query["offset"] != "") {
          $query[] = "OFFSET";
          $query[] = $this->query["offset"];
        }
        $q["query"] = implode(" ", $query);
        return $q;
      case "DELETE FROM":
        $query = ["DELETE FROM"];
        $query[] = $this->query["table"];
        if(count($this->query["where"]) > 0) {
          $last = count($this->query["where"]) - 1;
          $query[] = "WHERE";
          foreach($this->query["where"] as $i=>$w) {
            if($i == $last) {
              $query[] = str_replace("_&&_", "", $w[0]);
              foreach(array_diff($w, array($w[0])) as $p) {
                $q["parameters"][] = $p;
              }
            } else {
              $query[] = str_replace("_&&_", "AND", $w[0]);
              foreach(array_diff($w, array($w[0])) as $p) {
                $q["parameters"][] = $p;
              }
            }
          }
        }
        if(count($this->query["order"]) > 0) {
          $o = [];
          foreach($this->query["order"] as $obj=>$ord) {
            $o[] = "ORDER BY ".$this->obj($obj) . " ".$ord;
          }
          $query[] = implode(",", $o);
        }
        if($this->query["limit"] != "") {
          $query[] = "LIMIT";
          $query[] = $this->query["limit"];
        }
        if($this->query["offset"] != "") {
          $query[] = "OFFSET";
          $query[] = $this->query["offset"];
        }
        $q["query"] = implode(" ", $query);
        return $q;
      case "INSERT INTO":
        $query = ["INSERT INTO"];
        $query[] = $this->query["table"];
        $query[] = "(" . $this->query["columns"]. ")";
        $query[] = "VALUES";
        $query[] = $this->vlist($this->query["values"]);
        $q["query"] = implode(" ", $query);
        $q["parameters"] = $this->query["values"];
        return $q;
    }
  }
  
  private function execute($type="select") {
    switch($type) {
      case "select":        
        $q = $this->compileQuery();
        print_r($q);
        die();
        $stmt = self::$db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->result = [];
        while($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $this->result[] = new DbModelResult($res, $this->className);
        }
        break;
      case "insert":
        $q = $this->compileQuery();
        print_r($q);
        die();
        $stmt = self::$db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->result = $stmt->rowCount();
        break;
      case "delete":
        $q = $this->compileQuery();
        print_r($q);
        die();
        $stmt = self::$db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->result = $stmt->rowCount();
        break;
    }
  }
  
  private function obj($value) {
    if(strpos($value, "(") !== false && strpos($value, ")") !== false) {
      return $value;
    } else {
      if(strpos($value, ".") === false) {
        return "`".$this->query["table"]."`.`" . $value . "`";
      } else {
        return $value;
      }
    }
  }
  
  private function vlist($value) {
    return "(". implode(",", array_fill(0, count($value), "?")) .")";
  }
  
}