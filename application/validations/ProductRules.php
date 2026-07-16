<?php

defined('BASEPATH') or exit('No direct script access allowed');


class ProductRules
{

    /**
     * Create Product Validation
     */
    public static function create()
    {
        return [

            RuleBuilder::make(
                'category_lookup_id',
                'Category',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::integer()
                )
            ),


            RuleBuilder::make(
                'status_lookup_id',
                'Status',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::integer()
                )
            ),


            RuleBuilder::make(
                'name',
                'Product Name',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    'trim',
                    RuleBuilder::min(3),
                    RuleBuilder::max(255)
                )
            ),


            RuleBuilder::make(
                'description',
                'Description',
                RuleBuilder::combine(
                    'trim',
                    RuleBuilder::max(500)
                )
            ),


            RuleBuilder::make(
                'sku',
                'SKU',
                RuleBuilder::combine(
                    'trim',
                    RuleBuilder::max(100)
                )
            ),


            RuleBuilder::make(
                'price',
                'Price',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::numeric()
                )
            ),


            RuleBuilder::make(
                'stock_qty',
                'Stock Quantity',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::integer(),
                    'greater_than_equal_to[0]'
                )
            )

        ];
    }




    /**
     * Update Product Validation
     */
    public static function update()
    {
        return self::create();
    }




    /**
     * Delete Product Validation
     */
    public static function delete()
    {
        return [

            RuleBuilder::make(
                'id',
                'Product ID',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::integer()
                )
            )

        ];
    }
}
