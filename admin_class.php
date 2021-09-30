<?php
session_start();
ini_set('display_errors', 1);
class Action
{
	private $db;

	public function __construct()
	{
		ob_start();
		require_once 'config.php';

		$this->db = $link;
	}
	function __destruct()
	{
		$this->db->close();
		ob_end_flush();
	}

	function get_users()
	{
		extract($_POST);
		$data = " name Like '%$filter%' ";
		$data .= " or username Like '%$filter%' ";
		$rdata = array();
		$get = $this->db->query("SELECT * FROM users where $data");
		while ($row = $get->fetch_assoc()) {
			$rdata[] = $row;
		}
		return json_encode($rdata);
	}

	function send_chat()
	{
		extract($_POST);
		$data = " message = '$message' ";
		$data .= ", user_id = '{$_SESSION['login_id']}' ";
		if (empty($convo_id)) {
			$cdata = " user_ids = '$user_id,{$_SESSION['login_id']}' ";
			$cdata2 = " user_ids = '{$_SESSION['login_id']},$user_id' ";
			$user_ids = $_SESSION['login_id'] . "," . $user_id;
			$chk = $this->db->query("SELECT * from thread where $cdata or $cdata2 ");
			if ($chk->num_rows > 0) {
				$convo_id = $chk->fetch_array()['id'];
			} else {
				$thread = $this->db->query("INSERT INTO thread set $cdata ");
				$convo_id = $this->db->insert_id;
			}
		} else {
			$qry = $this->db->query("SELECT * from thread where md5(id) ='$convo_id' ")->fetch_array();
			$convo_id = $qry['id'];
			$user_ids = $qry['user_ids'];
		}
		$data .= ", convo_id = '$convo_id' ";
		$save = $this->db->query("INSERT INTO messages set $data");
		if ($save)
			return json_encode(array('status' => 1, 'convo_id' => md5($convo_id), 'convo_users' => $user_ids));
	}

	function load_convo()
	{
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT m.message,u.id,u.name,u.avatar,u.username FROM messages m inner join users u on u.id = m.user_id where md5(m.convo_id) = '$convo_id' ");
		while ($row = $get->fetch_assoc()) {
			$data[] = $row;
		}
		return json_encode($data);
	}

	function read_msg()
	{
		extract($_POST);
		if (isset($user_id) && $user_id > 0) {
			$update = $this->db->query("UPDATE messages set status = 1 where md5(convo_id) = '$convo_id' and user_id=$user_id ");
			if ($update) {
				return 1;
			}
		}
	}
}
