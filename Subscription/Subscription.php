<?php

namespace SwissthemeSarl\paypal\Subscription;

use App\Entity\Items;
use App\Entity\User;

class Subscription
{
    public function setPlanPayload(Items $item)
    {
        return [
            'product_id' => $item->getPaypalProduct(),
            'name' => $item->getType(),
            'description' => $item->getType(),
            'status' => 'ACTIVE',
            'billing_cycles' => [
                self::setFrequency($item),
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => $item->getPrice(),
                    'currency_code' => 'CHF',
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3,
            ],
            'taxes' => [
                'percentage' => 7,
                'inclusive' => true,
            ],
        ];
    }

    public function setPlanHeaders($token)
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ];
    }

    public function setSubscriptionPayLoad(string $plan, Items $item, User $user)
    {
        $payload = [
            'plan_id' => $plan,
            //'start_time' => new \DateTime('now'),
            'quantity' => 1,
            'shipping_amount' => [
                'currency_code' => 'CHF',
                'value' => $item->getPrice(),
            ],
            'email_address' => $user->getEmail(),
            'shipping_address' => [
                'full_name' => 'parentsolo',
            ],
            'application_context' => [
                'brand_name' => 'parentsolo',
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                ],
                'return_url' => empty($_ENV['PAYPAL_RETURN_URL']) ? 'https://parentsolo.ch/paypal/accept/sub' : $_ENV['PAYPAL_RETURN_URL'],
                'cancel_url' => empty($_ENV['PAYPAL_CANCEL_URL']) ? 'https://parentsolo.ch/paypal/cancel/sub' : $_ENV['PAYPAL_CANCEL_URL'],
            ],
        ];

        return $payload;
    }

    public function capturePayload($details)
    {
        return $payload = [
            'note' => 'first payment for subscription',
            'capture_type' => 'OUTSTANDING_BALANCE',
            'amount' => [
                'currency_code' => $details->shipping_amount->currency_code,
                'value' => $details->shipping_amount->value,
            ],
        ];
    }

    private function setFrequency(Items $item)
    {
        return ['frequency' => [
            'interval_unit' => 'MONTH',
            'interval_count' => $item->getDuration(),
        ],
            'tenure_type' => 'REGULAR',
            'sequence' => 1,
            'total_cycles' => 20,
            'pricing_scheme' => [
                'fixed_price' => [
                    'value' => $item->getPrice(),
                    'currency_code' => 'CHF',
                ],
            ], ];
    }

    //{
//"plan_id": "P-5ML4271244454362WXNWU5NQ",
//"start_time": "2018-11-01T00:00:00Z",
//"quantity": "20",
//"shipping_amount": {
//"currency_code": "USD",
//"value": "10.00"
//},
//"subscriber": {
//    "name": {
//        "given_name": "John",
//      "surname": "Doe"
//    },
//    "email_address": "customer@example.com",
//    "shipping_address": {
//        "name": {
//            "full_name": "John Doe"
//      },
//      "address": {
//            "address_line_1": "2211 N First Street",
//        "address_line_2": "Building 17",
//        "admin_area_2": "San Jose",
//        "admin_area_1": "CA",
//        "postal_code": "95131",
//        "country_code": "US"
//      }
//    }
//  },
//  "application_context": {
//    "brand_name": "walmart",
//    "locale": "en-US",
//    "shipping_preference": "SET_PROVIDED_ADDRESS",
//    "user_action": "SUBSCRIBE_NOW",
//    "payment_method": {
//        "payer_selected": "PAYPAL",
//      "payee_preferred": "IMMEDIATE_PAYMENT_REQUIRED"
//    },
//    "return_url": "https://example.com/returnUrl",
//    "cancel_url": "https://example.com/cancelUrl"
//  }
//}
}
