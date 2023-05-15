<?php

namespace robuust\teamup\console\controllers;

use craft\console\Controller;
use yii\console\ExitCode;

/**
 * Import controller.
 */
class ImportController extends Controller
{
    /**
     * Import action.
     *
     * @param string $calendarKey
     *
     * @return int
     */
    public function actionIndex(string $calendarKey): int
    {
        $events = $this->module->teamup->getEvents($calendarKey);
        $this->stdout('events found: '.count($events)."\n");

        // Import events
        $count = 0;
        foreach ($events as $event) {
            if ($this->module->teamup->importEvent($event)) {
                ++$count;
            }
        }
        $this->stdout("events imported: {$count}\n");

        return ExitCode::OK;
    }
}
