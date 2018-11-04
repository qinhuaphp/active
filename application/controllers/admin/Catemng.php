<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Catemng extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->checklogin();
		$this->load->model('cate_model','cate');
	}
	function addcate(){
		$this->load->library('form_validation');
		$dat['catelist']=$this->cate->get_tree();
		//print_r($dat['catelist']);
		$this->form_validation->set_rules('cate_name','分类名称','trim|required|is_unique[cate.cate_name]');
		$this->form_validation->set_rules('cate_introl','分类简介','trim|required');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addcate.html',$dat);
		}else{
			$array=array(
			'cate_name'=>trim($this->input->post('cate_name',true)),
			'cate_introl'=>trim($this->input->post('cate_introl',true)),
			'pid'=>$this->input->post('pid',true)
			);
			$res=$this->cate->add($array);
			if($res){
				$data['tip']='添加分类成功';
				
			}else{
				$data['tip']='添加分类失败';
			}
			$data['time']='1800';
			$this->load->view('admin/sysinfo.html',$data);
		}
		//$this->output->enable_profiler(true);
	}
	function catelist(){
		$data['catelist']=$this->cate->get_tree();
		$this->load->view('admin/catelist.html',$data);
	}
	function editcate(){
		$this->load->model('node_model','node');
		$curent=$this->node->fetch_one('nid',array('nname'=>'admin/catemng/editcate'));
		if(in_array($curent['nid'],$_SESSION['admin']['nids'])){
			$this->load->library('form_validation');
			$cate_id=$this->uri->segment(4)+0;
			$array['cate_info']=$this->cate->fetch_row('cate_id,cate_name,cate_introl,pid',$cate_id);
			$array['catelist']=$this->cate->get_tree();
			/* $sub=$this->cate->get_sub($cate_id);
			print_r($sub); */
			$this->form_validation->set_rules('cate_name','分类名称','trim|required|is_unique[cate.cate_name]');
			$this->form_validation->set_rules('cate_introl','分类简介','trim|required');
			$this->form_validation->set_rules('pid','父级分类','required|callback_checkcate');
			if($this->form_validation->run() == FALSE){
				$this->form_validation->set_error_delimiters('<span>','</span>');
				$this->load->view('admin/editcate.html',$array);
			}else{
				$dat=array(
					'cate_name'=>trim($this->input->post('cate_name',true)),
					'pid'=>$this->input->post('pid',true),
					'cate_introl'=>trim($this->input->post('cate_introl',true))
				);
				$res=$this->cate->renew($cate_id,$dat);
				if($res > 0){
					$da['tip']='编辑分类成功';
				}else{
					$da['tip']='分类信息为被改动';
				}
				$da['time']='1800';
				$this->load->view('admin/sysinfo.html',$da);
			}
			$this->output->enable_profiler(false);
		}else{
			$da['tip']='请联系管理员开通此权限';
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}

	}
	//检查是否父类与子类互为父子关系
	function checkcate($pid){
		$cate_id=$this->uri->segment(4)+0;
		$sub=$this->cate->get_sub($cate_id);
		$subcate=array();
		foreach($sub as $v){
			$subcate[]=$v['cate_id'];
		}
		if($pid == $cate_id or in_array($pid,$subcate)){
			$this->form_validation->set_message('checkcate','子类和父类不能互为父子关系');
			return false;
		}else{
			return true;
		}
	}
}
?>