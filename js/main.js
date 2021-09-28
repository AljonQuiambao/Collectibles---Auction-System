(function($) {
    "use strict"; // Start of use strict

    $("#place-bid").on('click', function(e) {
        var $bidPayment = $("#bid-payment");
        var $bidTxtbox = $("#bid-textbox");

        var $current_bid = parseInt($bidPayment.text());
        var $input_bid = parseInt($($bidTxtbox).val());

        if ($input_bid === 0) {
            $("#must-100-input").addClass('hidden');
            $("#greater").addClass('hidden');
            $("#success").addClass('hidden');
            $("#no-input").removeClass('hidden');

            return false;
        }
        //show error when input bid is lowest than the current bid
        else if ($current_bid > $input_bid) {
            $("#must-100-input").addClass('hidden');
            $("#greater").removeClass('hidden');
            $("#success").addClass('hidden');
            $("#no-input").addClass('hidden');
            return false;
        }
         //show error when input bid is lowest than 100
        else if ($input_bid < ($current_bid + 100)) {
            $("#must-100-input").removeClass('hidden');
            $("#greater").addClass('hidden');
            $("#success").addClass('hidden');
            $("#no-input").addClass('hidden');
            return false;

        } else {
            $("#must-100-input").addClass('hidden');
            $("#greater").addClass('hidden');
            $("#success").removeClass('hidden');
            $("#no-input").addClass('hidden');
            return true;
        }
    });


    $("#category").on('change', function(e) {
        var $value = parseInt(e.currentTarget.value);
        var $albums = $(".albums");
        var $coins = $(".coins");
        var $paintings = $(".paintings");
        var $sports = $(".sports");
        var $toys = $(".toys");

        switch ($value) {
            case 1:
                $albums.removeClass("hidden");
                $coins.addClass("hidden");
                $paintings.addClass("hidden");
                $sports.addClass("hidden");
                $toys.addClass("hidden");
                break;
            case 2:
                $albums.addClass("hidden");
                $coins.removeClass("hidden");
                $paintings.addClass("hidden");
                $sports.addClass("hidden");
                $toys.addClass("hidden");
                break;

            case 3:
                $albums.addClass("hidden");
                $coins.addClass("hidden");
                $paintings.removeClass("hidden");
                $sports.addClass("hidden");
                $toys.addClass("hidden");
                break;
            
            case 4:
                $albums.addClass("hidden");
                $coins.addClass("hidden");
                $paintings.addClass("hidden");
                $sports.removeClass("hidden");
                $toys.addClass("hidden");
                break;

            case 5:
                $albums.addClass("hidden");
                $coins.addClass("hidden");
                $paintings.addClass("hidden");
                $sports.addClass("hidden");
                $toys.removeClass("hidden");
                break;
        
            default:
                $albums.removeClass("hidden");
                $coins.removeClass("hidden");
                $paintings.removeClass("hidden");
                $sports.removeClass("hidden");
                $toys.removeClass("hidden");
                break;
        }     

    });

    $("#btn-login").on('click', function (e) {
        var $email = $("#email-address");
        localStorage.setItem('login-user',  $email.val());

         if ($email.val() === "auctioneer") {
            $(this).attr("href", "my-auctions.html");
        } else {
            $(this).attr("href", "index.html");
        }
    });

    $(document).ready(function() {
        var $user = localStorage.getItem('login-user'); 

        if ($user === "bidder") {
            $("#bidder").removeClass("hidden");
        }

        else if ($user === "auctioneer") {
            $("#auctioneer").removeClass("hidden");
        }

        else {
            $("#bidder").removeClass("hidden");
            $("#auctioneer").removeClass("hidden");
        }
    });
  
  })(jQuery); // End of use strict
  