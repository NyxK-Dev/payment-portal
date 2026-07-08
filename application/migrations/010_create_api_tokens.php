<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_api_tokens extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('api_tokens')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'token_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => FALSE,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'last_used_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->create_table('api_tokens', TRUE);

        $this->db->query('ALTER TABLE `api_tokens` ADD UNIQUE `uq_api_tokens_token_hash` (`token_hash`)');
        $this->db->query('ALTER TABLE `api_tokens` ADD CONSTRAINT `fk_api_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down()
    {
        $this->dbforge->drop_table('api_tokens', TRUE);
    }
}
