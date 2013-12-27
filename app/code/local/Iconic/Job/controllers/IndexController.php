<?php
class Iconic_Job_IndexController extends Mage_Core_Controller_Front_Action
{
	
	public function indexAction()
	{        
		$this->loadLayout();
       	$this->getLayout()->getBlock('head')->setTitle($this->__('Jobs Board For IconicVN')); 
		echo 'testing';       
        
		$this->renderLayout();    
	}
    
    public function newAction()
    {
        $this->loadLayout();        
        $this->renderLayout();        
        
        $time = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('customer/session');
            $session->setAfterAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $session->setBeforeAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $this->_redirect('customer/account/login/');
            return $this;
        }
        if($this->getRequest()->getPost()){
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            
            if((strtotime($this->getRequest()->getPost('deadlinetime')) < Mage::getModel('core/date')->timestamp(time())) || (strtotime($this->getRequest()->getPost('eventtime')) < Mage::getModel('core/date')->timestamp(time()))){
                Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('Your date input is invalid.'));
                $this->_redirect('*/*/new');
            }
            
            if(strtotime($this->getRequest()->getPost('deadlinetime')) >= strtotime($this->getRequest()->getPost('eventtime')) ){
                Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('The event date must be greater than the deadline.'));
                $this->_redirect('*/*/new');
            } 
            $diff = strtotime($this->getRequest()->getPost('eventtime')) - Mage::getModel('core/date')->timestamp(time());
            if($diff/31536000 > 2){
                Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('The event date cannot greater than 2 years from now.'));
                $this->_redirect('*/*/new');
            }
            if($customer->getBalance() >= (0.10 + $this->getRequest()->getPost('betAmount')) ){
                try{
                    $bet = Mage::getModel('job/bet');                    
                    $form = $this->getRequest()->getPost();
                    //echo $customer->getBalance() - 0.10 - $form['betAmount'];die;                    
                    $job = Mage::getModel('job/job');
                    $timeweight = Mage::helper('job')->getTimeWeight($form['deadlinetime']);
                    
                    if($form['betSide'] == 1){
                        $job->setAgree($form['betAmount']);
                        $job->setAgreeWeight($timeweight*$form['betAmount']);
                        $bet->setAgree($form['betAmount']);
                        $bet->setAgreeWeight($timeweight*$form['betAmount']);
                    }
                    elseif($form['betSide'] == 2){
                        $job->setDisagree($form['betAmount']);
                        $job->setDisagreeWeight($timeweight*$form['betAmount']);
                        $bet->setDisagree($form['betAmount']);
                        $bet->setDisagreeWeight($timeweight*$form['betAmount']);
                    }
                    else{
                        Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('You have to choose side to bet.'));
                        $this->_redirect('*/*/new');
                    }
                    
                    $job->setTitle($form['title'])
                            ->setContent($form['content'])
                            ->setDeadlineTime($form['deadlinetime'])
                            ->setEventTime($form['eventtime'])
                            ->setCreatedTime($time)
                            ->setCategory($form['category'])
                            ->setTotalBets($form['betAmount'])
                            ->setStatus('available')
                            ->setUserPost($customer->getId())
                            ->save();
                            
                    $customer->setBalance($customer->getBalance() - 0.10 - $form['betAmount'])->save();                    //write log
                    
                    $bet->setCustomerId($customer->getId())
                        ->setBetDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
                    if($job->isObjectNew()){
                        $bet->setJobId($job->getJobId())->save();
                        $log_txt = '<a href="'. Mage::getUrl('job/index/item?id='. $job->getJobId())  . '">Bet</a>';
                        $log_txt2 ='<a href="'. Mage::getUrl('job/index/item?id='. $job->getJobId())  . '">Submission Fee</a>';
                    }
                    $log = Mage::getModel('job/log');
                    $log->setCustomerId($customer->getId())
                        ->setAmount(-1*$this->getRequest()->getPost('betAmount'))
                        ->setCreatedDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))
                        ->setLog($log_txt)
                        ->save();
                    $log = Mage::getModel('job/log');
                    $log->setCustomerId($customer->getId())
                    ->setAmount(-0.010)
                    ->setCreatedDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))
                    ->setLog($log_txt2)
                    ->save();
                    $this->_redirect('*/*/');
                    
                } catch (Exception $e){
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                    $this->_redirect('*/*/new');
                }
            }
            else{
                Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('Your balance is not enough.'));
                $this->_redirect('*/*/new');
            }            
        }
    }
    
    public function itemAction()
    {
        $this->loadLayout();       
        if(!$this->getRequest()->getParam('id')){
            $this->_redirect('*/*/');
        }
        $item = Mage::getModel('job/job')->load($this->getRequest()->getParam('id')); 
                 
		$id = $this->getRequest()->getParam('id'); 
        if($this->getRequest()->getParam('id') >= 1){
            
            if(is_object($item)){
                $this->getLayout()->getBlock('job/item')->setData('item', $item);
            } 
        } 
        $this->renderLayout();                 
        
    }
    
    public function betAction()
    {
    	if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('customer/session');
            $session->setAfterAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $session->setBeforeAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $this->_redirect('customer/account/login/');
            return $this;
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $bet = Mage::getModel('job/bet');
        $log = Mage::getModel('job/log');
        $job = Mage::getModel('job/job');
        
        $log_txt = '<a href="'. Mage::getUrl('job/index/item?id='. $this->getRequest()->getPost('itemId'))  . '">Bet</a>';
        
        if($this->getRequest()->getPost()){
            $item = Mage::getModel('job/job')->load($this->getRequest()->getPost('itemId'));
            $bet->setCustomerId($customer->getId())
                ->setJobId($this->getRequest()->getPost('itemId'))
                ->setBetDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
            
            if($customer->getBalance() >= $this->getRequest()->getPost('amount')){
                if($this->getRequest()->getPost('betSide') == 1){
                    if($this->getRequest()->getPost('amount') % 0.010 != 0){                    
                        Mage::throwException('Your bet must be a multiple of 0.010.');
                    }
                    else{
                        $bet->setAgree($this->getRequest()->getPost('amount'))
                            ->setAgreeWeight(Mage::helper('job')->getTimeWeight($item->getDeadlineTime()) * $this->getRequest()->getPost('amount'))
                            ->save();
                        
                        $customer->setBalance($customer->getBalance() - $this->getRequest()->getPost('amount'))->save();
                        
                        $log->setCustomerId($customer->getId())
                            ->setAmount(-1*$this->getRequest()->getPost('amount'))
                            ->setCreatedDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))
                            ->setLog($log_txt)
                            ->save();
                        
                        $item->setAgree($job->getAgree() + $this->getRequest()->getPost('amount'))
                                ->setAgreeWeight($job->getAgreeWeight()+ Mage::helper('job')->getTimeWeight($item->getDeadlineTime()) * $this->getRequest()->getPost('amount'))
                                ->save();
                        
                        Mage::getSingleton('core/session')->addSuccess(Mage::helper('job')->__('You have successly bet on this statement.'));
                        $this->_redirect('*/*/item', array('id' => $this->getRequest()->getPost('itemId')));
                    }
                }
                elseif($this->getRequest()->getPost('betSide') == 2){
                    if($this->getRequest()->getPost('amount') % 0.010 != 0){                    
                        Mage::throwException('Your bet must be a multiple of 0.010.');
                    }
                    else{
                        $bet->setDisagree($this->getRequest()->getPost('amount'))
                            ->setDisagreeWeight(Mage::helper('job')->getTimeWeight($item->getDeadlineTime()) * $this->getRequest()->getPost('amount'))
                            ->save();
                        
                        $customer->setBalance($customer->getBalance() - $this->getRequest()->getPost('amount'))->save();
                        
                        $log->setCustomerId($customer->getId())
                            ->setAmount(-1*$this->getRequest()->getPost('amount'))
                            ->setCreatedDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))
                            ->setLog($log_txt)
                            ->save();
                        
                        $item->setDisagree($job->getDisagree() + $this->getRequest()->getPost('amount'))
                                ->setDisagreeWeight($job->getDisagreeWeight()+ Mage::helper('job')->getTimeWeight($item->getDeadlineTime()) * $this->getRequest()->getPost('amount'))
                                ->save();
                        Mage::getSingleton('core/session')->addSuccess(Mage::helper('job')->__('Your bet has successly submitted.'));
                        $this->_redirect('*/*/item?id='.$this->getRequest()->getPost('itemId'));
                    }
                }
                else{
                    Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('You have to choose bet side.'));
                    $this->_redirect('*/*/item?id='.$this->getRequest()->getPost('itemId'));
                }
            }
            else{
                Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('Not enough balance.'));
                $this->_redirect('*/*/item?id='.$this->getRequest()->getPost('itemId'));
            }
        }
    }
    
    public function statusAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        $conf_merchantAccountNumber = Mage::getSingleton('customer/session')->getCustomer()->getLraccount();
        $conf_merchantStoreName = "iconic-sci";
        $conf_merchantSecurityWord = "Frogface101@";
        
        $str = 
          $_REQUEST["lr_paidto"].":".
          $_REQUEST["lr_paidby"].":".
          stripslashes($_REQUEST["lr_store"]).":".
          $_REQUEST["lr_amnt"].":".
          $_REQUEST["lr_transfer"].":".
          $_REQUEST["lr_currency"].":".
          $conf_merchantSecurityWord;
          
          var_dump($_REQUEST["lr_paidto"]);
          var_dump($_REQUEST["lr_paidby"]);
          var_dump($_REQUEST["lr_store"]);
          var_dump($_REQUEST["lr_amnt"]);
          var_dump($_REQUEST["lr_transfer"]);
          var_dump($_REQUEST["lr_currency"]);
          
        //Calculating hash
        $hash = strtoupper(bin2hex(mhash(MHASH_SHA256, $str))); 
        
        //Let's check that all parameters exist and match and that the hash 
        //string we computed matches the hash string that was sent by LR system.
        if (isset($_REQUEST["lr_paidto"]) &&  
            $_REQUEST["lr_paidto"] == strtoupper($conf_merchantAccountNumber) &&
            isset($_REQUEST["lr_store"]) && 
            stripslashes($_REQUEST["lr_store"]) == $conf_merchantStoreName &&
            isset($_REQUEST["lr_encrypted"]) &&
            $_REQUEST["lr_encrypted"] == $hash) {
        
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customer->setBalance($customer->getBalance() + $_REQUEST['lr_amnt'])->save();
            $log = Mage::getModel('job/log');
            $log->setCustomerId($customer->getId())
                ->setAmount($_REQUEST['lr_amnt'])
                ->setCreatedDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))
                ->setLog('Deposit from Liberty Reserve')
                ->save();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('job')->__('Payment was verified and is successful.'));
            $this->_reidrect('customer/account/');
        }
    }
    
    public function successAction(){    	
    	$this->loadLayout();
        $this->renderLayout();   
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customer->setBalance($customer->getBalance() + $this->getRequest()->getPost('lr_amnt'))->save(); 
        $log = Mage::getModel('job/log');
        $log->setCustomerId($customer->getId())
            ->setAmount($_REQUEST['lr_amnt'])
            ->setCreatedDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))
            ->setLog('Deposit from Liberty Reserve')
            ->save();  
    }
    
    public function searchAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function changelraccountAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $this->_redirect('job/account/login');
        }
        
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if($this->getRequest()->getPost()){
            $password = $this->getRequest()->getPost('password');            
            $oldPass = $customer->getPasswordHash();
            if (Mage::helper('core/string')->strpos($oldPass, ':')) {
                list($_salt, $salt) = explode(':', $oldPass);
            } else {
                $salt = false;
            }
            if ($customer->hashPassword($password, $salt) != $oldPass) {
                Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('Password is incorrect.'));
                $this->_redirect('*/*/changelraccount');
                //redirect to a block or your own custom block in order to display the message
            }
            else{
                if(strlen($this->getRequest()->getPost('lraccount') == 8)){
                    $customer->setLraccount($this->getRequest()->getPost('lraccount'))
                    ->save();
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('job')->__('Your Liberty Reserve Account has been changed.'));
                    $this->_redirect('customer/account');
                }
                else{
                    Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('Your Liberty Reserve Account is invalid.'));
                    $this->_redirect('*/*/changelraccount');
                }
            }
        }
    }
    
    public function withdrawAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        
        require "LibertyReserveAPI.php";
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $this->_redirect('job/account/login');
        }
        
        $customer = Mage::getSingleton('customer/session')->getCustomer();        
        if($this->getRequest()->getPost()){
            $password = $this->getRequest()->getPost('password');            
            $oldPass = $customer->getPasswordHash();
            if (Mage::helper('core/string')->strpos($oldPass, ':')) {
                list($_salt, $salt) = explode(':', $oldPass);
            } else {
                $salt = false;
            }
            if ($customer->hashPassword($password, $salt) != $oldPass) {
                Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('Password is incorrect.'));
                $this->_redirect('*/*/withdraw');
                //redirect to a block or your own custom block in order to display the message
            }
            else{
                if(strlen($this->getRequest()->getPost('lrAmount') > 0)){
                    if($this->getRequest()->getPost('lrAmount') < $customer->getBalance()){
                        $auth = new Authentication("U0832731", "iconic-testing", '747c7fdce094cdf510dcd4697c3dfd82*&^#^%hfksdjhfsdKSD.&*#^@%*@^*^*TFGHBSFJHSGFJSBFKFJhfjksdhf37^&#(');
    
                        $payee = $customer->getLraccount();
                        $currency = "Usd";
                        $amount = $this->getRequest()->getPost('lrAmount');
                        $memo = "Withdraw";
                        $private = "false";
                        $purpose = "Salary";
                        $reference = "Reference1";
                        
                        $apiAgent = ApiAgentFactory::createApiAgent(ApiAgentFactory::JSON, $auth); 
                        $transfer = $apiAgent->transfer($payee, $currency, $amount, $private, $purpose, $reference, $memo);
                        
                        $customer->setBalance($customer->getBalance() - $this->getRequest()->getPost('lrAmount'))
                        ->save();
                        $log = Mage::getModel('job/log');
                        $log->setCustomerId($customer->getId())
                            ->setCreatedDate(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))
                            ->setAmount(-1*$this->getRequest()->getPost('lrAmount'))
                            ->setLog('Withdraw')
                            ->save(); 
                        Mage::getSingleton('core/session')->addSuccess(Mage::helper('job')->__('Your withdraw is success. Please check your Liberty Reserve Account.<br />If there is any problem, please <a href="malto:john@reid.cc">contact us</a>.'));
                        $this->_redirect('customer/account');
                    }
                    else{
                        Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('Your balance is not enough.'));
                        $this->_redirect('job/index/withdraw');
                    }
                }
                else{
                    Mage::getSingleton('core/session')->addError(Mage::helper('job')->__('You have to put valid amount.'));
                    $this->_redirect('*/*/withdraw');
                }
            }
        }
    }
}