<?php

namespace robuust\teamup\console\controllers;

use craft\console\Controller;

/**
 * Import controller.
 */
class ImportController extends Controller
{
    /**
     * Import action.
     */
    public function actionIndex()
    {
        $events = $this->module->teamup->getEvents();
        $this->stdout('events found: '.count($events)."\n");

        // Import events
        $count = 0;
        foreach ($events as $event) {
            if ($this->module->teamup->importEvent($event)) {
                ++$count;
            }
        }
        $this->stdout("events imported: {$count}\n");
    }
}
