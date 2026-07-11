<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_badge_class_to_lookups extends CI_Migration
{
    public function up()
    {
        $fields = $this->db->field_data('lookups');
        $columns = array_map(function ($field) {
            return $field->name;
        }, $fields);

        if (!in_array('badge_class', $columns, true)) {
            $this->dbforge->add_column('lookups', [
                'badge_class' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE,
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->field_data('lookups');
        $columns = array_map(function ($field) {
            return $field->name;
        }, $fields);

        if (in_array('badge_class', $columns, true)) {
            $this->dbforge->drop_column('lookups', 'badge_class');
        }
    }
}
