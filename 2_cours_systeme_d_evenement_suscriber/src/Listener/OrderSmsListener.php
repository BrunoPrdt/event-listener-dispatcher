<?php

namespace App\Listener;

use App\Event\OrderEvent;
use App\Logger;
use App\Texter\Sms;
use App\Texter\SmsTexter;

class OrderSmsListener {

    protected $smsTexter;
    protected $logger;

    function __construct(SmsTexter $smsTexter, Logger $logger)
    {
        $this->smsTexter = $smsTexter;
        $this->logger = $logger;
    }

    function sendSmsToBuyer(OrderEvent $event) {
        $order = $event->getOrder();
        //$event->stopPropagation();// permet de stopper tous les évènements devant normalement se déclencher après

        // Après enregistrement on veut aussi envoyer un SMS au client
        // voir src/Texter/Sms.php et /src/Texter/SmsTexter.php
        $sms = new Sms();
        $sms->setNumber($order->getPhoneNumber())
            ->setText("Merci pour votre commande de {$order->getQuantity()} {$order->getProduct()} !");
        $this->smsTexter->send($sms);

        // Après SMS au client, on veut logger ce qui se passe :
        // voir src/Logger.php
        $this->logger->log("SMS de confirmation envoyé à {$order->getPhoneNumber()} !");
    }

}