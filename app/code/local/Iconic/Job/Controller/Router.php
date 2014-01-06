<?php
/**
 * CommerceLab Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the CommerceLab License Agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://commerce-lab.com/LICENSE.txt
 *
 * @category   CommerceLab
 * @package    CommerceLab_News
 * @copyright  Copyright (c) 2012 CommerceLab Co. (http://commerce-lab.com)
 * @license    http://commerce-lab.com/LICENSE.txt
 */

class Iconic_Job_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('job', $this);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::app()->isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        $route = Mage::helper('job')->getRoute();

        $identifier = $request->getPathInfo();



        if (substr(str_replace("/", "", $identifier), 0, strlen($route)) != $route) {
            return false;
        }


        $identifier = substr_replace($request->getPathInfo(), '', 0, strlen("/" . $route. "/"));
        $identifier = str_replace(Mage::helper('clnews')->getNewsitemUrlSuffix(), '', $identifier);
        list($parentCategoryKey, $categoryKey, $jobKey) = explode("/", $identifier);

        $job = Mage::getModel('job/job')->load($jobKey, 'url_key');

        if($job->getId()){
            $request
                ->setModuleName('job')
                ->setControllerName('details')
                ->setActionName('index')
                ->setParam('id', $job->getId());
                return true;
        }

        return false;
    }
}
