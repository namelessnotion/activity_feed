<?php
class ActivityFeedUserFixture extends CakeTestFixture {
    var $name = 'ActivityFeedUser';
    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
        'screenname' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'key' => 'index'),
        'password' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'key' => 'index'),
        'active' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'key' => 'index'),
        'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'key' => 'index'),
        'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'screenname' => array('column' => 'screenname', 'unique' => 0), 'active' => array('column' => 'active', 'unique' => 0), 'password' => array('column' => 'password', 'unique' => 0), 'created' => array('column' => 'created', 'unique' => 0))
    );

    var $records = array(
        array(
            'id' => '1',
            'screenname' => 'user1',
            'password' => 'password',
            'active' => '1',
            'created' => '2009-01-01 00:00:00',
            'modified' => '2009-01-01 00:00:00'
        ),
        array(
            'id' => '2',
            'screenname' => 'user2',
            'password' => 'password',
            'active' => '1',
            'created' => '2009-01-01 00:00:00',
            'modified' => '2009-01-01 00:00:00'
        )
    );
}
?>

