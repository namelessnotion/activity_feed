<?php
class ActivityFeed extends AppModel {
    var $belongsTo = array("User");
    var $actsAs = array(
        "Containable",
        "Polymorphic" => array(
            "classField" => 'model',
            "foreignKey" => 'foreign_key'
        )
    );
}
?>
