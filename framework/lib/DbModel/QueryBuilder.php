<?php
namespace DbModel;

class QueryBuilder extends \stdClass{

  private $_db = null;
  
  private $_className = null;
  private $_table = "";
  
  private $_query = null;
  
  private $_result = null;
  
  private $_primary = "id";
  
  public function __construct($db, $table, $class, $primary = "id") {
    if($db == null) {
      throw new DbException('No database connection set! Set \\DbModel\\Base::$default_db before using DbModel');
    }

    $this->_db = $db;
    $this->_className = $class;
    $this->_table = $table;
    $this->_primary = $primary;
    
    $this->reset();
  }
  
  public function reset() {
    $this->_result = null;
    $this->_query = [
      "command" => "SELECT",
      "columns" => "*",
      "table" => $this->_table,
      "joins" => [
      ],
      "where" => [
      ],
      "order" => [
      ],
      "values" => [
      ],
      "limit" => "",
      "offset" => "",
      "groupby" => "",
      "having" => []
    ];
  }
  public function __get($name) {
    switch($name) {
      case "all":
        return $this->all();
      case "first":
        $r = $this->first(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return QueryRow::$nullRow;
        }
      case "last":
        $r = $this->last(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return QueryRow::$nullRow;
        }
      case "take":
        $r = $this->take(1);
        if(count($r) == 1) {
          return $r[0];
        } else {
          return QueryRow::$nullRow;
        }
      default:
        throw new DbException("Unknown value:" . $name);
        break;
    }
  }
  public function all() {
    $this->_execute();
    return $this->_result;
  }
  public function first($num = 1) {
    $this->_query["command"] = "SELECT";
    $this->_query["limit"] = $num;
    if(count($this->_query["order"]) == 0)
      $this->_query["order"] = [ $this->_primary => "ASC" ];
    else
      $this->_query["order"] = array_merge($this->_query["order"], [ $this->_primary => "ASC" ]);
    $this->_execute();
    return $this->_result;
  }
  public function last($num = 1) {
    $this->_query["command"] = "SELECT";
    $this->_query["limit"] = $num;
    if(count($this->_query["order"]) == 0)
      $this->_query["order"] = [ $this->_primary => "DESC" ];
    else
      $this->_query["order"] = array_merge($this->_query["order"], [ $this->_primary => "DESC" ]);
    $this->_execute();
    return $this->_result;
  }
  public function take($num = 1) {
    $this->_query["command"] = "SELECT";
    $this->_query["limit"] = $num;
    $this->_execute();
    return $this->_result;
  }
  public function create(array $params) {
    $this->reset();
    $this->_query["command"] = "INSERT INTO";
    $this->_query["columns"] = implode(",", array_keys($params));
    $this->_query["values"] = array_values($params);
    $this->_execute("insert");
    return $this->_result;
  }
  
  public function update(array $values) {
    $this->_query["command"] = "UPDATE";
    $this->_query["values"] = $values;
    $this->_execute("update");
    return $this->_result;
  }
  
