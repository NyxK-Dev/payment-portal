<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_payment_attempts extends CI_Migration
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

    private function foreign_key_exists($table, $constraint_name)
    {
        $result = $this->db->query('SHOW CREATE TABLE `' . $table . '`')->row_array();
        return isset($result['Create Table']) && strpos($result['Create Table'], $constraint_name) !== false;
    }

    private function add_foreign_key_if_missing($table, $constraint_name, $column, $reference_table, $reference_column, $on_update = 'CASCADE', $on_delete = 'RESTRICT')
    {
        if ($this->foreign_key_exists($table, $constraint_name) || !$this->db->table_exists($reference_table)) {
            return;
        }

        $this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `' . $constraint_name . '` FOREIGN KEY (`' . $column . '`) REFERENCES `' . $reference_table . '` (`' . $reference_column . '`) ON UPDATE ' . $on_update . ' ON DELETE ' . $on_delete);
    }

    public function up()
    {
        if (!$this->db->table_exists('payment_attempts')) {
            $this->dbforge->add_field([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'payment_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                ],
                'provider' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE,
                ],
                'provider_reference' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE,
                ],
                'amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '12,2',
                    'null'       => FALSE,
                ],
                'currency' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => TRUE,
                ],
                'failure_reason' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
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
            $this->dbforge->add_key('payment_id');
            $this->dbforge->add_key('provider_reference');
            $this->dbforge->create_table('payment_attempts', TRUE);
        }

        $this->add_column_if_missing('payment_attempts', 'provider_reference', [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('payment_attempts', 'amount', [
            'type'       => 'DECIMAL',
            'constraint' => '12,2',
            'null'       => FALSE,
        ]);
        $this->add_column_if_missing('payment_attempts', 'currency', [
            'type'       => 'VARCHAR',
            'constraint' => 10,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('payment_attempts', 'failure_reason', [
            'type' => 'TEXT',
            'null' => TRUE,
        ]);
        $this->add_column_if_missing('payment_attempts', 'created_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);
        $this->add_column_if_missing('payment_attempts', 'updated_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);

        $this->add_index_if_missing('payment_attempts', 'idx_payment_attempts_payment_id', array('payment_id'));
        $this->add_index_if_missing('payment_attempts', 'idx_payment_attempts_provider_reference', array('provider_reference'));
        $this->add_unique_if_missing('payment_attempts', 'uq_payment_attempts_provider_reference', array('provider', 'provider_reference'));
        $this->add_foreign_key_if_missing('payment_attempts', 'fk_payment_attempts_payment', 'payment_id', 'payments', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dbforge->drop_table('payment_attempts', TRUE);
    }
}
