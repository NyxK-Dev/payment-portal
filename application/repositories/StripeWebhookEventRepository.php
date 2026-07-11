<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class StripeWebhookEventRepository
{

    protected $CI;


    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->model(
            'Stripe_webhook_event_model',
            'stripeWebhookEventModel'
        );

    }



    public function existsByEventId($eventId)
    {

        return $this->CI
            ->stripeWebhookEventModel
            ->where(
                'event_id',
                $eventId
            )
            ->count_all_results() > 0;

    }



    public function create($data)
    {

        return $this->CI
            ->stripeWebhookEventModel
            ->insert($data);

    }



    public function update($id,$data)
    {

        return $this->CI
            ->stripeWebhookEventModel
            ->update(
                $id,
                $data
            );

    }

}