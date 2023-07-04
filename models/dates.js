/* var start_date = document.getElementById('start_date').value;
var end_date = document.getElementById('end_date').value;
 */

$(document).ready(function() {

    $('#start_date').val();
    $('#end_date').val();

    let start_date = start_date

    const [dateString, timeString] = dateTimeString.split(' ')
    const [day, month, year] = dateString.split('/')
    const [hour, minute, second] = timeString.split(':')

    const date1 = new Date(+year, +month - 1, +day, +hour, +minute, +second + .000)

    console.log(date1);


});