<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_role_permissions extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ],
            'role_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => FALSE,
            ],
            'permission_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => FALSE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('role_id');
        $this->dbforge->add_key('permission_id');

        $this->dbforge->create_table('role_permissions');

        $this->db->query("ALTER TABLE role_permissions ADD CONSTRAINT uq_role_permission UNIQUE(role_id, permission_id)");

        $this->db->query("
        ALTER TABLE role_permissions
        ADD CONSTRAINT fk_role_permissions_role
        FOREIGN KEY(role_id)
        REFERENCES roles(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
    ");

    $this->db->query("
        ALTER TABLE role_permissions
        ADD CONSTRAINT fk_role_permissions_permission
        FOREIGN KEY(permission_id)
        REFERENCES permissions(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
    ");
    }

    public function down()
    {
        $this->dbforge->drop_table('role_permissions');
    }
}