<?php
namespace framework\db;

class DbModelQuery {

  public static $db = null;
  
  private $_className = null;
  private $_table = "";
  
  private $_query = null;
  
  private $_result = null;
  
  private $_primary = "id";
  
  private $_nullresult;
  
  
  public function __construct($table, $class) {
    $this->_nullresult = new DbModelResult(null, null);
    
    $this->_className = $class;
    $this->_table = $table;
    
    $this->reset();
  }
  
  public function reset() {
    $this->_result = null;
    $this->_query = [
      "command" => "SELECT",
      "columns" => "*",
      "table" => $this->_table,
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
        $this->_query["limit"] = ""; // ensure limit is unset
        $this->execute();
        return $this->_result;
      case "first":
        $r = $this->first(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return $this->_nullresult;
        }
      case "last":
        $r = $this->last(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return $this->_nullresult;
        }
      case "take":
        $r = $this->take(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return $this->_nullresult;
        }
      default:
        throw new DbException("Unknown value :" . $name);
        break;
    }
  }
  public function first($num) {
    $this->_query["command"] = "SELECT";
    $this->_query["limit"] = $num;
    $this->_query["offset"] = "";
    $this->_query["order"] = [ $this->_primary => "ASC" ];
    $this->_query["groupby"] = "";
    $this->_query["having"] = [];
    $this->execute();
    return $this->_result;
  }
  public function last($num) {
    $this->_query["command"] = "SELECT";
    $this->_query["limit"] = $num;
    $this->_query["offset"] = "";
    $this->_query["order"] = [ $this->_primary => "DESC" ];
    $this->_query["groupby"] = "";
    $this->_query["having"] = [];
    $this->execute();
    return $this->_result;
  }
  public function take($num) {
    $this->_query["command"] = "SELECT";
    $this->_query["limit"] = $num;
    $this->_query["offset"] = "";
    $this->_query["order"] = [];
    $this->_query["groupby"] = "";
    $this->_query["having"] = [];
    $this->execute();
    return $this->_result;
  }
  public function create($params) {
    $this->reset();
    $this->_query["command"] = "INSERT INTO";
    $this->_query["columns"] = implode(",", array_keys($params));
    $this->_query["values"] = array_values($params);
    $this->execute("insert");
    return $db->result;
  }
  
  public function update($values) {
    $this->_query["command"] = "UPDATE";
    $this->_query["values"] = $params;
    $this->execute("update");
    return $db->result;
  }
  
