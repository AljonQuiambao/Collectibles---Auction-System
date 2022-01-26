// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable();

  $('#pending-items').DataTable({
      "order": [[ 5, "desc" ]]
  });

  $('#approved-items').DataTable({
    "order": [[ 5, "desc" ]]
  });

  $('#reject-items').DataTable({
    "order": [[ 5, "desc" ]]
  });

  $('#cancel-items').DataTable({
    "order": [[ 5, "desc" ]]
  });

  $('#bought-items').DataTable({
    "order": [[ 5, "desc" ]]
  });

  $('#ongoing-bidding').DataTable({
    "order": [[ 5, "desc" ]]
  });

  $('#bidding-history').DataTable({
    "order": [[ 4, "desc" ]]
  });

  $('#sold-items').DataTable({
    "order": [[ 5, "desc" ]]
  });

  $('#bidder-users').DataTable();
  $('#auctioneer-users').DataTable();
  $('#multi-users').DataTable();
});

