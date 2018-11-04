<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Systemmng extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->checklogin();
	}
	function index(){
		$this->load->library('form_validation');
		$this->load->model('systemsetup_model','systemsetup');
		$data['result']=$this->systemsetup->fetch_all();
		$this->form_validation->set_rules('title','标题','trim|required');
		$this->form_validation->set_rules('keywords','关键词','trim|required');
		$this->form_validation->set_rules('defaultpwd','用户初始密码','required');
		$this->form_validation->set_rules('description','详细描述','trim|required');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/systemsetup.html',$data);
		}else{
			$array=array(
				array('setupname'=>'title','setupvalue'=>$this->input->post('title',true)),
				array('setupname'=>'keywords','setupvalue'=>$this->input->post('keywords',true)),
				array('setupname'=>'defaultpwd','setupvalue'=>$this->input->post('defaultpwd',true)),
				array('setupname'=>'description','setupvalue'=>$this->input->post('description',true))
			);
			$res=$this->systemsetup->renew_batch($array,'setupname');
			//echo $res;
			if($res > 0){
				$dat['tip']='设置成功!';
			}else{
				$dat['tip']='设置未生效';
			}
			$dat['time']='1600';
			$this->load->view('admin/sysinfo.html',$dat);
		}
		$this->output->enable_profiler(false);
	}
}
?>