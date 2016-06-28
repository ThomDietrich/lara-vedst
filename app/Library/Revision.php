<?php
/**
 * Created by PhpStorm.
 * User: Ludwig
 * Date: 23.06.2016
 * Time: 16:03
 */

namespace Lara\Library;

use Illuminate\Database\Eloquent\Model;
use Lara\RevisionEntry;
use Illuminate\Support\Facades\Session;


/**
 * Class Revision
 * @package Lara\Library
 */
class Revision
{
    /**
     * @var Model
     */
    private $old_model;

    /**
     * @var string[]
     */
    private $ignoreArray = ["created_at", "updated_at", "deleted_at", "survey_question_id", "survey_answer_id", "survey_question_id", "survey_id", "creator_id", "id"];

    /**
     * Revision constructor.
     * @param Model $old_model
     */
    public function __construct($old_model)
    {
        $this->old_model = clone $old_model;
    }


    /**
     * @param Model $new_model
     * @return bool
     */
    public function save(Model $new_model)
    {
        if($new_model->getTable() !== $this->old_model->getTable()) {
            // old and new model dont have the same class -> they are not compareable
            return false;
        }
        if($new_model == $this->old_model) {
            // no changes -> no entry
            return false;
        }

        $revision = new \Lara\Revision();
        $revision->creator_id = Session::get('userId');
        $revision->ip = request()->ip();
        $revision->object_id = $new_model->id;
        $revision->object_name = $this->parse_classname(get_class($new_model))['classname'];

        if ($new_model->wasRecentlyCreated) {     // empty($this->old_model->attributesToArray())
            // new entry
            $revision->summary = "created new ".$this->parse_classname(get_class($new_model))['classname'];
        } elseif (!$new_model->exists) {
            // deleted entry
            $revision->summary = "deleted ".$this->parse_classname(get_class($new_model))['classname'];
        } else {
            // update entry
            $revision->summary = "updated ".$this->parse_classname(get_class($new_model))['classname'];
        }
        $revision->save();


        if ($new_model->wasRecentlyCreated) {     // empty($this->old_model->attributesToArray())
            // new entry
            foreach($new_model->attributesToArray() as $column_name => $column_value) {
                $this->save_revision_entry($column_name, $column_value, $revision->id, "create");
            }
        } elseif (!$new_model->exists) {
            // deleted entry
            foreach($this->old_model->attributesToArray() as $column_name => $column_value) {
                $this->save_revision_entry($column_name, $column_value, $revision->id, "delete");
            }
        } else {
            // update entry
            foreach($new_model->attributesToArray() as $column_name => $column_value) {
                if($column_value != $this->old_model->attributesToArray()[$column_name]) {
                    $this->save_revision_entry($column_name, $column_value, $revision->id, "update");
                }
            }
        }
        return true;
    }

    /**
     * @param Model $model
     * @return string
     */
    protected function parse_relation_model_name(Model $model)
    {
        $class = $this->parse_classname(get_class($model));
        $path = "\\".$class['namespace'][0]."\\Revision_".$class['classname'];
        return $path;
    }

    /**
     * @param string $name
     * @return array
     */
    protected function parse_classname ($name)
    {
        return array(
            'namespace' => array_slice(explode('\\', $name), 0, -1),
            'classname' => join('', array_slice(explode('\\', $name), -1)),
        );
    }

    /**
     * @param string $column_name
     * @param string $column_value
     * @param int $revision_id
     * @param string $type "create" | "update" | "delete"
     * @return bool
     */
    protected function save_revision_entry($column_name, $column_value, $revision_id, $type)
    {
        if (in_array($column_name, $this->ignoreArray)) {
            // filter columns which should not be shown in revisions
            return false;
        }
        if ($type != "create" AND $type != "update" AND $type != "delete") {
            // type needs to be one of those 3 options
            return false;
        }

        $revision_entry = new RevisionEntry();
        $revision_entry->revision_id = $revision_id;
        $revision_entry->changed_column_name = $column_name;
        switch ($type) {
            case "create":
                $revision_entry->new_value = $column_value;
                break;
            case "delete":
                $revision_entry->old_value = $column_value;
                break;
            case "update":
                $revision_entry->new_value = $column_value;
                $revision_entry->old_value = $this->old_model->attributesToArray()[$column_name];
        }
        return $revision_entry->save();
    }
}