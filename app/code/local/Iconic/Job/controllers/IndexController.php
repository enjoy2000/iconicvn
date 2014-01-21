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
		}
		
	}

	public function afterforgotAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}