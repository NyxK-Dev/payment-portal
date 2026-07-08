<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_refunds extends CI_Migration
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

    private function add_column_if_missing($table, $column, array $definition)
    {
        if ($this->column_exists($table, $column)) {
            return;
        }

        $this->dbforge->add_column($table, array($column => $definition));
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
        if ($this->foreign_key_exists($table, $constraint_name)) {
            return;
        }

        $this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `' . $constraint_name . '` FOREIGN KEY (`' . $column . '`) REFERENCES `' . $reference_table . '` (`' . $reference_column . '`) ON UPDATE ' . $on_update . ' ON DELETE ' . $on_delete);
    }

    public function up()
    {
        if (!$this->db->table_exists('refunds')) {
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
                'refund_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                ],
                'stripe_refund_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE,
                ],
                'amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '12,2',
                    'null'       => FALSE,
                ],
                'reason' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE,
                ],
                'status_lookup_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'refunded_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'created_by' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'approved_by' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'approved_at' => [
                    'type' => 'DATETIME',
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
            $this->dbforge->add_key('status_lookup_id');
            $this->dbforge->add_key('created_by');
            $this->dbforge->add_key('approved_by');
            $this->dbforge->create_table('refunds', TRUE);
        }

        $this->add_column_if_missing('refunds', 'refund_no', [
            'type'       => 'VARCHAR',
            'constraint' => 100,
            'null'       => FALSE,
        ]);
        $this->add_column_if_missing('refunds', 'status_lookup_id', [
            'type'       => 'BIGINT',
            'constraint' => 20,
            'unsigned'   => TRUE,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('refunds', 'refunded_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);
        $this->add_column_if_missing('refunds', 'created_by', [
            'type'       => 'BIGINT',
            'constraint' => 20,
            'unsigned'   => TRUE,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('refunds', 'approved_by', [
            'type'       => 'BIGINT',
            'constraint' => 20,
            'unsigned'   => TRUE,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('refunds', 'approved_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);
        $this->add_column_if_missing('refunds', 'updated_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);

        $this->add_index_if_missing('refunds', 'idx_refunds_status_lookup_id', array('status_lookup_id'));
        $this->add_index_if_missing('refunds', 'idx_refunds_created_by', array('created_by'));
        $this->add_index_if_missing('refunds', 'idx_refunds_approved_by', array('approved_by'));
        $this->add_unique_if_missing('refunds', 'uq_refunds_refund_no', array('refund_no'));
        $this->add_unique_if_missing('refunds', 'uq_refunds_stripe_refund_id', array('stripe_refund_id'));
        $this->add_foreign_key_if_missing('refunds', 'fk_refunds_payment', 'payment_id', 'payments', 'id', 'CASCADE', 'RESTRICT');
        $this->add_foreign_key_if_missing('refunds', 'fk_refunds_status_lookup', 'status_lookup_id', 'lookups', 'id', 'CASCADE', 'SET NULL');
        $this->add_foreign_key_if_missing('refunds', 'fk_refunds_created_by', 'created_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->add_foreign_key_if_missing('refunds', 'fk_refunds_approved_by', 'approved_by', 'users', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->dbforge->drop_table('refunds', TRUE);
    }
}
