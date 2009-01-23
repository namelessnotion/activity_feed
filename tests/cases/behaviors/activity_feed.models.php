<?php
if (!defined('CAKEPHP_UNIT_TEST_EXECUTION')) {
	define('CAKEPHP_UNIT_TEST_EXECUTION', 1);
}
class ActivityFeedPost extends CakeTestModel {
    var $belongsTo = array('ActivityFeedUser');
    var $hasMany = array('ActivityFeedComment');
    var $actsAs = array(
        'Containable',
        'ActivityFeed.Activity' => array(
            'sentence' => array(
                'subject' => array('model' => 'ActivityFeedUser', 'field' => 'screenname'),
                'verb' => 'posted',
                'preposition' => '',
                'object' => array('model' => 'ActivityFeedPost', 'field' => 'title')
            ),
            'saveOn' => array(
                'created' => true
            ),
            'userForeignKey' => 'activity_feed_user_id'
        )
    );
}

class ActivityFeedComment extends CakeTestModel {
    var $belongsTo = array('ActivityFeedPost', 'ActivityFeedUser');
    var $actsAs = array(
        'Containable',
        'ActivityFeed.Activity' => array(
            'sentence' => array(
                'subject' => array('model' => 'ActivityFeedUser', 'field' => 'screenname'),
                'verb' => 'commented',
                'preposition' => 'on',
                'object' => array('model' => 'ActivityFeedPost', 'field' => 'title')
            ),
            'saveOn' => array(
                'created' => true
            ),
            'userForeignKey' => 'activity_feed_user_id'
        )
    );
}

class ActivityFeedUser extends CakeTestModel {
    var $hasMany = array('ActivityFeedPost');
}

?>
