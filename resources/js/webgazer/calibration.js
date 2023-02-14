var PointCalibrate = 0;
var CalibrationPoints = {};

/**
 * Clear the canvas and the calibration button.
 */
function ClearCanvas() {
	$(".Calibration").hide();
	var canvas = document.getElementById("plotting_canvas");
	canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
}

/**
 * Show the instruction of using calibration at the start up screen.
 */
function PopUpInstruction() {
	ClearCanvas();
	swal({
		title: "Калибровка взгляда",
		text: "Подождите появления в левом верхнем углу окошка вебкамеры.\nПосле этого прокликайте каждую из 9 точек на экране. Вам необходимо прокликать каждую из этих точек 5 раз до тех пор, пока цвет не сменится на жёлтый. Это настроит учет движения ваших глаз.",
		buttons: {
			confirm: { text: "Начать калибровку", value: true, className: 'btn btn-primary' },
		},
	}).then((value) => {
		if (value) {
			ShowCalibrationPoint();
			openFullscreen();
		}
	});

}
/**
  * Show the help instructions right at the start.
  */
function helpModalShow() {
	// $('#helpModal').modal('show');
	Restart();
}

/**
 * Load this function when the index page starts.
* This function listens for button clicks on the html page
* checks that all buttons have been clicked 5 times each, and then goes on to measuring the precision
*/
$(document).ready(function () {
	ClearCanvas();
	helpModalShow();
	$(".Calibration").click(function () { // click event on the calibration buttons

		var id = $(this).attr('id');

		if (!CalibrationPoints[id]) { // initialises if not done
			CalibrationPoints[id] = 0;
		}
		CalibrationPoints[id]++; // increments values

		if (CalibrationPoints[id] == 5) { //only turn to yellow after 5 clicks
			$(this).css('background-color', 'yellow');
			$(this).prop('disabled', true); //disables the button
			PointCalibrate++;
		} else if (CalibrationPoints[id] < 5) {
			//Gradually increase the opacity of calibration points when click to give some indication to user.
			var opacity = 0.2 * CalibrationPoints[id] + 0.2;
			$(this).css('opacity', opacity);
		}

		//Show the middle calibration point after all other points have been clicked.
		if (PointCalibrate == 8) {
			$("#Pt5").show();
		}

		if (PointCalibrate >= 9) { // last point is calibrated
			//using jquery to grab every element in Calibration class and hide them except the middle point.
			$(".Calibration").hide();
			$("#Pt5").show();

			// clears the canvas
			var canvas = document.getElementById("plotting_canvas");
			canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

			// notification for the measurement process
			swal({
				title: "Измерение точности калибровки",
				text: "Пожалуйста, не двигайте мышь и сфокусируйте свой взгляд на центральную точку в течение следующих 5 секунд. Это позволит вычислить точность айтрекинга.",
				closeOnEsc: false,
				allowOutsideClick: false,
				closeModal: true
			}).then(isConfirm => {

				// makes the variables true for 5 seconds & plots the points
				$(document).ready(function () {

					store_points_variable(); // start storing the prediction points

					sleep(5000).then(() => {
						stop_storing_points_variable(); // stop storing the prediction points
						var past50 = webgazer.getStoredPoints(); // retrieve the stored points
						var precision_measurement = calculatePrecision(past50);
						const title = "Вычисленная точность айтрекинга: " + precision_measurement + "%";
						let buttons = null;
						if (precision_measurement <= 50) {
							title = title + ".\nНедостаточная точность калибровки, выполните калибровку заново и более аккуратно фокусируйте взгляд на предлагаемых точках";
							buttons = {
								recaliber: { text: 'Перекалибровать', value: false, className: 'btn btn-primary' }
							};
						} else {
							buttons = {
								recaliber: { text: 'Перекалибровать', value: false, className: 'btn btn-secondary' },
								continue: { text: 'Начать тестирование', value: true, className: 'btn btn-primary' },
							};
						}
						swal({
							title: title,
							allowOutsideClick: false,
							buttons: buttons,
						}).then(value => {
							if (value) {
								//clear the calibration & hide the last middle button
								ClearCanvas();
								closeFullscreen();
								window.location.href = window.forward;
							} else {
								//use restart function to restart the calibration
								webgazer.clearData();
								ClearCalibration();
								ClearCanvas();
								openFullscreen();
								ShowCalibrationPoint();
							}
						});
					});
				});
			});
		}
	});
});

/**
 * Show the Calibration Points
 */
function ShowCalibrationPoint() {
	$(".Calibration").show();
	$("#Pt5").hide(); // initially hides the middle button
}

/**
* This function clears the calibration buttons memory
*/
function ClearCalibration() {
	// Clear data from WebGazer

	$(".Calibration").css('background-color', 'red');
	$(".Calibration").css('opacity', 0.2);
	$(".Calibration").prop('disabled', false);

	CalibrationPoints = {};
	PointCalibrate = 0;
}

// sleep function because java doesn't have one, sourced from http://stackoverflow.com/questions/951021/what-is-the-javascript-version-of-sleep
function sleep(time) {
	return new Promise((resolve) => setTimeout(resolve, time));
}

/* Get the documentElement (<html>) to display the page in fullscreen */
var elem = document.documentElement;

/* View in fullscreen */
function openFullscreen() {
	if (elem.requestFullscreen) {
		elem.requestFullscreen();
	} else if (elem.webkitRequestFullscreen) { /* Safari */
		elem.webkitRequestFullscreen();
	} else if (elem.msRequestFullscreen) { /* IE11 */
		elem.msRequestFullscreen();
	}
}

/* Close fullscreen */
function closeFullscreen() {
	if (document.exitFullscreen) {
		document.exitFullscreen();
	} else if (document.webkitExitFullscreen) { /* Safari */
		document.webkitExitFullscreen();
	} else if (document.msExitFullscreen) { /* IE11 */
		document.msExitFullscreen();
	}
}
