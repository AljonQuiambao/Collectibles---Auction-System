// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable();

  $('#pending-items').DataTable();
  $('#approved-items').DataTable();
  $('#reject-items').DataTable();
  $('#cancel-items').DataTable();

  $('#bidder-users').DataTable();
  $('#auctioneer-users').DataTable();
  $('#multi-users').DataTable();
});

