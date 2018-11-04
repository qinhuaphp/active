<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Manage extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->checklogin();
	}
	function index(){
	
		$this->load->view('admin/index.html');
	}
	function top(){
		$this->load->view('admin/top.html');
	}
	function left(){
		//加载权限导航树
		$this->load->model('node_model','node');
		$name=$_SESSION['admin']['admin_name'];
		$data=$this->node->fetch_privliges($name);
		$_SESSION['admin']['nids']=$data['nid'];
		//print_r($_SESSION);
		$this->load->view('admin/left.html',$data);
		$this->output->enable_profiler(false);
	}
	function drag(){
		$this->load->view('admin/drag.html');
	}
	function main(){
		//print_r($_SERVER);
		$data['server']=$this->input->server(array('SERVER_SOFTWARE','SERVER_NAME','SERVER_ADDR'));
		//print_r($data['server']);
		$this->load->library('user_agent');//加载用户代理类
		$data['client_os']=$this->agent->platform();//获取客户端的操作系统
		if($this->agent->is_browser()){//判断是否为浏览器访问
			$data['client_browser']='名称'.$this->agent->browser().'| 版本'.$this->agent->version();
		}
		$data['db']=$this->db->platform();//获取数据库平台
		$data['version']=$this->db->version();//获取数据库版本
		$data['ip_address']=$this->input->ip_address();//获取客户端ip
		$this->load->view('admin/main.html',$data);
	}
	//修改密码
	function updatepwd(){
		$admin_name=$_SESSION['admin'];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password','密码','required|min_length[6]');
		$this->form_validation->set_rules('repeatpwd','确认密码','required|matches[password]');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/updatepwd.html');
		}else{
			$this->load->model('admins_model','admins');
			$pwds=$this->admins->encrypt($this->input->post('password',true));
			$this->db->where(array('admin_name'=>$admin_name))->update('admins',array('password'=>$pwds['password'],'security_code'=>$pwds['security_code']));
			if($this->db->affected_rows() > 0){
				$da['tip']='成功修改密码';
			}else{
				$da['tip']='未修改密码';
			}
			$da['time']='1800';
			$da['url']=site_url('admin/manage/index');
			$this->load->view('admin/sysinfo.html',$da);
		}
		$this->output->enable_profiler(false);
	}
	function quite(){
		unset($_SESSION['admin']);	
		session_destroy();
		redirect('cctv5');
	}
}
?>