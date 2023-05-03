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
    const URL = 'https://api.teamup.com';

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
            'baseUrl' => static::URL,
            'headers' => [
                'Teamup-Token' => $this->settings->apiToken,
            ],
        ])->get($this->settings->calendarKey.'/events');

        return Json::decode((string) $request->getBody());
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
        $entry = $this->getEntry($event['ID']);

        if (!$entry) {
            $image = base64_encode(file_get_contents($event['Newsevent']['ImageUrl']));

            $entry = new Entry();
            $entry->sectionId = $this->section->id;
            $entry->typeId = $this->entryType->id;
            $entry->enabled = true;
            $entry->title = $event['Title'];
            $entry->postDate = new DateTime($event['Date']);
            $entry->setFieldValues([
                $this->settings->eventIdField => $event['ID'],
                $this->settings->imageField => ['data' => ['data:image/jpeg;base64,'.$image], 'filename' => [$event['ID'].'.jpg']],
                $this->settings->textField => $event['Newsevent']['PublicText'],
            ]);

            return Craft::$app->getElements()->saveElement($entry);
        }

        return false;
    }
}
