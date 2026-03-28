<?php

namespace App\Support;

class Calendar
{
    public static function event($title, $allDay, $start, $end, $id = null, array $options = [])
    {
        $event = [
            'title' => $title,
            'allDay' => (bool) $allDay,
            'start' => static::normalizeDate($start),
            'end' => static::normalizeDate($end),
        ];

        if (! is_null($id)) {
            $event['id'] = $id;
        }

        return array_merge($event, $options);
    }

    public static function addEvents(array $events)
    {
        return (new CalendarBuilder())->addEvents($events);
    }

    private static function normalizeDate($date)
    {
        if ($date instanceof \DateTimeInterface) {
            return $date->format('Y-m-d\TH:i:s');
        }

        return (string) $date;
    }
}

class CalendarBuilder
{
    private $events = [];

    private $options = [];

    private $callbacks = [];

    private $id = 'default';

    public function addEvent(array $event, array $customAttributes = [])
    {
        $this->events[] = array_merge($event, $customAttributes);

        return $this;
    }

    public function addEvents(array $events)
    {
        foreach ($events as $event) {
            if (is_array($event)) {
                $this->events[] = $event;
            }
        }

        return $this;
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function setCallbacks(array $callbacks)
    {
        $this->callbacks = array_merge($this->callbacks, $callbacks);

        return $this;
    }

    public function setId($id)
    {
        $this->id = trim((string) $id) ?: 'default';

        return $this;
    }

    public function calendar()
    {
        return '<div id="calendar-'.e($this->id).'"></div>';
    }

    public function script()
    {
        $calendarId = 'calendar-'.addslashes($this->id);
        $options = $this->buildJavascriptOptions();

        return "<script type=\"text/javascript\">\n"
            ."(function(){\n"
            ."    if (typeof $ === \"undefined\" || typeof $.fn.fullCalendar === \"undefined\") { return; }\n"
            ."    var el = $(\"#{$calendarId}\");\n"
            ."    if (!el.length) { return; }\n"
            ."    el.fullCalendar({$options});\n"
            ."})();\n"
            .'</script>';
    }

    private function buildJavascriptOptions()
    {
        $entries = [];

        foreach ($this->options as $key => $value) {
            $entries[] = json_encode((string) $key).': '.json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $entries[] = 'events: '.json_encode($this->events, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        foreach ($this->callbacks as $callbackName => $callbackBody) {
            $entries[] = json_encode((string) $callbackName).': '.$callbackBody;
        }

        return '{'.implode(',', $entries).'}';
    }
}
