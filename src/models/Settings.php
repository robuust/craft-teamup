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
    public $apiToken;

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
    public $locationField;

    /**
     * @var string
     */
    public $startDateTimeField;

    /**
     * @var string
     */
    public $endDateTimeField;

    /**
     * @var string
     */
    public $unitField;

    /**
     * @var string
     */
    public $descriptionField;

    /**
     * @var string
     */
    public $attachmentsField;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['calendarKey', 'apiToken', 'sectionHandle', 'entryTypeHandle', 'eventIdField', 'locationField', 'startDateTimeField', 'endDateTimeField', 'unitField', 'descriptionField', 'attachmentsField'], 'required'],
        ];
    }
}
