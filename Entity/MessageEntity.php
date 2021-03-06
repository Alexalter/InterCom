<?php

/**
 * Intercom
 *
 * @copyright (c) 2001-now, Intercom Development Team
 * @link https://github.com/zikula-modules/Intercom
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Intercom
 */

namespace Zikula\IntercomModule\Entity;

use ServiceUtil;
use ModUtil;
use DateTime;
use UserUtil;
use System;
use Zikula\Core\Doctrine\EntityAccess;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Message entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="intercom")
 * @ORM\Entity(repositoryClass="Zikula\IntercomModule\Entity\Repository\MessageRepository")
 */
class MessageEntity extends EntityAccess
{
    /**
     * Module name
     * @var string
     */
    const MODULENAME = 'ZikulaIntercomModule';

    /**
     * id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The sender user
     *     
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="sender", referencedColumnName="uid")
     */
    private $sender;

    /**
     * The recipient uid
     *     
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="recipient", referencedColumnName="uid")
     */
    private $recipient;
    
    /**
     * subject
     *
     * @ORM\Column(type="string", length=255)
     */
    private $subject = '';    

    /**
     * send
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $send;

    /**
     * text
     *
     * @ORM\Column(type="text")
     */
    private $text = '';
    
    /**
     * seen
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default":null})
     */
    private $seen;

    /**
     * replied
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default":null})
     */
    private $replied;
    
    /**
     * notified
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default":null})
     */
    private $notified;
    
    /**
     * inbox
     *
     * @ORM\Column(type="boolean")
     */
    private $inbox;
    
    /**
     * outbox
     *
     * @ORM\Column(type="boolean")
     */
    private $outbox;
    
    /**
     * storedbysender
     *
     * @ORM\Column(type="boolean")
     */
    private $storedbysender;
    
    /**
     * storedbyrecipient
     *
     * @ORM\Column(type="boolean")
     */
    private $storedbyrecipient;

    /**
     * @ORM\ManyToOne(targetEntity="MessageEntity", inversedBy="conversation")
     * @ORM\JoinColumn(name="conversationid", referencedColumnName="id")
     **/
    private $conversationid; 
    
    /**
     * @ORM\OneToMany(targetEntity="MessageEntity", mappedBy="conversationid")
     * @ORM\OrderBy({"send" = "ASC"})
     **/
    private $conversation;    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conversationid = null;        
        $this->conversation = new \Doctrine\Common\Collections\ArrayCollection();        
        $this->seen = null;
        $this->inbox = 1;
        $this->outbox = 1;
        $this->storedbysender = 0;
        $this->storedbyrecipient = 0;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set sender
     *
     * @param UserEntity $sender
     * @return User
     */
    public function setSender(\Zikula\Module\UsersModule\Entity\UserEntity $sender = null)
    {
        $this->sender = $sender;  
        return $this;
    }
    
    /**
     * Get sender
     *
     * @return Sender user object 
     */
    public function getSender()
    {
        if(is_null($this->sender)){
        $sender['uname'] = 'Deleted User';   
        }else {
        $sender = $this->sender;   
        }
        return $sender;
    }
    
    /**
     * Set recipient
     *
     * @param UserEntity $recipient
     * @return User
     */
    public function setRecipient(\Zikula\Module\UsersModule\Entity\UserEntity $recipient = null)
    {
        $this->recipient = $recipient;  
        return $this;
    }
    
    /**
     * Get recipient
     *
     * @return Recipient user object 
     */
    public function getRecipient()
    {
        return $this->recipient;
    }
    /**
     * Set subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;  
        return $this;
    }
    
    /**
     * Get message subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * Set message send
     *
     * @param DateTime object $send
     * @return $this
     */
    public function setSend(\DateTime $send)
    {
        $this->send = $send;  
        return $this;
    }
    
    /**
     * Get message send
     *
     * @return DateTime object 
     */
    public function getSend()
    {
        return $this->send;
    }
    
    /**
     * Set message text
     *
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;  
        return $this;
    }
    
    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
    
    /**
     * Set seen status
     *
     * @param  DateTime/null $seen
     * @return $this
     */
    public function setSeen(\DateTime $seen = null)
    {
        $this->seen = $seen;  
        return $this;
    }
    
    /**
     * Get seen status
     *
     * @return DateTime/null 
     */
    public function getSeen()
    {
        return $this->seen;
    }
    
    /**
     * Set replied status
     *
     * @param DateTime $replied
     * @return $this
     */
    public function setReplied(\DateTime $replied = null)
    {
        $this->replied = $replied;  
        return $this;
    }
    
    /**
     * Get replied status
     *
     * @return DateTime/null 
     */
    public function getReplied()
    {
        return $this->replied;
    }
    
    /**
     * Set notified status
     *
     * @param DateTime/null $notified
     * @return $this
     */
    public function setNotified(\DateTime $notified = null)
    {
        $this->notified = $notified;  
        return $this;
    }
    
    /**
     * Get notified status
     *
     * @return DateTime/null 
     */
    public function getNotified()
    {
        return $this->notified;
    }
    
    /**
     * Set inbox status
     *
     * @param $inbox
     * @return $this
     */
    public function setInbox($inbox)
    {
        $this->inbox = $inbox;  
        return $this;
    }
    
    /**
     * Get inbox status
     *
     * @return boolean 
     */
    public function getInbox()
    {
        return $this->inbox;
    }
    
    /**
     * Set outbox status
     *
     * @param $outbox
     * @return $this
     */
    public function setOutbox($outbox)
    {
        $this->outbox = $outbox;  
        return $this;
    }
    
    /**
     * Get outbox
     *
     * @return boolean  
     */
    public function getOutbox()
    {
        return $this->outbox;
    } 
    
    /**
     * Set stored status for sender
     *
     * @param $storedbysender
     * @return $this
     */
    public function setStoredbysender($storedbysender)
    {
        $this->storedbysender = $storedbysender;  
        return $this;
    }
    
    /**
     * Get stored status for sender
     *
     * @return boolean 
     */
    public function getStoredbysender()
    {
        return $this->storedbysender;
    }
    
    /**
     * Set stored status for recipient
     *
     * @param $storedbyrecipient
     * @return $this
     */
    public function setStoredbyrecipient($storedbyrecipient)
    {
        $this->storedbyrecipient = $storedbyrecipient;  
        return $this;
    }    
    
    /**
     * Get stored status for recipient
     *
     * @return boolean 
     */
    public function getStoredbyrecipient()
    {
        return $this->storedbyrecipient;
    }
    
    /**
     * get the conversation parent id
     *
     * @return int the message id
     */
    public function getConversationid()
    {
        return $this->conversationid;
    }

    /**
     * set the conversation parent id
     *
     * @param MessageEntity the parent conversation id
     */
    public function setConversationid(MessageEntity $conversationid = null)
    {
        $this->conversationid = $conversationid;
    }

    /**
     * get the messages for conversation
     *
     * @return array the conversation messages
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * set the messages for conversation
     *
     * @param array $conversation the child categories
     */
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
    }    
}
