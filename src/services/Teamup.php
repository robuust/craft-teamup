<?php

namespace robuust\teamup\services;

use Craft;
use craft\elements\Entry;
use craft\helpers\Json;
use DateTime;
use robuust\teamup\Plugin;
use yii\base\Component;

/**
 * Teamup service.
 */
class Teamup extends Component
{
    /**
     * @var string
     */
    public const URL = 'https://api.teamup.com';

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var Sections
     */
    protected $sections;

    /**
     * @var Section
     */
    protected $section;

    /**
     * @var EntryType
     */
    protected $entryType;

    /**
     * Initialize service.
     */
    public function init()
    {
        $this->settings = Plugin::getInstance()->getSettings();
        $this->sections = Craft::$app->getSections();

        $this->section = $this->sections->getSectionByHandle($this->settings->sectionHandle);
        list($this->entryType) = $this->sections->getEntryTypesByHandle($this->settings->entryTypeHandle);
    }

    /**
     * Get teamup events.
     *
     * @return array
     */
    public function getEvents(): array
    {
        // Get events
        $request = Craft::createGuzzleClient([
            'base_uri' => static::URL,
            'headers' => [
                'Teamup-Token' => $this->settings->apiToken,
            ],
        ])->get($this->settings->calendarKey.'/events', [
            'query' => [
                'startDate' => 'today',
                'endDate' => 'today+1year',
            ],
        ]);

        $response = Json::decode((string) $request->getBody());

        return $response['events'];
    }

    /**
     * Get entry by event id.
     *
     * @param string $eventId
     *
     * @return Entry|null
     */
    public function getEntry(string $eventId): ?Entry
    {
        $query = Entry::find()->section($this->section);
        $query[$this->settings->eventIdField] = $eventId;

        return $query->anyStatus()->one();
    }

    /**
     * Import event.
     *
     * @param array $event
     *
     * @return bool
     */
    public function importEvent(array $event): bool
    {
        $entry = $this->getEntry($event['id']);

        if (!$entry) {
            $entry = new Entry();
            $entry->sectionId = $this->section->id;
            $entry->typeId = $this->entryType->id;
            $entry->enabled = true;
            $entry->title = $event['title'];
            $entry->setFieldValues([
                $this->settings->eventIdField => $event['id'],
                $this->settings->locationField => $event['location'],
                $this->settings->startDateTimeField => new DateTime($event['start_dt']),
                $this->settings->endDateTimeField => new DateTime($event['end_dt']),
                $this->settings->unitField => $event['who'],
                $this->settings->descriptionField => $event['notes'],
            ]);

            // @TODO attachments

            return Craft::$app->getElements()->saveElement($entry);
        }

        return false;
    }
}
