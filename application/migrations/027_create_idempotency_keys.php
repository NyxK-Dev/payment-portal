<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_idempotency_keys extends CI_Migration
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

    private function add_unique_if_missing($table, $index_name, $columns)
    {
        if ($this->index_exists($table, $index_name)) {
            return;
        }

        $this->db->query('ALTER TABLE `' . $table . '` ADD UNIQUE `' . $index_name . '` (' . implode(', ', array_map(function ($c) {
            return '`' . $c . '`';
        }, $columns)) . ')');
    }

    public function up()
    {
        if (!$this->db->table_exists('idempotency_keys')) {
            $this->dbforge->add_field([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'key_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => FALSE,
                ],
                'resource_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                ],
                'resource_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('key_name');
            $this->dbforge->create_table('idempotency_keys', TRUE);
        }

        $this->add_column_if_missing('idempotency_keys', 'resource_type', [
            'type'       => 'VARCHAR',
            'constraint' => 100,
            'null'       => FALSE,
        ]);
        $this->add_column_if_missing('idempotency_keys', 'resource_id', [
            'type'       => 'BIGINT',
            'constraint' => 20,
            'unsigned'   => TRUE,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('idempotency_keys', 'status', [
            'type'       => 'VARCHAR',
            'constraint' => 50,
            'null'       => FALSE,
        ]);
        $this->add_column_if_missing('idempotency_keys', 'created_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);
        $this->add_column_if_missing('idempotency_keys', 'updated_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);

        $this->add_index_if_missing('idempotency_keys', 'idx_idempotency_keys_key_name', array('key_name'));
        $this->add_index_if_missing('idempotency_keys', 'idx_idempotency_keys_resource_type', array('resource_type'));
        $this->add_unique_if_missing('idempotency_keys', 'uq_idempotency_keys_key_name', array('key_name'));
    }

    public function down()
    {
        $this->dbforge->drop_table('idempotency_keys', TRUE);
    }
}
