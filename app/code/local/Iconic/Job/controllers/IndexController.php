<?php
class Iconic_Job_IndexController extends Mage_Core_Controller_Front_Action
{
	
	public function indexAction(){        
		$this->loadLayout();
       	$this->getLayout()->getBlock('head')->setTitle($this->__('Jobs Board For IconicVN')); 
		Mage::helper('job')->redirectToSearchPage();
    }
	
	public function contactAction(){
		$post = $this->getRequest()->getPost();
		if($post['email'] && $post['subject'] && $post['message']){
			try{
				$mail = new Zend_Mail('UTF-8');
				$nameAdmin = Mage::getStoreConfig('trans_email/ident_general/name'); 
				$emailAdmin = Mage::getStoreConfig('trans_email/ident_general/email');
				
				
				$config = array(
		                    'auth' => 'login',
		                    'ssl'  => 'tls',
						    'port' => 587,
						    'username' => 'test',
						    'password' => 'testing'
							);
		 
				$transport = new Zend_Mail_Transport_Smtp('mail.iconicvn.com', $config);
				
				
				$bodyHtml = '<table><tbody>';
				$bodyHtml .= '<tr><td align="center" colspan="2">' . Mage::helper('job')->__('Liên lạc từ IconicVN') . '</td></tr>';
				$bodyHtml .= '<tr><td>'.Mage::helper('job')->__('Email').':</td><td> '.$post['email'].'</td></tr>';
				$bodyHtml .= '<tr><td>'.Mage::helper('job')->__('Nội dung').':</td><td> '.$post['message'].'</td></tr>';
				$bodyHtml .= '</tbody></table>';
				
				$mail->setBodyHtml($bodyHtml);
				$mail->addTo('info@iconic-intl.com', Mage::helper('job')->__('IconicVN'));
				$mail->setFrom($post['email'], $post['email']);
				$mail->setSubject($post['subject']);
				$checkSend = $mail->send($transport);
				if($checkSend){
					echo Mage::helper('job')->__('Email của bạn đã được gửi thành công. Cảm ơn.');
				}
			}catch(Exception $e){
				echo Mage::helper('job')->__('Đã có lỗi xảy ra. Xin vui lòng thử lại sau.');
			}
		}else{
			$this->_redirect('/');
			return;
		}
		
	}

	public function afterforgotAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function afterregisterAction(){
		$this->loadLayout();
		//set breadcrumbs		
		$helper = Mage::helper('job');
		/*
		if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
			$breadcrumbs->addCrumb('home', array('label'=>$helper->__('Trang chủ'), 'title'=>$helper->__('Trang chủ'), 'link'=>Mage::getBaseUrl()));
			$breadcrumbs->addCrumb('search_results', array('label'=>$helper->__('Đăng ký'), 'title'=>$helper->__('Đăng ký'), 'link'=>Mage::getUrl(Mage::helper('job')->getRegisterUrl())));
		}
		*/
		$this->renderLayout();
	}
	
	public function createcvAction(){
		$this->loadLayout();
		// redirect if user not login 
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('customer/session');
            $session->setAfterAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $session->setBeforeAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $this->_redirect(Mage::helper('job')->getLoginUrl());
            return $this;
        }
		if($this->getRequest()->getPost()){
			$data = $this->getRequest()->getPost();
			$birthday = $data['day'].'/'.$data['month'].'/'.$data['year'];
			//explode the date to get month, day and year
			$birthDate = explode("/", $birthday);
			//get age from date or birthdate
			$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[0], $birthDate[2]))) > date("md")
				? ((date("Y") - $birthDate[2]) - 1)
				: (date("Y") - $birthDate[2]));
			$now = date('d/m/Y');
			$wish = 'Ngành nghề: ' . $data['category'];
			$wish .= ' -- Chức năng: ' . $data['function'];
			$wish .= ' -- Mức lương: ' . $data['salary2'].$data['currency2'].'('.$data['salarytype2'].')';
			$wish .= ' -- Địa điểm: ' . $data['location2'];
			$wish .= ' -- Cấp độ: ' . $data['level']; 
			$arr = array($data['ho'].' '.$data['ten'], '', $age, $data['sex'], $birthday, $data['address'], '', '', $data['phone'], $data['nation'], $data['email'], $data['school'].'/'.$data['spec'], '~'.$data['graduate'], $data['jp'], $data['jp'], $data['jp'], $data['en'], $data['en'], $data['en'], $data['vn'], $data['vn'], $data['vn'], '', '', $data['skill'], $data['decide'], $data['decide'], $data['salary'].$data['currency'].'('.$data['salarytype'].')', '', $data['category2'], $data['function2'], $wish, '', $now);
			
			//Write Excel File
			/** PHPExcel */
			include Mage::getBaseDir().'/lib/PHPExcel.php';
			/** PHPExcel_Writer_Excel2007 */
			include Mage::getBaseDir().'/lib/PHPExcel/Writer/Excel2007.php';
			$locationFile = Mage::getBaseDir()."/tmp/".Mage::getSingleton('customer/session')->getCustomer()->getEmail().date('Ymdhis').".xlsx";
			Mage::helper('job')->writeExcel($locationFile,$arr);
			
			//Send mail
			$mail = new Zend_Mail('UTF-8');
			$at = new Zend_Mime_Part(file_get_contents($locationFile));
			$at->filename = basename($locationFile);
			$at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
			$at->encoding = Zend_Mime::ENCODING_BASE64;
			$mail->addAttachment($at);
			$config = array(
	                    'auth' => 'login',
	                    'ssl'  => 'tls',
					    'port' => 587,
					    'username' => 'test',
					    'password' => 'testing'
						);
	 
			$transport = new Zend_Mail_Transport_Smtp('mail.iconicvn.com', $config);
			//get general contact from config admin
			/* Sender Name */
			$nameAdmin = Mage::getStoreConfig('trans_email/ident_general/name'); 
			/* Sender Email */
			$emailAdmin = Mage::getStoreConfig('trans_email/ident_general/email');
			
			$bodyHtml = '<table><tbody>';			
			$bodyHtml .= '<tr><td>'.Mage::helper('job')->__('CV for importing to IS.').':</td></tr>';
			$bodyHtml .= '</tbody></table>';
			
			$mail->setBodyHtml($bodyHtml);
			$mail->addTo('auto_iconic_vn@iconic-intl.com',Mage::helper('job')->__('IconicVN'));
			$mail->setFrom('info@iconicvn.com', Mage::helper('job')->__('IconicVN'));
			$mail->setSubject(Mage::helper('job')->__('[IS] CV của %s %s', $data['ho'], $data['ten']));
			$checkSend = $mail->send($transport);
			if($checkSend){
				$this->_redirect('/');return;
			}else{
				echo 123;
			}
		}
		$this->renderLayout();
	}
}