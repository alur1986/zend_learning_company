<?php

namespace Notification\Event;

use Savve\EventManager\Event as BaseEvent;

class Event extends BaseEvent
{
    const EVENT_NOTIFY = 'notify';

    /**
     * Get the sender array('name', 'email')
     *
     * @return mixed
     */
    public function getSender ()
    {
        return $this->getParam('sender');
    }

    /**
     * Set the sender array('name','email')
     *
     * @param mixed $sender
     * @return \Notification\Event\Event
     */
    public function setSender ($sender)
    {
        $this->setParam('sender', $sender);
        return $this;
    }

    /**
     * Get the email message content body
     *
     * @return \Zend\Mail\Message string array
     */
    public function getMessage ()
    {
        return $this->getParam('message');
    }

    /**
     * Set the email message content body
     *
     * @param \Zend\Mail\Message|string|array $message
     * @return \Notification\Event\Event
     */
    public function setMessage ($message)
    {
        $this->setParam('message', $message);
        return $this;
    }

    /**
     * Get the email subject
     *
     * @return string
     */
    public function getSubject ()
    {
        return $this->getParam('subject');
    }

    /**
     * Set the email subject
     *
     * @param string $subject
     * @return \Notification\Event\Event
     */
    public function setSubject ($subject)
    {
        $this->setParam('subject', $subject);
        return $this;
    }

    /**
     * Get the email template
     *
     * @return string
     */
    public function getTemplate ()
    {
        return $this->getParam('template');
    }

    /**
     * Set the email template
     *
     * @param string $template
     * @return \Notification\Event\Event
     */
    public function setTemplate ($template)
    {
        $this->setParam('template', $template);
        return $this;
    }
}