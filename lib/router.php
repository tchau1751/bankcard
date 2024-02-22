<?php 
class Router {
  function __construct() {
    # we deliver json
    header('Content-type: application/json');

    #include appointment class
    include_once('./lib/appointment.php');
  }

  #basic routing based on the first path level
  public function doRouting($action) {
    switch ($action) {
      #creates an appointment, if its available
      case 'create':
        $result = $this->_doCreate();   
        break;
      case 'update': #updates an existing appointment with meta data
        $result = $this->_doUpdate();
        break;
      default: # no supported action, return with error
        $result = $this->_formatError(new Exception('unsupported action'));
    }

    #echo json encoded result
    return json_encode($result);
  }

  private function _doCreate() {
    # create object
    $appointment = new Appointment();

    #create an new appointment
    try {
      $id = $appointment->create($_POST['start_time'], $_POST['end_time'], $_POST['name']);
      if ($id !== 0) {
        $result = array(
          'success' => array (
            'message' => 'appointment created',
            'id' => $id,
            'start' => $_POST['start_time'],
            'end' => $_POST['end_time'],
            'name' => $_POST['name']
        ));
      } else {
        throw new Exception("appointment creation failed");
      }
    } catch (Exception $e) { #if fails set error
      $result = $this->_formatError($e);
    }

    return $result;
  }

  private function _doUpdate() {
    #create object
    $appointment = new Appointment();

    try {
      $success = $appointment->update($_POST['appointment_id'], $_POST['topic'], $_POST['description'], $_POST['location']);
      if ($success) {
        $result = array(
          'success' => array (
            'message' => 'appointment updated',
            'id' => $_POST['appointment_id'],
            'topic' => $_POST['topic'], 
            'description' => $_POST['description'], 
            'location' => $_POST['location']
        ));
      } else {
        throw new Exception("appointment update failed");
      }
    } catch (Exception $e) { #if fails set error
      $result = $this->_formatError($e);
    }

    return $result;
  }

  private function _formatError($err) {
    return array(
      'error' => array (
        'message' => $err->getMessage() 
    ));
  }
}
?>