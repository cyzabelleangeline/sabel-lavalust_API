<?php

class Add_user_names
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->database();
    }

    public function up()
    {
        foreach (['firstname', 'lastname'] as $column) {
            $exists = $this->_lava->db->raw(
                'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['users', $column]
            );

            if ($exists && !$exists->fetch(PDO::FETCH_ASSOC)) {
                $this->_lava->db->raw(
                    "ALTER TABLE `users` ADD COLUMN `{$column}` VARCHAR(100) NOT NULL DEFAULT ''"
                );
            }
        }
    }

    public function down()
    {
        foreach (['lastname', 'firstname'] as $column) {
            $exists = $this->_lava->db->raw(
                'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['users', $column]
            );

            if ($exists && $exists->fetch(PDO::FETCH_ASSOC)) {
                $this->_lava->db->raw(
                    "ALTER TABLE `users` DROP COLUMN `{$column}`"
                );
            }
        }
    }
}
