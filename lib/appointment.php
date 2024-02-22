<?php 
include_once('database.php'); 

class Appointment {
  const DATETIME_FORMAT = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/'; #use specified format YYYY-MM-DD HH:MM:SS

  function __construct() {
    $this->db = new Database();
  }
    
  public function create($start_time, $end_time, $name) {
    # validate times
    $this->_validateTimes($start_time, $end_time);
    # validate name
    $this->_validateName($name);
    # check availability
    $this->_checkAvailability($start_time, $end_time);
    # create entry
    $id = $this->_create($start_time, $end_time, $name);

    return $id;
  }

  public function update($id, $topic, $desc, $location) {
    #check and parse id
    $id = $this->_validateId($id);
    #chekc if id exists
    $this->_checkAppointment($id);
    #do update
    $this->db->updateEntry($id, $topic, $desc, $location);

    return true;
  }

  private function _validateTimes($start, $end) {
    #test if start is in the correct format
    if (!preg_match(self::DATETIME_FORMAT, $start)) {
      throw new Exception("start time is in the wrong format");
    }

    #test if start is in the correct format
    if (!preg_match(self::DATETIME_FORMAT, $end)) {
      throw new Exception("end time is in the wrong format");
    }

    # test if start time is before end time
    if ($end < $start) {
      throw new Exception("end time must be after start time");
    }
  }

  private function _validateName($name) {
    # test if name is set and not empty
    if (!isset($name) || strlen($name) === 0) {
      throw new Exception("name must be set and not empty");
    }
  }

  private function _validateId($id) {
    # first parse the string to int
    try {
      $id = intval($id);
    } catch (Exception $e) {
      throw new Exception("error by parsing the id (".$e->message.")");
    }

    # test if id is set and not 0
    if (!isset($id) || $id === 0) {
      throw new Exception("id must be set and greater then 0");
    }

    return $id;
  }
  
  private function _checkAvailability($start, $end) {
    #check if existing entries are in db
    $existing = $this->db->hasExistingEntries($start, $end);
    if ($existing) { 
      throw new Exception("time frame not available");
    }
  }

  private function _checkAppointment($id) {
    #check if appointemnt exists in database
    $dbId = $this->db->getEntry($id);
    if ($dbId !== $id) { 
      throw new Exception("appointment doesn't exists");
    }
  }

  private function _create($start, $end, $name) {
    # create entry and if id is 0 set it fails
    $id = $this->db->createEntry($start, $end, $name);
    if ($id === 0) { 
      throw new Exception("couldn't create the entry");
    }
    return $id;
  }
}
?>