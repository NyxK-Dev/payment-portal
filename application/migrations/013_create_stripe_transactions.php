<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_stripe_transactions extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id'=>[
                'type'=>'BIGINT',
                'constraint'=>20,
                'unsigned'=>TRUE,
                'auto_increment'=>TRUE,
            ],
            'payment_id'=>[
                'type'=>'BIGINT',
                'constraint'=>20,
                'unsigned'=>TRUE,
                'null'=>FALSE,
            ],
            'payment_attempt_id'=>[
                'type'=>'BIGINT',
                'constraint'=>20,
                'unsigned'=>TRUE,
                'null'=>TRUE,
            ],
            'webhook_event_id'=>[
                'type'=>'BIGINT',
                'constraint'=>20,
                'unsigned'=>TRUE,
                'null'=>TRUE,
            ],
            'provider'=>[
                'type'=>'VARCHAR',
                'constraint'=>50,
                'default'=>'stripe',
            ],
            'stripe_session_id'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'null'=>TRUE,
            ],
            'payment_intent_id'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'null'=>TRUE,
            ],
            'charge_id'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'null'=>TRUE,
            ],
            'currency'=>[
                'type'=>'VARCHAR',
                'constraint'=>10,
                'null'=>TRUE,
            ],
            'amount'=>[
                'type'=>'DECIMAL',
                'constraint'=>'12,2',
                'null'=>TRUE,
            ],
            'provider_status'=>[
                'type'=>'VARCHAR',
                'constraint'=>50,
                'null'=>TRUE,
            ],
            'raw_payload'=>[
                'type'=>'TEXT',
                'null'=>TRUE,
            ],
            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],
            'updated_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('payment_id');
        $this->dbforge->add_key('payment_attempt_id');
        $this->dbforge->add_key('webhook_event_id');
        $this->dbforge->add_key('payment_intent_id');
        $this->dbforge->add_key('stripe_session_id');
        $this->dbforge->add_key('charge_id');

        $this->dbforge->create_table('stripe_transactions');
        $this->db->query("
    ALTER TABLE stripe_transactions
    ADD CONSTRAINT fk_stripe_transactions_payment
    FOREIGN KEY(payment_id)
    REFERENCES payments(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
");


$this->db->query("
    ALTER TABLE stripe_transactions
    ADD CONSTRAINT fk_stripe_transactions_attempt
    FOREIGN KEY(payment_attempt_id)
    REFERENCES payment_attempts(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");


$this->db->query("
    ALTER TABLE stripe_transactions
    ADD CONSTRAINT fk_stripe_transactions_webhook
    FOREIGN KEY(webhook_event_id)
    REFERENCES stripe_webhook_events(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");
    }

    public function down()
    {
        $this->dbforge->drop_table('stripe_transactions');
    }
}