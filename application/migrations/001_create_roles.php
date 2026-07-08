<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_roles extends CI_Migration
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
        if (!$this->db->table_exists('roles')) {
            $this->dbforge->add_field([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE,
                ],
                'description' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE,
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
            $this->dbforge->create_table('roles', TRUE);
        }

        $this->add_column_if_missing('roles', 'description', [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('roles', 'created_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);
        $this->add_column_if_missing('roles', 'updated_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);

        $this->add_unique_if_missing('roles', 'uq_roles_name', array('name'));
    }

    public function down()
    {
        $this->dbforge->drop_table('roles', TRUE);
    }
}
