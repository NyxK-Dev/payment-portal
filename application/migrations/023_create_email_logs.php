<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_email_logs extends CI_Migration
{
    private function column_exists($table, $column)
    {
        $columns = $this->db->query('SHOW COLUMNS FROM `' . $table . '`')->result_array();
        foreach ($columns as $col) {
            if ($col['Field'] === $column) {
                return true;
            }
        }

        return false;
    }

    private function add_column_if_missing($table, $column, array $definition)
    {
        if ($this->column_exists($table, $column)) {
            return;
        }

        $this->dbforge->add_column($table, array($column => $definition));
    }

    private function index_exists($table, $index_name)
    {
        $indexes = $this->db->query('SHOW INDEX FROM `' . $table . '`')->result_array();
        foreach ($indexes as $index) {
            if ($index['Key_name'] === $index_name) {
                return true;
            }
        }

        return false;
    }

    private function add_index_if_missing($table, $index_name, $columns)
    {
        if ($this->index_exists($table, $index_name)) {
            return;
        }

        $this->db->query('ALTER TABLE `' . $table . '` ADD INDEX `' . $index_name . '` (' . implode(', ', array_map(function ($c) {
            return '`' . $c . '`';
        }, $columns)) . ')');
    }

    private function foreign_key_exists($table, $constraint_name)
    {
        $result = $this->db->query('SHOW CREATE TABLE `' . $table . '`')->row_array();
        return isset($result['Create Table']) && strpos($result['Create Table'], $constraint_name) !== false;
    }

    private function add_foreign_key_if_missing($table, $constraint_name, $column, $reference_table, $reference_column, $on_update = 'CASCADE', $on_delete = 'RESTRICT')
    {
        if ($this->foreign_key_exists($table, $constraint_name)) {
            return;
        }

        $this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `' . $constraint_name . '` FOREIGN KEY (`' . $column . '`) REFERENCES `' . $reference_table . '` (`' . $reference_column . '`) ON UPDATE ' . $on_update . ' ON DELETE ' . $on_delete);
    }

    public function up()
    {
        if (!$this->db->table_exists('email_logs')) {
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
                    'null'       => TRUE,
                ],
                'email_to' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE,
                ],
                'subject' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE,
                ],
                'response' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'status_lookup_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'sent_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('user_id');
            $this->dbforge->add_key('status_lookup_id');
            $this->dbforge->create_table('email_logs', TRUE);
        }

        $this->add_column_if_missing('email_logs', 'status_lookup_id', [
            'type'       => 'BIGINT',
            'constraint' => 20,
            'unsigned'   => TRUE,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('email_logs', 'sent_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);

        $this->add_index_if_missing('email_logs', 'idx_email_logs_status_lookup_id', array('status_lookup_id'));
        $this->add_foreign_key_if_missing('email_logs', 'fk_email_logs_user', 'user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->add_foreign_key_if_missing('email_logs', 'fk_email_logs_status_lookup', 'status_lookup_id', 'lookups', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->dbforge->drop_table('email_logs', TRUE);
    }
}
