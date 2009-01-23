<?php
class ActivityBehavior extends ModelBehavior {
    var $_defaultSettings = array(
        "sentence" => array(
            "subject" => "",
            "verb" => "",
            "preposition" => "",
            "object" => ""
        ),
        "saveOn" => array(
            "conditions" => array(),
            "created" => true
        ),
        'userForeignKey' => 'user_id'
    );

    var $runtime = array();

    function setup(&$Model, $config = array()) {
        $this->settings[$Model->alias] = Set::merge($this->_defaultSettings, $config);
    }


    /**
     * Use cases:
     * 1. Created, no saveOn Conditions, onSave.created = true
     * 2. Created, no saveOn Conditions, onSave.created = false //do nothing
     * 3. Created, has saveOn Conditions, onSave.created = true
     * 4. Created, has saveOn Conditions, onSave.created = false //do nothing 
     * 5. Updated, no saveOn Conditions, onSave.created = true //do nothing
     * 6. Updated, no saveOn Conditions, onSave.created = false //always save activity
     * 7. Updated, has saveOn conditions, onSave.created = true //do nothing 
     * 8. Updated, has saveOn conditions, onSave.created = false 
     */  

    function beforeSave(&$Model) {
        $data = array();
        if(!empty($this->settings[$Model->alias]['saveOn']['conditions'])) {
            if(isset($Model->data[$Model->alias])) {
                $data = &$Model->data[$Model->alias];
            } else {
                $data = &$Model->data;
            }
            if(!empty($data)) {
                if(isset($data[$Model->primaryKey])) {
                    $this->runtime[$Model->alias]['primaryKey'] = $data[$Model->primaryKey]; 
                    unset($data);
                } else if(isset($Model->id)) {
                    $this->runtime[$Model->alias]['primaryKey'] = $Model->id;
                }
            }
        }
        return true;
    }

    function afterSave(&$Model, $created) {
        $saveActivity = false;
        $validateActivity = false;
        $primaryKey = null;
        if($created) {
            if(empty($this->settings[$Model->alias]['saveOn']['conditions'])) {
                if($this->settings[$Model->alias]['saveOn']['created']) {
                    //Use Case 1
                   $primaryKey = $Model->getLastInsertId(); 
                    $saveActivity = true;

                } else {
                    //Use Case 2
                    //do nothing
                } 
            } else {
                if($this->settings[$Model->alias]['saveOn']['created']) {
                    //Use Case 3
                    $primaryKey = $Model->getLastInsertId();
                    $validateActivity = true;
                } else {
                    //Use Case 4
                    $primaryKey = $Model->getLastInsertId();
                    $validateActivity = true;
                } 
            }
        } else {
            if(empty($this->settings[$Model->alias]['saveOn']['conditions'])) {
                if($this->settings[$Model->alias]['saveOn']['created']) {
                    //Use Case 5
                    //do noting
                } else {
                    //Use Case 6
                    //always happens - not implemented yet
                    //$saveActivity = true;
                } 
            } else {
                if($this->settings[$Model->alias]['saveOn']['created']) {
                    //Use Case 7
                    //do noting
                } else {
                    //Use Case 8
                    $primaryKey = $this->runtime[$Model->alias]['primaryKey'];
                    $validateActivity = true;
                } 
            }
        }

        if($validateActivity && $primaryKey != null) {
            if($this->validateConditions($Model, $primaryKey)) {
                $saveActivity = true;
            }
        }

        if($saveActivity) {
            $this->saveActivity($Model, $primaryKey);
        }

        unset($this->runtime[$Model->alias]);
    }

    function getFieldsForConditions(&$Model, $primaryKey) {
        if(!empty($this->settings[$Model->alias]['saveOn']['conditions'])) {
            $conditions = array( $Model->alias .'.'. $Model->primaryKey => $primaryKey);
            $recursive = - 1;
            $fields = array_keys($this->settings[$Model->alias]['saveOn']['conditions']);
            return $Model->find("first", compact("conditions", "fields", "recursive"));
        }
        return false;
    }

