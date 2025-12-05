Teamup plugin for Craft
=================

Plugin that allows you to import Teamup entries.

## Requirements

This plugin requires Craft CMS 5.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require robuust/craft-teamup

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Teamup.

## Config

Create a file called `teamup.php` in you Craft config folder with the following contents:

```php
<?php

return [
    // General
    'apiToken' => '', // YOUR API TOKEN
    // Section
    'sectionHandle' => 'YOUR_EVENT_SECTION_HANDLE',
    'entryTypeHandle' => 'YOUR_EVENT_ENTRY_TYPE_HANDLE',
    // Fields
    'eventIdField' => 'YOUR_EVENT_ID_FIELD', // PlainText
    'locationField' => 'YOUR_EVENT_LOCATION_FIELD', // PlainText
    'startDateTimeField' => 'YOUR_EVENT_START_DATETIME_FIELD', // DateTime
    'endDateTimeField' => 'YOUR_EVENT_END_DATETIME_FIELD', // DateTime
    'unitField' => 'YOUR_EVENT_UNIT_FIELD', // PlainText
    'descriptionField' => 'YOUR_EVENT_DESCRIPTION_FIELD', // Redactor
    'attachmentsField' => 'YOUR_EVENT_ATTACHMENTS_FIELD', // Assets
];

```

## Usage

Run `craft teamup/import YOUR_CALENDAR_KEY` on the CLI to import the newest items.

Run `craft teamup/import/cleanup YOUR_CALENDAR_KEY` on the CLI to clean up old items.
