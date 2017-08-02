<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Notification;

/**
 * Class for processing the list of system messages
 *
 * Class MessageList
 * @api
 * @since 2.0.0
 */
class MessageList
{
    /**
     * List of configured message classes
     *
     * @var array
     * @since 2.0.0
     */
    protected $_messageClasses;

    /**
     * List of messages
     *
     * @var array
     * @since 2.0.0
     */
    protected $_messages;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $messages
     * @since 2.0.0
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $messages = [])
    {
        $this->_objectManager = $objectManager;
        $this->_messageClasses = $messages;
    }

    /**
     * Load messages to display
     *
     * @return void
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @since 2.0.0
     */
    protected function _loadMessages()
    {
        if (!empty($this->_messages)) {
            return;
        }
        foreach ($this->_messageClasses as $key => $messageClass) {
            if (!$messageClass) {
                throw new \InvalidArgumentException('Message class for message "' . $key . '" is not set');
            }
            $message = $this->_objectManager->get($messageClass);
            if ($message instanceof \Magento\Framework\Notification\MessageInterface) {
                $this->_messages[$message->getIdentity()] = $message;
            } else {
                throw new \UnexpectedValueException("Message class has to implement the message interface.");
            }
        }
    }

    /**
     * Retrieve message by
     *
     * @param string $identity
     * @return null|\Magento\Framework\Notification\MessageInterface
     * @since 2.0.0
     */
    public function getMessageByIdentity($identity)
    {
        $this->_loadMessages();
        return isset($this->_messages[$identity]) ? $this->_messages[$identity] : null;
    }

    /**
     * Retrieve list of all messages
     *
     * @return \Magento\Framework\Notification\MessageInterface[]
     * @since 2.0.0
     */
    public function asArray()
    {
        $this->_loadMessages();
        return $this->_messages;
    }
}
