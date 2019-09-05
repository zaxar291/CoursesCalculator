$(document).ready(function () {
    setInterval(function(){
        $('#logo>span').addClass('blink_on');
        setTimeout(function(){$('#logo>span').removeClass('blink_on')},1500);
    },6000)
});