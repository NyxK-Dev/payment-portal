<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Update_refresh_tokens extends CI_Migration
{
    public function up()
    {
        /*
        Rename api_tokens table
        */

        if ($this->db->table_exists('api_tokens')) {
            $this->db->query("RENAME TABLE api_tokens TO refresh_tokens");
        }

        if (!$this->db->table_exists('refresh_tokens')) {
            return;
        }

        if (!$this->db->field_exists('revoked_at', 'refresh_tokens')) {
            $this->dbforge->add_column('refresh_tokens', [
                'revoked_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);
        }

        if (!$this->db->field_exists('token_family', 'refresh_tokens')) {
            $this->db->query("ALTER TABLE refresh_tokens ADD COLUMN token_family VARCHAR(100) NULL");
        }

        if (!$this->db->field_exists('ip_address', 'refresh_tokens')) {
            $this->dbforge->add_column('refresh_tokens', [
                'ip_address' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => TRUE,
                ],
            ]);
        }

        if (!$this->db->field_exists('user_agent', 'refresh_tokens')) {
            $this->dbforge->add_column('refresh_tokens', [
                'user_agent' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
            ]);
        }
    }

    public function down()
    {
        if (!$this->db->table_exists('refresh_tokens')) {
            return;
        }

        if ($this->db->field_exists('revoked_at', 'refresh_tokens')) {
            $this->dbforge->drop_column('refresh_tokens', 'revoked_at');
        }

        if ($this->db->field_exists('token_family', 'refresh_tokens')) {
            $this->dbforge->drop_column('refresh_tokens', 'token_family');
        }

        if ($this->db->field_exists('ip_address', 'refresh_tokens')) {
            $this->dbforge->drop_column('refresh_tokens', 'ip_address');
        }

        if ($this->db->field_exists('user_agent', 'refresh_tokens')) {
            $this->dbforge->drop_column('refresh_tokens', 'user_agent');
        }

        if ($this->db->table_exists('refresh_tokens')) {
            $this->db->query("RENAME TABLE refresh_tokens TO api_tokens");
        }
    }
}
