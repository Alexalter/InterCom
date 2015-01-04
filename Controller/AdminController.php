<?php
/**
 * InterCom Module for Zikula
 *
 * @copyright  InterCom Team
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package    InterCom
 * @subpackage User
 *
 * Please see the CREDITS.txt file distributed with this source code for further
 * information regarding copyright.
 */

namespace Zikula\IntercomModule\Controller;

use ModUtil;
use System;
use SecurityUtil;
use ServiceUtil;
use UserUtil;
use Zikula\Core\Controller\AbstractController;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // used in annotations - do not remove
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; // used in annotations - do not remove
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

use Zikula\IntercomModule\Util\Tools;
use Zikula\IntercomModule\Util\Settings;

/**
 * @Route("/admin")
 */
class AdminController extends \Zikula_AbstractController
{ 
    public function postInitialize()
    {
        $this->view->setCaching(false);
    }

    /**
     * @Route("")
     *
     * the main administration function
     *
     * @return RedirectResponse
     */
    public function indexAction(Request $request)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        
        $inbox = $this->entityManager
                    ->getRepository('Zikula\IntercomModule\Entity\MessageEntity')
                    ->getAll(array('inbox' => 1, 'countonly' => true));
        if(is_null($inbox) || !$inbox) {
            $inbox = 0;
        }
   
        $outbox  = $this->entityManager
                    ->getRepository('Zikula\IntercomModule\Entity\MessageEntity')
                    ->getAll(array('outbox' => 1, 'countonly' => true));
        if(is_null($outbox) || !$outbox) {
            $outbox = 0;
        }
        $archive = $this->entityManager
                    ->getRepository('Zikula\IntercomModule\Entity\MessageEntity')
                    ->getAll(array('stored' => 1, 'countonly' => true));
        if(is_null($archive) || !$archive) {
            $archive = 0;
        }
        
        $this->view->assign(array('inbox'              => $inbox,
                'outbox'             => $outbox,
                'archive'            => $archive));

