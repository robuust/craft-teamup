<?php

namespace robuust\teamup\services;

use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\fields\Assets as AssetField;
use craft\helpers\Assets as AssetsHelper;
use craft\helpers\FileHelper;
use craft\helpers\Json;
use craft\services\Entries;
use craft\services\Fields;
use DateTime;
use Exception;
use robuust\teamup\models\Settings;
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
     * @var Entries
     */
    protected $entries;

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
        $this->entries = Craft::$app->getEntries();

        $this->section = $this->entries->getSectionByHandle($this->settings->sectionHandle);
        $this->entryType = $this->entries->getEntryTypeByHandle($this->settings->entryTypeHandle);
    }

    /**
     * Get teamup events.
     *
     * @param string $calendarKey
     *
     * @return array
     */
    public function getEvents(string $calendarKey): array
    {
        // Get events
        $request = Craft::createGuzzleClient([
            'base_uri' => static::URL,
            'headers' => [
                'Teamup-Token' => $this->settings->apiToken,
            ],
        ])->get($calendarKey.'/events', [
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

        return $query->status(null)->one();
    }

    /**
     * Get all entries.
     *
     * @return array
     */
    public function getEntries(): array
    {
        $query = Entry::find()->section($this->section);
        $query->orderBy($this->settings->startDateTimeField.' asc');

        return $query->status(null)->all();
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
            $entry->{$this->settings->eventIdField} = $event['id'];
        }

        $entry->title = $event['title'];
        $entry->{$this->settings->locationField} = $event['location'];
        $entry->{$this->settings->startDateTimeField} = new DateTime($event['start_dt']);
        $entry->{$this->settings->endDateTimeField} = new DateTime($event['end_dt']);
        $entry->{$this->settings->unitField} = $event['who'];
        $entry->{$this->settings->descriptionField} = $event['notes'];
        $entry->{$this->settings->attachmentsField} = $this->getAttachments($event['attachments']);

        return Craft::$app->getElements()->saveElement($entry);
    }

    /**
     * Get attachments.
     *
     * @param array $attachments
     *
     * @return array
     */
    public function getAttachments(array $attachments): array
    {
        $ids = [];

        foreach ($attachments as $attachment) {
            $content = @file_get_contents($attachment['link']);
            if ($content) {
                // Temporarily save file to disk
                $tempPath = AssetsHelper::tempFilePath($attachment['name']);
                try {
                    FileHelper::writeToFile($tempPath, $content);
                } catch (Exception $e) {
                    Craft::warning(sprintf('Error writing asset for %s. Message: %s', $attachment['name'], $e->getMessage()));
                }

                // Get upload folder
                /** @var Fields $fields */
                $fields = Craft::$app->getFields();
                /** @var AssetField $field */
                $field = $fields->getFieldByHandle($this->settings->attachmentsField);
                $uploadFolderId = $field->resolveDynamicPathToFolderId();
                $uploadFolder = Craft::$app->getAssets()->getFolderById($uploadFolderId);

                // Create new asset
                $asset = new Asset();
                $asset->tempFilePath = $tempPath;
                $asset->setFilename($attachment['name']);
                $asset->newFolderId = $uploadFolderId;
                $asset->setVolumeId($uploadFolder->volumeId);
                $asset->avoidFilenameConflicts = true;
                $asset->setScenario(Asset::SCENARIO_CREATE);

                try {
                    if (Craft::$app->getElements()->saveElement($asset)) {
                        $ids[] = $asset->id;
                    }
                } catch (Exception $e) {
                    // do nothing
                }
            }
        }

        return $ids;
    }

    /**
     * Clean up event.
     *
     * @param Entry $entry
     * @param array $events
     */
    public function cleanupEvent(Entry $entry, array $events): bool
    {
        foreach ($events as $event) {
            if ($event['id'] == $entry->{$this->settings->eventIdField}) {
                return false;
            }
        }

        return Craft::$app->getElements()->deleteElement($entry);
    }
}
