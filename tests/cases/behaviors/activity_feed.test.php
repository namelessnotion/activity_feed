<?php
App::import('Core', array('AppModel', 'Model'));
App::import('Plugin', array('ActivityFeed.ActivityFeedAppModel'));
App::import('Model', array('ActivityFeed.ActivityFeed'));
require_once('activity_feed.models.php');

class ActivityFeedTest extends CakeTestCase {
    var $fixtures = array(
        'plugin.activity_feed.activity_feed',
        'plugin.activity_feed.activity_feed_post',
        'plugin.activity_feed.activity_feed_user',
        'plugin.activity_feed.activity_feed_comment'
    );

    var $defaultFields = array(
        'user_id',
        'model',
        'foreign_key',
        'subject',
        'verb',
        'preposition',
        'object'
    );

    function startTest() {
        $this->ActivityFeed = ClassRegistry::init('ActivityFeed.ActivityFeed');
        $this->Post = ClassRegistry::init('ActivityFeedPost');
    }

    function testCreate() {
        $this->Post->create();
        $this->Post->save(
            array(
                'title' => 'Test Post 1',
                'body' => 'Test Post 1 Body',
                'activity_feed_user_id' => '1',
                'active' => '1'
            )
        );

        $af = $this->ActivityFeed->find('first', array(
            'fields' => $this->defaultFields,
            'contain' => array(),
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ActivityFeedPost',
                'foreign_key' => $this->Post->getLastInsertId()
            )
        ));

        $expected = array(
            'user_id' => '1',
            'model' => 'ActivityFeedPost',
            'foreign_key' => ''.$this->Post->getLastInsertId().'',
            'subject' => 'user1',
            'verb' => 'posted',
            'preposition' => '',
            'object' => 'Test Post 1'
        );
        $this->assertEqual($af['ActivityFeed'], $expected);

        $this->Post->ActivityFeedComment->create();
        $this->Post->ActivityFeedComment->save( array(
            'activity_feed_user_id' => '2',
            'comment' => 'nice blog post',
            'activity_feed_post_id' => $this->Post->getLastInsertId()
        ));
        $comment = $this->ActivityFeed->find('first', array(
            'fields' => $this->defaultFields,
            'contain' => array(),
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ActivityFeedComment',
                'foreign_key' => $this->Post->ActivityFeedComment->getLastInsertId()
            )
        ));
        $expected = array(
            'user_id' => 2,
            'model' => 'ActivityFeedComment',
            'foreign_key' => $this->Post->ActivityFeedComment->getLastInsertId(),
            'subject' => 'user2',
            'verb' => 'commented',
            'preposition' => 'on',
            'object' => 'Test Post 1'
        );
        $this->assertEqual($comment['ActivityFeed'], $expected);
    }
}
