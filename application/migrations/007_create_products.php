<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_products extends CI_Migration
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
            'category_lookup_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'status_lookup_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'sku' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => FALSE,
            ],
            'stock_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'version' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('sku', FALSE, TRUE);
        $this->dbforge->add_key('category_lookup_id');
        $this->dbforge->add_key('status_lookup_id');
        $this->dbforge->add_key('created_by');

        $this->dbforge->create_table('products');

        $this->db->query("
    ALTER TABLE products
    ADD CONSTRAINT fk_products_category_lookup
    FOREIGN KEY(category_lookup_id)
    REFERENCES lookups(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");


$this->db->query("
    ALTER TABLE products
    ADD CONSTRAINT fk_products_status_lookup
    FOREIGN KEY(status_lookup_id)
    REFERENCES lookups(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");


$this->db->query("
    ALTER TABLE products
    ADD CONSTRAINT fk_products_created_by
    FOREIGN KEY(created_by)
    REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");
    }

    public function down()
    {
        $this->dbforge->drop_table('products');
    }
}