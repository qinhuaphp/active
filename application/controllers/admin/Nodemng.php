<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Nodemng extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->checklogin();
		$this->load->model('node_model','node');
		$this->load->library('form_validation');
	}
	function addnode(){
		$data['parentnodes']=$this->node->fetch_all(array('pid'=>'0'),'nid,remark,level,pid');
		//print_r($data['parentnodes']);exit;
		$this->form_validation->set_rules('nname','权限名称','trim|required|callback_check_nname');
		$this->form_validation->set_rules('remark','备注','trim|required');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addnode.html',$data);
		}else{
			$array=array('nname'=>trim($this->input->post('nname',true)),
						'remark'=>trim($this->input->post('remark',true)),
						'level'=>$this->input->post('level',true),
						'pid'=>$this->input->post('pid',true)
			);
			//print_r($array);exit;
			$res=$this->node->add($array);
			if($res){
				$da['tip']='成功添加权限';
			}else{
				$da['tip']='添加权限失败';
			}
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}
		$this->output->enable_profiler(false);
	}
	function nodelist(){
		$array=$this->node->fetch_all(0,'nid,nname,remark,level,pid');
		$data['nodelist']=$this->node->fetch_nodes($array);
		$this->load->view('admin/nodelist.html',$data);
	}

	function check_nname($nname){
		$nname=trim($nname);
		$res=$this->node->fetch_one('nname',array('nname'=>$nname));
		if(!empty($res)){
			$this->form_validation->set_message('check_nname','权限名称已存在');
			return false;
		}else{
			return true;
		}
	}
}
?>