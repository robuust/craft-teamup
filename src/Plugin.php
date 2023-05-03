<?php

namespace robuust\teamup;

use robuust\teamup\models\Settings;
use robuust\teamup\services\Teamup;

/**
 * Teamup plugin.
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * Initialize plugin.
     */
    public function init()
    {
        parent::init();

        // Register services
        $this->setComponents([
            'teamup' => Teamup::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}
