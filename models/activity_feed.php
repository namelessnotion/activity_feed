<?php
class ActivityFeed extends ActivityFeedAppModel {
    var $belongsTo = array("ActivityFeedUser" => array('foreignKey' => 'user_id'));
    var $actsAs = array(
        "Containable",
        "ActivityFeed.Polymorphic" => array(
            "classField" => 'model',
            "foreignKey" => 'foreign_key'
        )
    );
}
?>