  public function destroy() {
    $this->_query["command"] = "DELETE FROM";
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
      $this->_query["where"][] = $cust;
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
          $this->_query["where"][] = $in;
        } else {
          $this->_query["where"][] = ["(" . $k . " = ?) _&&_", $val];
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
      $this->_query["where"][] = $cust;
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
          $this->_query["where"][] = $in;
        } else {
          $this->_query["where"][] = ["(" . $k . " != ?) _&&_", $val];
        }
      }
    }
    return $this;
  }
  
  public function select() {
    if(func_num_args() == 0) {
      $this->_query["columns"] = "*";
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
      $this->_query["columns"] = implode(",", $columns);
    }
    return $this;
  }
  
  public function order() {
    if(func_num_args() == 0) {
      $this->_query["order"] = [ $this->_primary => "ASC" ];
    } else {
      foreach(func_get_args() as $a) {
        if(is_array($a)) {
          foreach($a as $obj => $order) {
            $this->_query["order"][$obj] = $order;
          }
        } else {
          list($obj, $order) = explode(" ", trim($a));
          $this->_query["order"][$obj] = $order;
        }
      }
    }
    return $this;
  }
  
  public function limit($l) {
    $this->_query["limit"] = $l;
    return $this;
  }
  
  public function offset($o) {
    $this->_query["offset"] = $o;
    return $this;
  }
  
  public function group($grp) {
    $this->_query["groupby"] = $grp;
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
    $this->_query["having"] = $hv;
    
  }
  
  private function compileQuery() {
    $q = [
      "query" => "",
      "parameters" => []
    ];
    switch($this->_query["command"]) {
      case "SELECT":
        return $this->compileSelectQuery($q);
      case "DELETE FROM":
        return $this->compileDeleteQuery($q);
      case "INSERT INTO":
        return $this->compileInsertQuery($q);
      case "UPDATE":
        return $this->compileUpdateQuery($q);
      default:
        throw new DbError("Unsupported SQL command!");
    }
  }
  
  private function execute($type="select") {
    switch($type) {
      case "select":        
        $q = $this->compileQuery();
        $stmt = self::$db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->_result = [];
        while($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $this->_result[] = new DbModelResult($res, $this->_className);
        }
        break;
      case "insert":
        $q = $this->compileQuery();
        $stmt = self::$db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->_result = $stmt->rowCount();
        break;
      case "delete":
        $q = $this->compileQuery();
        $stmt = self::$db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->_result = $stmt->rowCount();
        break;
      case "update":
        $q = $this->compileQuery();
        $stmt = self::$db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->_result = $stmt->rowCount();
        break;
    }
  }
  
  // Compile queries
  private function compileSelectQuery($q) {
    $query = ["SELECT"];
    $query[] = $this->_query["columns"];
    $query[] = "FROM";
    $query[] = "`" . $this->_query["table"] . "`";
    if(count($this->_query["where"]) > 0) {
      $last = count($this->_query["where"]) - 1;
      $query[] = "WHERE";
      foreach($this->_query["where"] as $i=>$w) {
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
    if($this->_query["groupby"] != "") {
      $query[] = "GROUP BY";
      $query[] = $this->obj($this->_query["groupby"]);
    }
    if(count($this->_query["having"]) > 0) {
      $query[] = "HAVING";
      $h = $this->_query["having"][0];
      $query[] = $h[0];
      foreach(array_diff($h, array($h[0])) as $p) {
        $q["parameters"][] = $p;
      }
    }
    if(count($this->_query["order"]) > 0) {
      $o = [];
      foreach($this->_query["order"] as $obj=>$ord) {
        $o[] = "ORDER BY ".$this->obj($obj) . " ".$ord;
      }
      $query[] = implode(",", $o);
    }
    if($this->_query["limit"] != "") {
      $query[] = "LIMIT";
      $query[] = $this->_query["limit"];
    }
    if($this->_query["offset"] != "") {
      $query[] = "OFFSET";
      $query[] = $this->_query["offset"];
    }
    $q["query"] = implode(" ", $query);
    return $q;
  }
  
  private function compileDeleteQuery($q) {
    $query = ["DELETE FROM"];
    $query[] = "`" . $this->_query["table"] . "`";
    if(count($this->_query["where"]) > 0) {
      $last = count($this->_query["where"]) - 1;
      $query[] = "WHERE";
      foreach($this->_query["where"] as $i=>$w) {
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
    if(count($this->_query["order"]) > 0) {
      $o = [];
      foreach($this->_query["order"] as $obj=>$ord) {
        $o[] = "ORDER BY ".$this->obj($obj) . " ".$ord;
      }
      $query[] = implode(",", $o);
    }
    if($this->_query["limit"] != "") {
      $query[] = "LIMIT";
      $query[] = $this->_query["limit"];
    }
    if($this->_query["offset"] != "") {
      $query[] = "OFFSET";
      $query[] = $this->_query["offset"];
    }
    $q["query"] = implode(" ", $query);
    return $q;
  }
  
  private function compileUpdateQuery($q) {
    $query = ["UPDATE"];
    $query[] = "`" . $this->_query["table"] . "`";
    $query[] = "SET";
    $set = [];
    foreach($this->_query["values"] as $key => $val) {
      $set[] = $this->obj($key)." = ?";
    }
    $query[] = implode(",", $set);
    if(count($this->_query["where"]) > 0) {
      $last = count($this->_query["where"]) - 1;
      $query[] = "WHERE";
      foreach($this->_query["where"] as $i=>$w) {
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
    if(count($this->_query["order"]) > 0) {
      $o = [];
      foreach($this->_query["order"] as $obj=>$ord) {
        $o[] = "ORDER BY ".$this->obj($obj) . " ".$ord;
      }
      $query[] = implode(",", $o);
    }
    if($this->_query["limit"] != "") {
      $query[] = "LIMIT";
      $query[] = $this->_query["limit"];
    }
    $q["query"] = implode(" ", $query);
    return $q;
  }
  
  private function compileInsertQuery($q) {
    $query = ["INSERT INTO"];
    $query[] = "`" . $this->_query["table"] . "`";
    $query[] = "(" . $this->_query["columns"]. ")";
    $query[] = "VALUES";
    $query[] = $this->vlist($this->_query["values"]);
    $q["query"] = implode(" ", $query);
    $q["parameters"] = $this->_query["values"];
    return $q;
  }
  
  
  // Escaping
  private function obj($value) {
    if(strpos($value, "(") !== false && strpos($value, ")") !== false) {
      return $value;
    } else {
      if(strpos($value, ".") === false) {
        return "`".$this->_query["table"]."`.`" . $value . "`";
      } else {
        return $value;
      }
    }
  }
  
  private function vlist($value) {
    return "(". implode(",", array_fill(0, count($value), "?")) .")";
  }
  
}