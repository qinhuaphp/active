<?php
class MY_Controller extends CI_Controller{
	function __construct(){
		parent::__construct();
		session_start();
	}
	function checklogin(){
		if(!isset($_SESSION['admin'])||empty($_SESSION['admin'])){
					redirect('admin/login/index');			
		}
	}
}
?>