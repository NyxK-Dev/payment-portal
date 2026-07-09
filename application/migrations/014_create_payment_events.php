<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_payment_events extends CI_Migration
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
            'event_type'=>[
                'type'=>'VARCHAR',
                'constraint'=>100,
                'null'=>TRUE,
            ],
            'event_source'=>[
                'type'=>'VARCHAR',
                'constraint'=>50,
                'null'=>TRUE,
            ],
            'payload'=>[
                'type'=>'TEXT',
                'null'=>TRUE,
            ],
            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('payment_id');
        $this->dbforge->add_key('event_type');

        $this->dbforge->create_table('payment_events');
        $this->db->query("
        ALTER TABLE payment_events
        ADD CONSTRAINT fk_payment_events_payment
        FOREIGN KEY(payment_id)
        REFERENCES payments(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
    ");
    }

    public function down()
    {
        $this->dbforge->drop_table('payment_events');
    }
}