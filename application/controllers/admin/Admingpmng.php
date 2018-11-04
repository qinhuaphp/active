<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admingpmng extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model(array('admingroup_model','node_model'));
		$this->load->library('form_validation');
		$this->checklogin();
	}
	function addadmingp(){
		$array=$this->node_model->fetch_all(0,'nid,nname,remark,level,pid');
		$data['nodelist']=$this->node_model->fetch_nodes($array);
		//print_r($data['nodelist']);
		$this->form_validation->set_rules('gname','管理员组名称','trim|required|callback_check_gname');
		$this->form_validation->set_rules('nid[]','权限','required');
		$this->form_validation->set_rules('remark','备注','trim|required');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addadmingp.html',$data);
		}else{
			$array=array('gname'=>trim($this->input->post('gname',true)),
						'remark'=>trim($this->input->post('remark',true)),
						'is_normal'=>$this->input->post('is_normal',true),
						'nid'=>implode(',',$this->input->post('nid[]',true))
			);
			//print_r($array);exit;
			$res=$this->admingroup_model->add($array);
			if($res){
				$da['tip']='成功添加管理员组';
			}else{
				$da['tip']='添加管理员组失败';
			}
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}
		$this->output->enable_profiler(false);
	}
	function admingplist(){
		$data['grouplist']=$this->admingroup_model->fetch_all(0,'gid,gname,remark,is_normal');
		$this->load->view('admin/admingplist.html',$data);
	}
	/*编辑管理组的方法*/
	function editadmingp(){
		$curent=$this->node_model->fetch_one('nid',array('nname'=>'admin/admingpmng/editadmingp'));
		//print_r($curent);
		if(in_array($curent['nid'],$_SESSION['admin']['nids'])){
			$gid=$this->uri->segment(4)+0;
			$row=$this->admingroup_model->fetch_row('gid,gname,remark,is_normal,nid',$gid);
			$row['nid']=explode(',',$row['nid']);
			$data['groupinfo']=$row;
			$array=$this->node_model->fetch_all(0,'nid,nname,remark,level,pid');
			$data['nodelist']=$this->node_model->fetch_nodes($array);
			$this->form_validation->set_rules('nid[]','权限','required');
			$this->form_validation->set_rules('remark','备注','trim|required');
			if($this->form_validation->run() == FALSE){
				$this->form_validation->set_error_delimiters('<span>','</span>');
				$this->load->view('admin/editadmingp.html',$data);
			}else{
				$array=array('remark'=>trim($this->input->post('remark',true)),
							'is_normal'=>$this->input->post('is_normal',true),
							'nid'=>implode(',',$this->input->post('nid[]',true))
				);
				$res=$this->admingroup_model->renew($gid,$array);
				if($res){
					$da['tip']='成功编辑管理员组';
				}else{
					$da['tip']='未编辑管理员组';
				}
				$da['time']='1800';
				$this->load->view('admin/sysinfo.html',$da);
			}
			
		}
		else{
			$da['tip']='请联系管理员开通此权限';
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}
		
		$this->output->enable_profiler(false);
		
	}
	function check_gname($gname){
		$gname=trim($gname);
		$res=$this->admingroup_model->fetch_one('gname',array('gname'=>$gname));
		if(!empty($res)){
			$this->form_validation->set_message('check_gname','组名已存在');
			return false;
		}else{
			return true;
		}
	}
}
?>