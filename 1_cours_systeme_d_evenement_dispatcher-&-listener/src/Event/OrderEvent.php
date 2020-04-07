<?php

namespace App\Event;

use App\Model\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderEvent extends Event {

    protected $order;

    public function __construct(order $order)
    {
        $this->order = $order;
    }

    public function getOrder():order {
        return $this->order;
    }

}