  public function destroy() {
    $this->_query["command"] = "DELETE FROM";
    $this->_execute("delete");
    return $this->_result;
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
      return $this;
    }
    if(is_array(func_get_arg(0))) {
      foreach(func_get_arg(0) as $key=>$val) {
        $k = $this->_obj($key);
        if(is_array($val)) { // USE IN
          $in = ["(" . $k . " IN ". $this->_vlist($val) .") _&&_"];
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
  
  public function whereNot() {
    if(func_num_args() == 0) return $this;
    
    if(is_string(func_get_arg(0))) {
      $cust = ["NOT (" . func_get_arg(0) . ") _&&_"];
      $params = func_get_args();
      unset($params[0]);
      foreach($params as $p) {
        $cust[] = $p;
      }
      $this->_query["where"][] = $cust;
      return $this;
    }
    if(is_array(func_get_arg(0))) {
      foreach(func_get_arg(0) as $key=>$val) {
        $k = $this->_obj($key);
        if(is_array($val)) { 
          $in = ["(" . $k . " NOT IN ". $this->_vlist($val) .") _&&_"];
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
              $columns[] = $this->_obj($as);
            } else {
              $columns[] = $this->_obj($c) . " AS " . $as;
            }
          }
        } else {
          if($a == "*") {
            $columns = ["*"];
            break;
          } else {
            $columns[] = $this->_obj($a);
          }
        }
      }
      $this->_query["columns"] = implode(",", $columns);
    }
    return $this;
  }
  
  public function joins() {
    if(func_num_args() == 0) {
      return $this;
    }
    elseif(func_num_args() == 1) {
      $this->_query["joins"][] = func_get_arg(0);
    } else {
      $this->_query["joins"][] = func_get_args();
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
    $this->_query["having"] = func_get_args();
    return $this;
  }
  
  private function _compileQuery() {
    $q = [
      "query" => "",
      "parameters" => []
    ];
    switch($this->_query["command"]) {
      case "SELECT":
        return $this->_compileSelectQuery($q);
      case "DELETE FROM":
        return $this->_compileDeleteQuery($q);
      case "INSERT INTO":
        return $this->_compileInsertQuery($q);
      case "UPDATE":
        return $this->_compileUpdateQuery($q);
      default:
        throw new DbError("Unsupported SQL command!");
    }
  }
  
  private function _execute($type="select") {
    switch($type) {
      case "select":        
        $q = $this->_compileQuery();
        $stmt = $this->_db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->_result = [];
        while($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $this->_result[] = new QueryRow($res, $this->_className);
        }
        $this->_result = new QueryResult($this->_result);
        break;
      case "insert":
        $q = $this->_compileQuery();
        $stmt = $this->_db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->_result = $stmt->rowCount();
        break;
      case "delete":
        $q = $this->_compileQuery();
        $stmt = $this->_db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->_result = $stmt->rowCount();
        break;
      case "update":
        $q = $this->_compileQuery();
        $stmt = $this->_db->prepare($q["query"]);
        $stmt->execute($q["parameters"]);
        
        $this->_result = $stmt->rowCount();
        break;
    }
  }
  
  // Compile queries
  private function _compileSelectQuery($q) {
    $query = ["SELECT"];
    $query[] = $this->_query["columns"];
    $query[] = "FROM";
    $query[] = "`" . $this->_query["table"] . "`";
    
    // joins
    if(count($this->_query["joins"]) > 0) {
      $c = $this->_className;
      foreach($this->_query["joins"] as $j) {
        if(is_string($j)) {
          if(strpos(strtolower($j), "join") !== false) {
            $query[] = $j;
          } else {
            $query[] = "JOIN";
            $query[] = $j;
            $query[] = "ON";
            $query[] = $this->_obj("id");
            $query[] = "=";
            $query[] = $c::foreign();
          }
        } elseif(is_array($j)) {
          foreach($j as $t) {
            $query[] = "JOIN";
            $query[] = $t;
            $query[] = "ON";
            $query[] = $this->_obj("id");
            $query[] = "=";
            $query[] = $c::foreign();
          }
        }
      }
    }
    
    // where
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
    
    // group by
    if($this->_query["groupby"] != "") {
      $query[] = "GROUP BY";
      $query[] = $this->_obj($this->_query["groupby"]);
    }
    if(count($this->_query["having"]) > 0) {
      $query[] = "HAVING";
      $h = $this->_query["having"];
      $query[] = $h[0];
      foreach(array_diff($h, array($h[0])) as $p) {
        $q["parameters"][] = $p;
      }
    }
    
    // order
    if(count($this->_query["order"]) > 0) {
      $query[] = "ORDER BY";
      $o = [];
      foreach($this->_query["order"] as $obj=>$ord) {
        $o[] = $this->_obj($obj) . " ".$ord;
      }
      $query[] = implode(",", $o);
    }
    //limit
    if($this->_query["limit"] != "") {
      $query[] = "LIMIT";
      $query[] = $this->_query["limit"];
    }
    // offset
    if($this->_query["offset"] != "") {
      $query[] = "OFFSET";
      $query[] = $this->_query["offset"];
    }
    $q["query"] = implode(" ", $query);
    return $q;
  }
  
  private function _compileDeleteQuery($q) {
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
        $o[] = "ORDER BY ".$this->_obj($obj) . " ".$ord;
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
  
  private function _compileUpdateQuery($q) {
    $query = ["UPDATE"];
    $query[] = "`" . $this->_query["table"] . "`";
    $query[] = "SET";
    $set = [];
    foreach($this->_query["values"] as $key => $val) {
      $set[] = $this->_obj($key)." = ?";
      $q["parameters"][] = $val;
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
        $o[] = "ORDER BY ".$this->_obj($obj) . " ".$ord;
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
  
  private function _compileInsertQuery($q) {
    $query = ["INSERT INTO"];
    $query[] = "`" . $this->_query["table"] . "`";
    $query[] = "(" . $this->_query["columns"]. ")";
    $query[] = "VALUES";
    $query[] = $this->_vlist($this->_query["values"]);
    $q["query"] = implode(" ", $query);
    $q["parameters"] = $this->_query["values"];
    return $q;
  }
  
  
  // Escaping
  private function _obj($value) {
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
  
  private function _vlist(array $value) {
    return "(". implode(",", array_fill(0, count($value), "?")) .")";
  }
  
}