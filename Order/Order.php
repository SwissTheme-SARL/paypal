<?php


namespace SwissthemeSarl\paypal\Order;

class Order
{
    protected $currency;

    public function __construct()
    {
        $this->currency = 'CHF';
    }

    public function setOrderPayload(float $amount): ?array {
        $payload = [];
        $payload['intent'] = "AUTHORIZE";
        $payload['purchase_units'] = [
            ['amount' =>
                [
                    'currency_code' => "CHF",
                    'value' => $amount
                    ]
            ]
        ];
        return $payload;
    }

}