    function validateConditions(&$Model, $primaryKey) {
        if(!empty($this->settings[$Model->alias]['saveOn']['conditions'])) {
            $data = $this->getFieldsForConditions(&$Model, $primaryKey);
            if(isset($data[$Model->alias])) {
                $data = $data[$Model->alias];
            }
            if(Set::isEqual($data, $this->settings[$Model->alias]['saveOn']['conditions'])) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    function getUserId(&$Model, $primaryKey) {
        $userForeignKey = $this->settings[$Model->alias]['userForeignKey'];
        $user_id = $Model->find("first", array(
            "fields" => array($userForeignKey), 
            "recursive" => -1, 
            "conditions" => array( $Model->alias. '.' .$Model->primaryKey => $primaryKey)
        ));
        if($user_id) {
            return $user_id[$Model->alias][$userForeignKey];
        }
        return $user_id;
    }

    function saveActivity(&$Model, $primaryKey) {
        $user_id = $this->getUserId($Model, $primaryKey);
        $sentence = $this->buildSentence(&$Model, $primaryKey);
        $data = array(
            "model" => $Model->alias,
            "foreign_key" => $primaryKey,
            "user_id" => $user_id,
            "subject" => $sentence['subject'],
            "verb" => $sentence['verb'],
            "preposition" => $sentence['preposition'],
            "object" => $sentence['object']
        );
        $binded = false;
        if(!isset($Model->ActivityFeed)) {
            $Model->bindModel(array("hasOne" => array("ActivityFeed")));
            $binded = true;
        }
        $Model->ActivityFeed->create();
        if(!$Model->ActivityFeed->save($data)) {
            debug("failed to save activity");
            debug($data);
        }
        if($binded) {
            $Model->unbindModel(array("hasOne" => array("ActivityFeed")));
        }

    }

    function buildSentence(&$Model, $primaryKey) {
        $fields = $conditions = $contain = $sentence = $pathes = array();
        

        $conditions = array($Model->alias .'.'. $Model->primaryKey => $primaryKey);
        foreach($this->settings[$Model->alias]['sentence'] as $key => $part) {
            if(is_array($part)) {

                if(is_array($part['model'])) {
                    $contain = am($contain, $part['model']);
                    $keys = array_keys($part['model']);
                    if(is_array($part['model'][$keys[0]])) {
                        foreach($part['model'][$keys[0]] as $key2 => $modelName) {
                            if(is_array($modelName)) {
                                $keys2 = array_keys($modelName);
                                $fields = am($fields, array($modelName[$keys2[0]] .'.'. $part['field']));
                                $pathes[$key][] = "/".$keys[0]."/".$key2."/".$modelName[$keys2[0]]."/".$part['field'];
                            } else {
                                $fields = am($fields, array($modelName .'.'. $part['field']));
                                $pathes[$key][] = "/".$keys[0]."/".$modelName."/".$part['field'];
                            }
                        }
                    }
                } else if($part['model'] != $Model->alias) {
                    $contain = am($contain, array($part['model']));
                }

                if(!is_array($part['model'])) {
                    $fields = am($fields, array($part['model'] .'.'. $part['field']));
                    $pathes[$key][] = "/".$part['model']."/".$part['field'];
                }
            } else {
                $sentence[$key] = $part; 
            }
        }
        
        if(!empty($fields)) {
            $data = $Model->find("first", compact("conditions", "contain"));
            foreach($this->settings[$Model->alias]['sentence'] as $key => $part) {
                if(isset($pathes[$key])) {
                    if(is_array($pathes[$key])) {
                        foreach($pathes[$key] as $path) {
                            $value = Set::extract($path, $data);
                            if(!empty($value)) {
                                $sentence[$key] = $value[0];
                                unset($value);
                                break;
                            }
                        }
                    } else {
                        $value = Set::extract($pathes[$key], $data);
                        $sentence[$key] = $value[0];
                        unset($value);
                    }
                } else {
                    $sentence[$key] = $part;
                }
            }
        }
        return $sentence;
    }

}
?>
