<?php

namespace App\Controller;

use App\Database;
use App\Event\OrderEvent;
use App\Logger;
use App\Mailer\Email;
use App\Mailer\Mailer;
use App\Model\Order;
use App\Texter\Sms;
use App\Texter\SmsTexter;
use Symfony\Component\EventDispatcher\EventDispatcher;

class OrderController
{

    protected $database;
    protected $mailer;
    protected $texter;
    protected $logger;
    protected $dispatcher;

    public function __construct(Database $database, Mailer $mailer, SmsTexter $texter, Logger $logger, EventDispatcher $dispatcher)
    {
        $this->database = $database;
        $this->mailer = $mailer;
        $this->texter = $texter;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    public function displayOrderForm()
    {
        require __DIR__ . '/../../views/form.html.php';
    }

    public function handleOrder()
    {
        // Extraction des données du POST et création d'un objet Order (voir src/Model/Order.php)
        $order = new Order;
        $order->setProduct($_POST['product'])
            ->setQuantity($_POST['quantity'])
            ->setEmail($_POST['email'])
            ->setPhoneNumber($_POST['phone']);

        //on crée on 1er évènement, générique pour le moment, avec un tableau précisant les détails av l'objet order,
        // et le nom que l'on veut donner à cet évènement.
        $this->dispatcher->dispatch(new OrderEvent($order), 'order.before_insert');

        // Enregistrement en base de données :
        // voir src/Database.php
        $this->database->insertOrder($order);

        $this->dispatcher->dispatch(new OrderEvent($order), 'order.after_insert');
    }
}
