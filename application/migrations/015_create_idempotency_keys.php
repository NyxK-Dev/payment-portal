<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_idempotency_keys extends CI_Migration
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


            'user_id'=>[
                'type'=>'BIGINT',
                'constraint'=>20,
                'unsigned'=>TRUE,
                'null'=>TRUE,
            ],


            'idempotency_key'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'null'=>FALSE,
            ],


            'request_hash'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'null'=>FALSE,
            ],


            /*
             processing
             completed
             failed
            */
            'status'=>[
                'type'=>'VARCHAR',
                'constraint'=>30,
                'default'=>'processing'
            ],


            'response_code'=>[
                'type'=>'INT',
                'null'=>TRUE
            ],


            'response_data'=>[
                'type'=>'LONGTEXT',
                'null'=>TRUE,
            ],


            'locked_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],


            'expires_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],


            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ],


            'updated_at'=>[
                'type'=>'DATETIME',
                'null'=>TRUE,
            ]

        ]);


        $this->dbforge->add_key(
            'id',
            TRUE
        );


        // important
        $this->dbforge->add_key(
            'idempotency_key',
            FALSE,
            TRUE
        );


        $this->dbforge->create_table(
            'idempotency_keys'
        );


        $this->db->query("
            ALTER TABLE idempotency_keys
            ADD CONSTRAINT fk_idempotency_user
            FOREIGN KEY(user_id)
            REFERENCES users(id)
            ON DELETE CASCADE
        ");
    }



    public function down()
    {
        $this->dbforge->drop_table(
            'idempotency_keys'
        );
    }
}