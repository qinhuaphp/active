<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Adminsmng extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->checklogin();
		$this->load->model(array('admins_model','systemsetup_model','admingroup_model'));
		$this->load->library('form_validation');
		
	}
	function addadmin(){
		$data['grouplist']=$this->admingroup_model->fetch_all(array('is_normal'=>'1'),'gid,gname');
		$this->form_validation->set_rules('admin_name','管理员账号','trim|required|callback_check_adminname|alpha_numeric|min_length[2]|max_length[45]');
		$this->form_validation->set_rules('remark','备注','trim|required');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addadmin.html',$data);
		}else{
			$pwd=$this->systemsetup_model->fetch_one('setupvalue',array('setupname'=>'defaultpwd'));
			//echo $pwd['setupvalue'];
			$pwds=$this->admins_model->encrypt($pwd['setupvalue']);
			$array=array('admin_name'=>trim($this->input->post('admin_name',true)),
						'remark'=>trim($this->input->post('remark',true)),
						'is_normal'=>$this->input->post('is_normal',true),
						'gid'=>$this->input->post('gid',true),
						'password'=>$pwds['password'],
						'security_code'=>$pwds['security_code'],
						'created_at'=>time()
			);
			//var_dump($array);
			$res=$this->admins_model->add($array);
			if($res){
				$da['tip']='成功添加管理员';
			}else{
				$da['tip']='添加管理员失败';
			}
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}
		$this->output->enable_profiler(false);
	}
	function adminlist(){
		$data['adminlist']=$this->admins_model->admin_group();
		//print_r($data['adminlist']);
		$this->load->view('admin/adminlist.html',$data);
		$this->output->enable_profiler(false);
	}
	function editadmin(){
		$this->load->model('node_model','node');
		$curent=$this->node->fetch_one('nid',array('nname'=>'admin/adminsmng/editadmin'));
		if(in_array($curent['nid'],$_SESSION['admin']['nids'])){
			$admin_id=$this->uri->segment(4)+0;
			$data['grouplist']=$this->admingroup_model->fetch_all(array('is_normal'=>'1'),'gid,gname');
			$data['admininfo']=$this->admins_model->fetch_row('admin_id,admin_name,is_normal,remark,gid',$admin_id);
			$this->form_validation->set_rules('remark','备注','trim|required');
			if($this->form_validation->run() == FALSE){
				$this->form_validation->set_error_delimiters('<span>','</span>');
				$this->load->view('admin/editadmin.html',$data);
			}else{
				$array=array('is_normal'=>$this->input->post('is_normal',true),
							'remark'=>trim($this->input->post('remark',true)),
							'gid'=>$this->input->post('gid',true)
				);
				$res=$this->admins_model->renew($admin_id,$array);
				if($res > 0){
					$da['tip']='成功编辑管理员信息';
				}else{
					$da['tip']='编辑管理员信息失败';
				}
				$da['time']='1800';
				$this->load->view('admin/sysinfo.html',$da);
			}
		}else{
			$da['tip']='请联系管理员开通此权限';
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}
		
	}
	function resetpwd(){
		if($this->input->is_ajax_request()){
			$this->load->model('node_model','node');
			$curent=$this->node->fetch_one('nid',array('nname'=>'admin/adminsmng/resetpwd'));
			if(in_array($curent['nid'],$_SESSION['admin']['nids'])){
				$admin_id=$this->uri->segment(4)+0;
				$defaultpwd=$this->systemsetup_model->fetch_one('setupvalue',array('setupname'=>'defaultpwd'));
				$pwds=$this->admins_model->encrypt($defaultpwd['setupvalue']);
				$res=$this->admins_model->renew($admin_id,$pwds);
				if($res > 0){
					echo '1';
				}else{
					echo '0';
				}
			}
			else{
				echo '2';
			}
			
		}
	}
	function check_adminname($name){
		$name=trim($name);
		$res=$this->admins_model->fetch_one('admin_name',array('admin_name'=>$name));
		if(!empty($res)){
			$this->form_validation->set_message('check_adminname','管理员账号已存在');
			return false;
		}else{
			return true;
		}
	}
}
?>