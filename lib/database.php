<?php 
class Database {
  function __construct() {
    #load config file
    $this->config = parse_ini_file('config/run.ini', true);
    $this->table = 'appointment'; #set short hand for table name
  }

  public function hasExistingEntries($start, $end) {
    # create query params
    $sql = "SELECT id, start_time, end_time FROM ".$this->table." WHERE state <> 'DELETED'";
    $bindTypes = '';
    $bindValues = array();

    #execute query
    $stmt = $this->_doQuery($sql, $bindTypes, $bindValues);

    #get appointments from db and check everry entry, if its blocked or not
    $stmt->bind_result($id, $dbStart, $dbEnd);
    $blocked = false;
    while($stmt->fetch()) {
      #check if start and end are outside the db time frame at the same time
      $completeOutside = ($start <= $dbStart && $end >= $dbEnd);

      #check if start and end are inside the db time frame at the same time
      $completeInside = ($start >= $dbStart && $end <= $dbEnd);

      #check if only start date is between db time frame
      $startInside = ($start >= $dbStart && $start <= $dbEnd);

      #check if only start date is between db time frame
      $endInside = ($end <= $dbEnd && $end >= $dbStart);

      #appointment is blocked, if either of the checks are true
      if ($completeInside || $completeOutside || $startInside || $endInside) {
        $blocked = true;
        break;
      }
    }

    #close connections
    $this->_closeConnection();
    return $blocked;
  }

  public function createEntry($start, $end, $name) {
    # create query params
    $sql = "INSERT INTO ".$this->table." (start_time, end_time, name) VALUES(?, ?, ?)";
    $bindTypes = 'sss';
    $bindValues = array($start, $end, $name);

    #execute query
    $stmt = $this->_doQuery($sql, $bindTypes, $bindValues);

    #get insert id
    $id = $stmt->insert_id;

    #close connections
    $this->_closeConnection();

    #return insert id
    return $id;
  }

  public function getEntry($id) {
    # create query params
    $sql = "SELECT id FROM ".$this->table." WHERE id = ? AND state <> 'DELETED'";
    $bindTypes = 'i';
    $bindValues = array($id);

    #execute query
    $stmt = $this->_doQuery($sql, $bindTypes, $bindValues);
    
    # get result
    $stmt->bind_result($dbId);
    $stmt->fetch(); # no loop need, it can be only one entry

    #close connections
    $this->_closeConnection();

    #return id
    return $dbId;
  }

  public function updateEntry($id, $topic, $desc, $location) {
    # create query params
    $sql = "UPDATE ".$this->table." SET topic=?, description=?, location=? WHERE id=?";
    $bindTypes = 'sssi';
    $bindValues = array($topic, $desc, $location, $id);

    #execute query
    $this->_doQuery($sql, $bindTypes, $bindValues);
    
    #close connections
    $this->_closeConnection();
  }

  #wrapper method to create a connection
  private function _getConnection() {
    #create connection to mysql
    $this->con = new mysqli(
      $this->config['mysql']['host'], 
      $this->config['mysql']['user'], 
      $this->config['mysql']['password'], 
      $this->config['mysql']['database'], 
      $this->config['mysql']['port']);

    # check for connection error
    if ($this->con->connect_error) {
      throw new Exception('Connect Error ('.$this->con->connect_errno.') '.$this->con->connect_error);
    }

    #return connection
    return $this->con;
  }

  #wrapper method to close statement and connection
  private function _closeConnection() {
    $this->stmt->close();
    $this->con->close();
  }

  #wrapper method, to only implement query stuff only once
  private function _doQuery($sql, $bindTypes, $bindParams) {
    #connect to database
    $this->_getConnection();

    #prepare statement, throw error if it fails
    $this->stmt = $this->con->prepare($sql);
    if ($this->stmt===false) {
      throw new Exception("prepare failed: ".$this->con->error.' ('.$mysqli->errno.')');
    }

    if (count($bindParams) > 0) { #only bind params, if params were provided
      #bind params to prepares statement, throw error if it fails
      #use call_user_func_array method to support arbitrary bind params
      $result = call_user_func_array(array($this->stmt, "bind_param"), array_merge(array($bindTypes), $bindParams));
      if ($result === false) {
        throw new Exception('param binding failed: '.$this->stmt->error.' ('.$this->stmt->errno.')');
      }
    }

    #execute query, throw error if it fails
    $result = $this->stmt->execute();
    if ($result === false) {
      throw new Excpetion('execute failed: '.$this->stmt->error.' ('.$this->stmt->errno.')');
    }

    # return statement object for further use
    return $this->stmt;
  }
}

?>