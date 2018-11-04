<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Jurymng extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->checklogin();
		$this->load->model(array('jury_model','cate_model'));
		$this->load->library('form_validation');
	}
	function addjury(){
		$data['catelist']=$this->cate_model->get_tree();
		$this->form_validation->set_rules('username','账号','trim|required|callback_check_username|alpha_numeric|min_length[2]|max_length[30]');
		$this->form_validation->set_rules('telephone','手机号码','trim|required|callback_check_telephone');
		$this->form_validation->set_rules('emailbox','邮箱','trim|required|valid_email|callback_check_emailbox');
		$this->form_validation->set_rules('realname','真实姓名','trim|required');
		$this->form_validation->set_rules('pseudonym','笔名','trim|required');
		$this->form_validation->set_rules('introl','个人简介','trim|required');
		$this->form_validation->set_rules('resume','履历','trim|required');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addjury.html',$data);
		}else{
			$this->load->model('Systemsetup_model','systemsetup');
			$defaultpwd=$this->systemsetup->fetch_one('setupvalue',array('setupname'=>'defaultpwd'));
			//print_r($defaultpwd);
			$pwds=$this->jury_model->encrypt($defaultpwd['setupvalue']);//加密
			$array=array(
				'username'=>trim($this->input->post('username',true)),
				'password'=>$pwds['password'],
				'security_code'=>$pwds['security_code'],
				'telephone'=>trim($this->input->post('telephone',true)),
				'realname'=>trim($this->input->post('realname',true)),
				'pseudonym'=>trim($this->input->post('pseudonym',true)),
				'introl'=>trim($this->input->post('introl',true)),
				'resume'=>trim($this->input->post('resume',true)),
				'cate_id'=>$this->input->post('cate_id',true),
				'addtime'=>time(),
				'emailbox'=>trim($this->input->post('emailbox',true)),
				'is_normal'=>$this->input->post('is_normal',true)
			);
			//var_dump($array);
			$res=$this->jury_model->add($array);
			if($res){
				$da['tip']='成功添加评委';
			}else{
				$da['tip']='添加评委失败';
			}
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}
		$this->output->enable_profiler(false);
	}
	function jurylist(){
		$this->load->library('pagination');
		$config['base_url']=site_url('admin/jurymng/jurylist');
		$offset=intval($this->uri->segment(4));
		$where=array();
		$pagesize=6;
		$jurylist=$this->jury_model->get_jury_cate($pagesize,$offset,$where);//获取分页形式的数据
		//print_r($jurylist);
		$config['total_rows']=$jurylist['total'];
		$config['per_page']=$pagesize;
		$config['uri_segment']=4;
		$config['num_links']=2;
		//当该参数设置为 TRUE 时，会使用 application/config/config.php 配置文件中定义的 $config['url_suffix'] 参数 重写 $config['suffix'] 的值。
		$config['use_global_url_suffix']=true;//当全局伪静态设置后次项设不设置无所谓
		$config['first_link']='&lt&lt';
		$config['last_link']='&gt&gt';
		$config['next_link']='&gt';
		$config['prev_link']='&lt';
		$config['cur_tag_open']='<a class="links">';//设置当前页的标签
		$config['cur_tag_close']='</a>';
		$config['display_pages']=true;
		$config['attributes']=array('class'=>'links');//设置超链接的属性
		$this->pagination->initialize($config);
		$data['jurylist']=$jurylist['jurys'];
		$data['links']=$this->pagination->create_links();
		$this->load->view('admin/jurylist.html',$data);
		$this->output->enable_profiler(false);
	}
	function editjury(){
		$this->load->model('node_model','node');
		$curent=$this->node->fetch_one('nid',array('nname'=>'admin/jurymng/editjury'));
		if(in_array($curent['nid'],$_SESSION['admin']['nids'])){
			$jid=$this->uri->segment(4)+0;
			$data['catelist']=$this->cate_model->get_tree();
			$data['juryinfo']=$this->jury_model->fetch_row('jid,username,telephone,emailbox,realname,pseudonym,introl,resume,is_normal,cate_id',$jid);
			$this->form_validation->set_rules('telephone','手机号码','trim|required|callback_check_telephone');
			$this->form_validation->set_rules('emailbox','邮箱','trim|required|valid_email|callback_check_emailbox');
			$this->form_validation->set_rules('realname','真实姓名','trim|required');
			$this->form_validation->set_rules('pseudonym','笔名','trim|required');
			$this->form_validation->set_rules('introl','个人简介','trim|required');
			$this->form_validation->set_rules('resume','履历','trim|required');
			if($this->form_validation->run() == FALSE){
				$this->form_validation->set_error_delimiters('<span>','</span>');
				$this->load->view('admin/editjury.html',$data);
			}else{
				$array=array(
					'telephone'=>trim($this->input->post('telephone',true)),
					'realname'=>trim($this->input->post('realname',true)),
					'pseudonym'=>trim($this->input->post('pseudonym',true)),
					'introl'=>trim($this->input->post('introl',true)),
					'resume'=>trim($this->input->post('resume',true)),
					'cate_id'=>$this->input->post('cate_id',true),
					'emailbox'=>trim($this->input->post('emailbox',true)),
					'is_normal'=>$this->input->post('is_normal',true)
				);
				$res=$this->jury_model->renew($jid,$array);
				if($res > 0){
					$da['tip']='成功编辑评委信息';
				}else{
					$da['tip']='编辑评委信息失败';
				}
				$da['time']='1800';
				$this->load->view('admin/sysinfo.html',$da);
			}
		}else{
			$da['tip']='请联系管理员开通此权限';
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}
		
		$this->output->enable_profiler(false);
	}
	function resetpwd(){
		if($this->input->is_ajax_request()){
			$this->load->model('node_model','node');
			$curent=$this->node->fetch_one('nid',array('nname'=>'admin/jurymng/resetpwd'));
			if(in_array($curent['nid'],$_SESSION['admin']['nids'])){
				$jid=$this->uri->segment(4)+0;
				$this->load->model('Systemsetup_model','systemsetup');
				$defaultpwd=$this->systemsetup->fetch_one('setupvalue',array('setupname'=>'defaultpwd'));
				$pwds=$this->jury_model->encrypt($defaultpwd['setupvalue']);
				$res=$this->jury_model->renew($jid,$pwds);
				if($res > 0){
					echo '1';
				}else{
					echo '0';
				}
			}else{
				echo '2';
			}
		}
	}
	function check_username($username){
		$username=trim($username);
		$res=$this->jury_model->fetch_one('username',array('username'=>$username));
		if(!empty($res)){
			$this->form_validation->set_message('check_username','账号已存在');
			return false;
		}else{
			return true;
		}
	}
	function check_telephone($telephone){
		$telephone=trim($telephone);
		$rs=preg_match('/^18(\d{9})$|^12(\d{9})$|^17(\d{9})$|^15(\d{9})$|^13(\d{9})$|^147(\d{8})$/',$telephone);
		//var_dump($rs) ;
		if($rs == 0){
			$this->form_validation->set_message('check_telephone','请输入正确格式的手机号码');
			return false;
		}else{
			$res=$this->jury_model->fetch_one('telephone',array('telephone'=>$telephone));
			if(!empty($res)){
				$this->form_validation->set_message('check_telephone','手机号码已存在');
				return false;
			}else{
				return true;
			}
		}	
	}
	function check_emailbox($email){
		$email=trim($email);
		$res=$this->jury_model->fetch_one('emailbox',array('emailbox'=>$email));
		if(!empty($res)){
			$this->form_validation->set_message('check_emailbox','邮箱已存在');
			return false;
		}else{
			return true;
		}
	}
}
?>