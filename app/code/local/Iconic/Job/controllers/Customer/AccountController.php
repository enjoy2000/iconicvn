<?php
# Controllers are not autoloaded so we will have to do it manually:
require_once 'Mage/Customer/controllers/AccountController.php';
class Iconic_Job_Customer_AccountController extends Mage_Customer_AccountController
{
	
    # Overloaded createPostAction
    public function createPostAction()
    {
        /** @var $session Mage_Customer_Model_Session */
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if (!$this->getRequest()->isPost()) {
            $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
            $this->_redirectError($errUrl);
            return;
        }

        $customer = $this->_getCustomer();

        try {
            $errors = $this->_getCustomerErrors($customer);

            if (empty($errors)) {
            	$data = $this->getRequest()->getPost();
				$customer->setData($data);
                $customer->save();
                $this->_dispatchRegisterSuccess($customer);
				//success action
				$session = $this->_getSession();
		        if ($customer->isConfirmationRequired()) {
		            /** @var $app Mage_Core_Model_App */
		            $app = $this->_getApp();
		            /** @var $store  Mage_Core_Model_Store*/
		            $store = $app->getStore();
		            $customer->sendNewAccountEmail(
		                'confirmation',
		                $session->getBeforeAuthUrl(),
		                $store->getId()
		            );
		            $customerHelper = $this->_getHelper('customer');
		            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
		                $customerHelper->getEmailConfirmationUrl($customer->getEmail())));
		            $url = $this->_getUrl('*/*/index', array('_secure' => true));
		        } else {
		            $session->setCustomerAsLoggedIn($customer);
		            $session->renewSession();
		            $url = $this->_welcomeCustomer($customer);
		        }
                $this->_redirect('job/index/afterregister');
                return;
            } else {
                $this->_addSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl(Mage::helper('job')->getForgotUrl());
                $message = $this->__('Email này đã được dùng để đăng ký. hãy bấm vào <a href="%s">đây</a> để lấy lại mật khẩu và quyền truy cập tài khoản.', $url);
                $session->setEscapeMessages(false);
            } else {
                $message = $e->getMessage();
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Cannot save the customer.'));
        }
        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
        $this->_redirectError($errUrl);
    }

	
    /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken =  $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('*/*/forgotpassword');
                    return;
                }
            }
            $this->_redirect('job/index/afterforgot', array('m'=>$email));
			
            return;
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->_redirect('*/*/forgotpassword');
            return;
        }
    }

	/**
     * Default customer account page
     */
    public function indexAction()
    {
    	if(Mage::helper('client')->isEmployerSite()){
			$this->_redirect('client/job/manage');
			return;
		}else{
			$this->_redirect('/');
			return;
		}
    	
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/account_dashboard')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }
	
	/**
     * Change customer password action
     */
    public function editPostAction()
    {
    	if(!Mage::helper('client')->isEmployerSite()){
    		header("Location: ".Mage::helper('job')->getUrl().'cong-ty/');
    		die;
    	}
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
        	$customer = $this->_getSession()->getCustomer();
			
            // If password change was requested then add it to common validation scheme
            if ($this->getRequest()->getParam('confirmation')) {
                $currPass   = $this->getRequest()->getPost('current_password');
                $newPass    = $this->getRequest()->getPost('password');
                $confPass   = $this->getRequest()->getPost('confirmation');

                $oldPass = $customer->getPasswordHash();
                if ( $this->_getHelper('core/string')->strpos($oldPass, ':')) {
                    list($_salt, $salt) = explode(':', $oldPass);
                } else {
                    $salt = false;
                }

                if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                    if (strlen($newPass)) {
                        /**
                         * Set entered password and its confirmation - they
                         * will be validated later to match each other and be of right length
                         */
                        $customer->setPassword($newPass);
                        $customer->setConfirmation($confPass);
						
						// Validate account and compose list of errors if any
		                $customerErrors = $customer->validate();
		                if (is_array($customerErrors)) {
		                	foreach($customerErrors as $error){
		                    	$this->_getSession()->addError($error);
		                	}
		                	$this->_redirect('*/*/edit', array('changepass'=>1));
							return;
		                }
                    } else {
						$this->_getSession()->addError($this->__('Bạn phải điền mật khẩu mới'));
	                	$this->_redirect('*/*/edit', array('changepass'=>1));
						return;
                    }
                } else {
					$this->_getSession()->addError($this->__('Mật khẩu không đúng'));
                	$this->_redirect('*/*/edit', array('changepass'=>1));
					return;
                }
			}else{
				$data = $this->getRequest()->getPost();
				/* Check data POST enough or not */
				if($data['company_name'] && $data['company_address'] && $data['company_detail'] && $data['firstname']){
					$customer->setCompanyLogo($data['company_logo'])
							->setCompanyName($data['company_name'])
							->setCompanyAddress($data['company_address'])
							->setCompanySize($data['company_size'])
							->setCompanyWebsite($data['company_website'])
							->setCompanyDetail($data['company_detail'])
							->setFirstname($data['firstname']);
					/* Check Url Structure */
					if($data['company_website'] && !filter_var($data['company_website'],FILTER_VALIDATE_URL)){
						$this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
						$this->_getSession()->addError($this->__('Invalid website URL.'));
	                	$this->_redirect('*/*/edit');
						return;
					}
					/* Check logo image exist or not */
					if(!file_exists(Mage::getBaseDir().'/media/logo/'.$data['company_logo'])){
						$this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
						$this->_getSession()->addError($this->__('Bạn chưa tải lên logo công ty.'));
	                	$this->_redirect('*/*/edit');
						return;
					}
				}else{
                	$this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
					$this->_getSession()->addError($this->__('Không đầy đủ thông tin'));
                	$this->_redirect('*/*/edit');
					return;
				}
				
				// Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                	foreach($customerErrors as $error){
                    	$this->_getSession()->addError($error);
                	}
                	$this->_redirect('*/*/edit');
					return;
                }
			}

            try {
                $customer->save();
                $this->_getSession()->setCustomer($customer);
                $this->_getSession()->addSuccess($this->__('Thay đổi thông tin thành công'));
                $this->_getSession()->setCustomerFormData(false);
                $this->_redirect('*/*/edit');
                return;
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirect('*/*/edit');
    }
}