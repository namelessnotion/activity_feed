<?php
class ActivityFeedFixture extends CakeTestFixture {
    var $name = 'ActivityFeed';
    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
        'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index'),
        'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => NULL),
        'subject' => array('type' => 'text', 'null' => false, 'default' => NULL),
        'verb' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50),
        'preposition' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50),
        'object' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50),
        'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'key' => 'index'),
        'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'model' => array('column' => 'model', 'unique' => 0), 'created' => array('column' => 'created', 'unique' => 0), 'user_id' => array('column' => 'user_id', 'unique' => 0))
    );
}
?>
