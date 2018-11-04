<?php
class Login extends CI_Controller{
	function __construct(){
		parent::__construct();
		session_start();
		$this->load->library('form_validation');
		$this->load->model('Admins_model','admins');
	}
	function index(){
		// print_r($_SESSION);
		$this->form_validation->set_rules('admin_name','账号','trim|required|callback_check_login');
		$this->form_validation->set_rules('password','密码','required');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/login.html');
		}
		else{
			$_SESSION['admin']['admin_name']=trim($this->input->post('admin_name'));
			redirect('admin/manage/index');
		}
		$this->output->enable_profiler(false);
	}
	function check_login($name){
		$admin_name=trim($name);
		$password=$this->input->post('password',true);
		$row=$this->admins->fetch_all(array('admin_name'=>$admin_name,'is_normal'=>'1'),'admin_name,password,security_code');
		
		if(empty($row)){
			$this->form_validation->set_message('check_login','账号不存在');
			return false;
		}else{
			$pwd=md5(md5($password).$row[0]['security_code']);
			if($pwd != $row[0]['password']){
				$this->form_validation->set_message('check_login','密码错误');
				return false;
			}else{
				return true;
			}
		}
	}
}
?>