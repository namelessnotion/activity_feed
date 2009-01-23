<?php
class ActivityFeedPostFixture extends CakeTestFixture {
    var $name = 'ActivityFeedPost';
    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
        'title' => array('type' => 'text', 'null' => false, 'default' => NULL),
        'body' => array('type' => 'text', 'null' => false, 'default' => NULL),
        'activity_feed_user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
        'active' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'key' => 'index'),
        'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'key' => 'index'),
        'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'created' => array('column' => 'created', 'unique' => 0), 'active' => array('column' => 'active', 'unique' => 0), 'activity_feed_user_id' => array('column' => 'activity_feed_user_id', 'unique' => 0))
    );
}
?>
