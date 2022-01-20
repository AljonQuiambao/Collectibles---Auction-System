<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/collectibles-auctions.js"></script>

<!-- Datatables -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/demo/datatables-demo.js"></script>

<script src="vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/chart-area-demo.js"></script>
<script src="js/demo/chart-pie-demo.js"></script>

<script>
    $(document).ready(function() {
        // Delete 
        $('.delete').click(function() {
            var el = this;

            var deleteId = $(this).data('id');
            var tableName = $(this).data('table-name');

            var confirmalert = confirm("Are you sure you want to delete?");
            if (confirmalert == true) {
                // AJAX Request
                $.ajax({
                    url: 'remove.php',
                    type: 'POST',
                    data: {
                        id: deleteId,
                        tableName: tableName
                    },
                    success: function(response) {
                        if (response == 1) {
                            // Remove row from HTML Table
                            $(el).closest('tr').css('background', 'tomato');
                            $(el).closest('tr').fadeOut(800, function() {
                                $(this).remove();
                            });
                        } else {
                            alert('Invalid data id.');
                        }

                    }
                });
            }

        });

    });
</script>

<script>
    $(document).ready(function() {
        $('#alertsDropdown').click(function() {

            var userId = $(this).data('user-id');
            $.ajax({
                url: 'alert-status.php',
                type: 'POST',
                data: {
                    userId: userId,
                    type: 1
                },
                success: function(response) {
                    if (response == 1) {} else {
                        alert('Invalid data id.');
                    }

                }
            });
        });

        $('#messagesDropdown').click(function() {

            var userId = $(this).data('user-id');
            $.ajax({
                url: 'alert-status.php',
                type: 'POST',
                data: {
                    userId: userId,
                    type: 2
                },
                success: function(response) {
                    if (response == 1) {} else {
                        alert('Invalid data id.');
                    }

                }
            });
        });
    });
</script>