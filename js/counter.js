(function($) {
    "use strict"; // Start of use strict

    function countDownDate(bidTime, endTime,  $target) {
         // Set the date we're counting down to
        var bidTimeDate = new Date(bidTime).getTime();
        var endTimeDate = new Date(endTime).getTime();

        // console.log(bidTimeDate);
        // console.log(endTimeDate);

        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get today's date and time
            var now = new Date().getTime();
                
            // Find the distance between now and the count down date
            var distance = endTimeDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // If the count down is over, write some text 
            // distance = -1; // for debug
            if (distance < 0) {
                clearInterval(x);
            
                var itemId = $($target).data('item_id');
                var bidderId = $($target).data('bidder_id');
                var auctioneerId = $($target).data('auctioneer_id');

                $($target).offsetParent().addClass('hidden');
                //$target.text("DONE");

                  // AJAX Request
                  $.ajax({
                    url: 'notification-updates.php',
                    type: 'POST',
                    data: {
                        itemId: itemId,
                        bidderId: bidderId,
                        auctioneerId: auctioneerId
                    },
                    success: function(response) {
                        if (response == 1) {
                            // Remove row from HTML Table
                            $(el).closest('tr').css('background', 'tomato');
                            $(el).closest('tr').fadeOut(800, function() {
                                $(this).remove();
                            });

                            $('.deleted-message').removeClass('hidden');
                        } else {
                            alert('Invalid data id.');
                        }

                    }
                });

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

        countDownDate($(element).data('bid-time'), $(element).data('end-time'), $(element));
    }

  })(jQuery); // End of use strict
  