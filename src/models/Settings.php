<?php

namespace robuust\teamup\models;

use craft\base\Model;

/**
 * Settings model.
 */
class Settings extends Model
{
    /**
     * @var string
     */
    public $calendarKey;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $sectionHandle;

    /**
     * @var string
     */
    public $entryTypeHandle;

    /**
     * @var string
     */
    public $eventIdField;

    /**
     * @var string
     */
    public $imageField;

    /**
     * @var string
     */
    public $textField;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['calendarKey', 'apiKey', 'sectionHandle', 'entryTypeHandle', 'eventIdField', 'imageField', 'textField'], 'required'],
        ];
    }
}
