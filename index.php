<?php 
# activate error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

# determine if its an ajax request or not
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

# if its ajax request then do basing routing and deliver json result
# 
# HINT: Is easy hackable via changing the http headers, but for now its enough :) 
if ($isAjax) {
  include_once('lib/router.php');
  $router = new Router();
  echo $router->doRouting($_GET['action']);
  exit;
}
# if not deliver basic html
?>
<!DOCTYPE html>
<html>
<head>
  <title>ABF - Appointment Booking Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- include bootstrap -->
  <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <link href="/assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" >
  
  <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootswatch/3.1.0/simplex/bootstrap.min.css">
  <!-- include own stuff -->
  <link href="/assets/css/main.css" rel="stylesheet" type="text/css">
</head>
  <body>
    <!-- header -->
    <header class="navbar navbar-default navbar-inverse" role="navigation">
      <nav class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <a class="navbar-brand" href="#">ABF - <strong>A</strong>ppointment <strong>B</strong>ooking <strong>F</strong>orm</a>
        </div>
      </nav>
    </header>
    <!-- creating appointment -->
    <div id="create"  class="container">
      <div class="jumbotron">
        <h1>Hello, schedule an appointment with me :)</h1>
        <p>There isn't much to do, just select a time, duration and give me your name!</p>
        <form class="form-inline row" role="form" action="/create">
          <div class="form-group col-sm-3 pull-left">
            <label for="name">Who?</label>
            <input type="text" class="form-control" id="name" placeholder="who?">
          </div>
          <label for="datetime" class="pull-left">When?</label>
          <div id="datetimepicker" class="input-group col-sm-3 date pull-left">
            <input type="text" data-format="yyyy-MM-dd hh:mm" class="form-control" id="datetime" placeholder="date and time" disabled>
            <span class="input-group-addon">
              <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
            </span>
          </div>
          <div class="form-group col-sm-4 pull-left">
            <label for="duration">How long (minutes)?</label>
            <input type="number" class="form-control" id="duration" value="10">
          </div>
          <button type="submit" class="btn btn-primary pull-right">Get it</button>
        </form>
      </div>
    </div>

    <!-- success html snippet -->
    <div id="success" class="container hide">
      <div class="alert alert-success"><strong>Congratulations!</strong> <span></span> </div>
    </div>

    <!-- failed html snippet -->
    <div id="error" class="container hide">
      <div class="alert alert-danger"><strong>Sorry!</strong> <span></span></div>
    </div>

    <!-- updating appointment -->
    <div id="update" class="container hide">
      <div class="jumbotron">
        <h1>Some more informations? :)</h1>
        <form class="form-inline row" role="form" action="/update">
          <div class="form-group col-sm-3">
            <label for="topic">About</label>
            <input type="text" class="form-control" name="topic" placeholder="About?">
          </div>
          <div class="form-group col-sm-3">
            <label for="location">Where</label>
            <input type="text" class="form-control" name="location" placeholder="where?">
          </div>
          <div class="form-group col-sm-4">
            <label for="description">More details?</label>
            <input type="text" class="form-control" name="description" placeholder="more details?">
          </div>
          <button type="submit" class="btn btn-default pull-right">update it</button>
        </form>
      </div>
    </div>
    <div id="reset" class="container hide">
      <button type="button"  class="btn btn-default btn-block center-block">Create a new appointment?</button>
    </div>
 
    <!-- include jquery -->
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <!-- include bootstrap -->
    <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <!-- moemnt.js -->
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.1/moment.min.js"></script>
    <!-- date picker -->
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
    <!-- include own stuff -->
    <script type="text/javascript" src="/assets/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="/assets/js/main.js"></script>
  </body>
</html>