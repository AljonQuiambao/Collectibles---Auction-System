(function($) {
    "use strict"; // Start of use strict

    function countDownDate(dateTime, $target) {
         // Set the date we're counting down to
        var countDownDate = new Date(dateTime).getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get today's date and time
            var now = new Date().getTime();
                
            // Find the distance between now and the count down date
            var distance = countDownDate - now;
                
            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // If the count down is over, write some text 
            // distance = -1; // for debug
            if (distance < 0) {
                clearInterval(x);
                $target.text("DONE");

                $('#bid-textbox').attr('disabled', 'disabled');
                $('#place-bid').attr('disabled', 'disabled');

                $('#comment').attr('disabled', 'disabled');
                $('#save-comment').attr('disabled', 'disabled');

                if ($('.indicator-status').val() == 4) {
                    $("#counter_submit").click(); 
                }
            } else {
                $target.text(days + "d " + hours + "h " + minutes + "m " + seconds + "s ");
            }

        }, 1000);
    }

    for (let index = 0; index < $('.counter').length; index++) {
        const target = $('.counter');
        const element = $(target)[index];

        countDownDate($(element).data('date-time'), $(element));
    }

  })(jQuery); // End of use strict
  