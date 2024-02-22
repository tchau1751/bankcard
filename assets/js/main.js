var dateTimeFormat = 'YYYY-MM-DD HH:mm:ss';
var appointmentId = 0;

/**
Helper Functions
**/

//generic function to show either an error or success notification
show = function(el, message) {
  var $el = $('#'+el)
  $el.find('span').text(message)
  $el.removeClass('hide');
}

//hide error and success notifications
hideErrorSuccess = function() {
  $('#error, #success').addClass('hide');
}

//show additional information form
showDataForm = function() {
  $('#update').removeClass('hide')
}

//hide additional information form
hideDataForm = function() {
  $('#update').addClass('hide')
}

//show reset button
showReset = function() {
  $('#reset').removeClass('hide');
}

//hide reset button
hideReset = function() {
  $('#reset').addClass('hide');
}

//set current time in date time picker
setCurrentTime = function() {
  //set default date to today
  var picker = $('#datetimepicker').data('datetimepicker');
  picker.setDate(moment());

}

/**
Event Functions
**/

doCreateSubmit = function(event) {
  event.preventDefault(); //prevent default submit behaviour
  //hide open notifications or the additional form
  hideErrorSuccess();
  hideDataForm();
  hideReset();
  //save this as variable
  var $form = $(this);

  //get form input values
  var name = $form.find('#name').val();
  var duration = $form.find('#duration').val();
  var datetime = $form.find('#datetime').val();

  //validate form input values
  if (name === undefined || name === null || name === '') return show('error', 'name need to be filled');
  if (duration === undefined || duration === null || duration === '' || Number(duration) === 0) return show('error', 'duration need to be set and greater 0');
  if (datetime === undefined || datetime === null || datetime === '') return show('error', 'datetime need to choosen');

  //create start and end date objects, based on the input
  var start = moment(datetime);
  var end = moment(datetime).add('m', duration);
  //do ajax request 
  $.post ($form.attr('action'),
    { // set required parameter
      name: name,
      start_time: start.format(dateTimeFormat),
      end_time: end.format(dateTimeFormat)
    }
  ).always(function(response) { //register done event to react on finished 
    //determine if it was successfully or not
    if (response.success !== undefined) {
      show('success', 'Appointment created!'); //show notification
      appointmentId = response.success.id; //save created id, for later access
      showDataForm(); //show additional information form
    } else {
      show('error', response.error.message); //show error
    }
  });
}

doUpdateSubmit = function(event) {
  event.preventDefault(); //prevent default submit behaviour
  //save this as variable
  var $form = $(this);

  var data = 'appointment_id='+appointmentId+'&'+$form.serialize();
  //do ajax request 
  $.post ($form.attr('action'), data).done(function(response) { //register done event to react on finished 
    //determine if it was successfully or not
    if (response.success !== undefined) {
      show('success', 'Appointment updated!'); //show notification
      showReset();
    } else {
      show('error', response.error.message); //show error
    }
  });
}

doReset = function(event) {
  event.preventDefault(); //prevent default submit behaviour
  
  //do a simple reload :)
  window.location.reload()
}

/**
Main execution
**/

//execution if document is ready
$(document).ready(function() {
  //add datetimepicket
  $('#datetimepicker').datetimepicker({
    maskInput: true,
    pick12HourFormat: false,
    pickSeconds: false,
    pickTime: true
  });

  setCurrentTime();

  //register on create form submit
  $('#create form').submit(doCreateSubmit);

  //register on update form submit
  $('#update form').submit(doUpdateSubmit);

  //register on click to reset forms
  $('#reset button').click(doReset);
});