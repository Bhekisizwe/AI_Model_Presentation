<?php
declare(strict_types=1);
namespace UserClasses\BusinessLayer;

use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

/**
 *
 * @author Bheki Mthethwa
 *        
 */
class AIResultsRelay implements MessageComponentInterface
{
    private $connectedClients;
    
    public function __construct() {
        $this->connectedClients = new \SplObjectStorage;         
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Ratchet\ComponentInterface::onClose()
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->connectedClients->detach($conn); //remove the Connected Client from the multicast list
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Ratchet\ComponentInterface::onError()
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {       
        $conn->close();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Ratchet\ComponentInterface::onOpen()
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->connectedClients->attach($conn); //add the Connected Client to the multicast list   
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Ratchet\WebSocket\MessageCallableInterface::onMessage()
     */
    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        foreach ($this->connectedClients as $client) {
            if ($conn !== $client) {
                // Send to each connected client except the AI Model Websocket client
                $client->send($msg);
            }
        }
    }
}

