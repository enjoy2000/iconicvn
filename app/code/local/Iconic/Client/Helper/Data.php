<?php
class Iconic_Client_Helper_Data extends Mage_Core_Helper_Abstract
{
	/* Check the site is for Employer or not */
	public function isEmployerSite(){
		return true;
	}
	
	/* Check locale is Japan or not */
	public function isJapanese(){
		return true;
	}
}