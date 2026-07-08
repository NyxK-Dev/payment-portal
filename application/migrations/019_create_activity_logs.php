<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_activity_logs extends CI_Migration
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
        if (!$this->db->table_exists('activity_logs')) {
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
                'activity_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => TRUE,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => TRUE,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('user_id');
            $this->dbforge->create_table('activity_logs', TRUE);
        }

        $this->add_column_if_missing('activity_logs', 'created_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);

        $this->add_foreign_key_if_missing('activity_logs', 'fk_activity_logs_user', 'user_id', 'users', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->dbforge->drop_table('activity_logs', TRUE);
    }
}
