Teamup plugin for Craft
=================

Plugin that allows you to import Teamup entries.

## Requirements

This plugin requires Craft CMS 4.0.0 or later.

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
    'calendarKey' => '', // YOUR CALENDAR KEY
    'apiKey' => '', // YOUR API KEY
    // Section
    'sectionHandle' => 'YOUR_EVENT_SECTION_HANDLE',
    'entryTypeHandle' => 'YOUR_EVENT_ENTRY_TYPE_HANDLE',
    // Fields
    'eventIdField' => 'YOUR_EVENT_ID_FIELD', // PlainText
    'imageField' => 'YOUR_EVENT_IMAGE_FIELD', // Asset
    'textField' => 'YOUR_EVENT_TEXT_FIELD', // Redactor
];

```

## Usage

Run `craft teamup/import` on the CLI to import the newest items.