        // Return the output that has been generated by this function
        return new Response($this->view->fetch('Admin/index.tpl'));
    }

    /**
     * @Route("/preferences")
     *
     * @return Response symfony response object
     *
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     */
    public function preferencesAction(Request $request)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }      
        if ($request->isMethod('Post')){
            $this->checkCsrfToken();
             $s = array(
            //general settings
            'active'=> $request->request->get('active',false),
            'maintain'=> $request->request->get('maintain',false),
            'allowhtml'=> $request->request->get('allowhtml',false),
            'allowsmilies'=> $request->request->get('allowsmilies',false),
            'disable_ajax'=> $request->request->get('disable_ajax',false),                 
            //Limitations
            'limitarchive'=> $request->request->get('limitarchive',false),
            'limitoutbox'=> $request->request->get('limitoutbox',false),
            'limitinbox'=> $request->request->get('limitinbox',false),
            'perpage'=> $request->request->get('perpage',false),                 
            //protection
            'protection_on'=> $request->request->get('protection_on',false),
            'protection_time'=> $request->request->get('protection_time',false),
            'protection_amount'=> $request->request->get('protection_amount',false),
            'protection_mail'=> $request->request->get('protection_mail',false),
            //user prompt
            'userprompt'=> $request->request->get('userprompt',false),
            'userprompt_display'=> $request->request->get('userprompt_display',false),
            //Welcome
            'welcomemessage_send'=> $request->request->get('welcomemessage_send',false),                 
            'welcomemessagesender'=> $request->request->get('welcomemessagesender',false),
            'welcomemessagesubject'=> $request->request->get('welcomemessagesubject',false),  
            'welcomemessage'=> $request->request->get('welcomemessage',false),
            'intlwelcomemessage'=> $request->request->get('intlwelcomemessage',false),
            'savewelcomemessage'=> $request->request->get('savewelcomemessage',false),              
            //Email
            'allow_emailnotification'=> $request->request->get('allow_emailnotification',false),
            'force_emailnotification'=> $request->request->get('force_emailnotification',false),
            'mailsender'=> $request->request->get('mailsender',false),
            'mailsubject'=> $request->request->get('mailsubject',false),
            'fromname'=> $request->request->get('fromname',false),
            'from_email'=> $request->request->get('from_email',false),
            //Autoreply
            'allow_autoreply'=> $request->request->get('allow_autoreply',false),
            ); 
            /* 
            echo('<pre>');
            var_dump($s);
            exit(0);*/
            $settings = new Settings();
            $settings->setNewData($s);
            if (!$settings->isValid()){
                $this->view->assign($settings->getNewData());
                $this->view->assign('errors' ,$settings->getErrors());
                return new Response($this->view->fetch('Admin/modifyconfig.tpl'));            
            }                    
            $settings->save();
            return new RedirectResponse($this->get('router')->generate('zikulaintercommodule_admin_preferences', array(), RouterInterface::ABSOLUTE_URL));         
        }    
        // assign all the module vars
        $this->view->assign($this->getVars());
        // Return the output that has been generated by this function
        return new Response($this->view->fetch('Admin/modifyconfig.tpl'));
    }
    
    /**
     * @Route("/tools")
     *
     * @return Response symfony response object
     *
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     */
    public function toolsAction(Request $request)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }        
        $tools = new Tools();
        // Get parameters
        $operation = $request->query->get('operation', false);
        if ($operation === false){
            //get check
            $this->view->assign('users_check', $tools->checkIntegrityUsers());
            $this->view->assign('orphaned', $tools->checkIntegrityOrphaned());
            $this->view->assign('inboxes', $tools->checkIntegrityInbox());
            $this->view->assign('outboxes', $tools->checkIntegrityOutbox());
            $this->view->assign('archives', $tools->checkIntegrityArchive());      
            return new Response($this->view->fetch('Admin/tools.tpl'));        
        }

        // to do: better information to the user if the action was successful or not! - DONE
        switch ($operation) {
            case "fix_integrity_users":
                if ($tools->fixIntegrityUsers()) {                   
                    $request->getSession()->getFlashBag()->add('status', $this->__('Done! users integrity fixed.'));
                } else {
                    $request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not fix users data integrity.'));
                }
                break;
            case "fix_integrity_inbox":
                if ($tools->fixIntegrityInbox()) {
                    $request->getSession()->getFlashBag()->add('status', $this->__('Done! inboxes integrity fixed.'));
                } else {
                    $request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not fix inbox data integrity..'));
                }
                break;
            case "fix_integrity_outbox":
                if ($tools->fixIntegrityOutbox()) {
                    $request->getSession()->getFlashBag()->add('status', $this->__('Done! outboxes integrity fixed.'));
                } else {
                    $request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not fix outbox data integrity.'));
                }
                break;
            case "fix_integrity_archive":
                if ($tools->fixIntegrityArchive()) {
                    $request->getSession()->getFlashBag()->add('status', $this->__('Done! archives integrity fixed.'));
                } else {
                    $request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not fix users archive integrity.'));
                }
                break;
            case "reset_to_defaults":
                if ($tools->resetSettings()) {
                    $request->getSession()->getFlashbag()->add('status', $this->__('Done! Reset settings to default values.'));  
                } else {
                    $request->getSession()->getFlashbag()->add('error', $this->__('Error! Could not reset settings to default values.'));             
                }                
                break;
            case "delete_inboxes":
                if ($tools->deleteInboxes()) {
                    $request->getSession()->getFlashBag()->add('status', $this->__('Done! Emptied inboxes.'));
                } else {
                    $request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not empty inboxes.'));
                }              
                break;                
            case "delete_outboxes":
                if ($tools->deleteOutboxes()) {
                    $request->getSession()->getFlashBag()->add('status', $this->__('Done! Emptied outboxes.'));
                } else {
                    $request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not empty outboxes.'));
                }               
                break;
            case "delete_archive":
                if ($tools->deleteArchive()) {
                    $request->getSession()->getFlashBag()->add('status', $this->__('Done! Emptied archives.'));
                } else {
                    $request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not empty archives.'));
                }
                break;
            case "delete_all":
                if ($tools->deleteAll()) {
                    $request->getSession()->getFlashBag()->add('status', $this->__('Done! Deleted all messages.'));
                } else {
                    $request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not delete all messages.'));
                }
                break;
            default:
                return new RedirectResponse($this->get('router')->generate('zikulaintercommodule_admin_tools', array(), RouterInterface::ABSOLUTE_URL));
        }
    return new RedirectResponse($this->get('router')->generate('zikulaintercommodule_admin_tools', array(), RouterInterface::ABSOLUTE_URL));        
    }
}