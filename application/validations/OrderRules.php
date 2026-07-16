<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrderRules
{
    /**
     * Create Order (POST /api/v1/orders)
     */
    public static function create()
    {
        return [];
    }
    /**
     * Update order status (PATCH /orders/{id}/status)
     */
    public static function updateStatus()
    {
        return [
            RuleBuilder::make(
                'status_lookup_id',
                'Order Status',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::integer()
                )
            )
        ];
    }

    /**
     * Delete Order
     */
    public static function delete()
    {
        return [
            RuleBuilder::make(
                'id',
                'Order ID',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::integer()
                )
            )
        ];
    }

    /**
     * Customer cancel order
     */
    public static function cancel()
    {
        return [
            RuleBuilder::make(
                'status_lookup_id',
                'Order Status',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::integer()
                )
            )
        ];
    }
}
