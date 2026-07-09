<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_stripe_webhook_events extends CI_Migration
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
            'event_id'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'null'=>FALSE,
            ],
            'event_type'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'null'=>TRUE,
            ],
            'processed'=>[
                'type'=>'TINYINT',
                'constraint'=>1,
                'default'=>0,
            ],
            'retry_count'=>[
                'type'=>'INT',
                'constraint'=>11,
                'default'=>0,
            ],
            'processing_started_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],
            'processing_completed_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],
            'error_message'=>[
                'type'=>'TEXT',
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
            'processed_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('processed');
        $this->dbforge->add_key('event_type');
        $this->dbforge->add_key('event_id', FALSE, TRUE);

        $this->dbforge->create_table('stripe_webhook_events');
    }

    public function down()
    {
        $this->dbforge->drop_table('stripe_webhook_events');
    }
}