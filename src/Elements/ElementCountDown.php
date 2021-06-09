<?php

namespace Dynamic\Elements\CountDown\Elements;

use \DateTime;
use \DateTimeZone;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\ArrayData;

/**
 * Class ElementCountDown
 * @package Dynamic\Elements\Elements
 *
 * @property string $End
 * @property string Timezone
 * @property boolean $ShowMonths
 * @property boolean $ShowSeconds
 * @property boolean $Elapse
 */
class ElementCountDown extends BaseElement
{
    /**
     * @var string
     */
    private static $icon = 'font-icon-clock';

    /**
     * @var string
     */
    private static $singular_name = 'Countdown Element';

    /**
     * @var string
     */
    private static $plural_name = 'Countdown Elements';

    /**
     * @var string
     */
    private static $description = 'Displays a countdown to a specific date and time.';

    /**
     * @var array
     */
    private static $db = [
        'End' => 'DBDatetime',
        'Timezone' => 'Varchar(20)',
        'ShowMonths' => 'Boolean',
        'ShowSeconds' => 'Boolean',
        'Elapse' => 'Boolean',
    ];

    /**
     * @var string
     */
    private static $table_name = 'ElementCountDown';

    /**
     * @var ArrayData
     */
    private $client_config;

    /**
     * @return string
     */
    public function getSummary()
    {
        $end = $this->dbObject('End');
        $timezone = $this->dbObject('Timezone');
        return DBField::create_field(
            'HTMLText',
            trim("Count down to {$end->Date()} {$end->Time()} {$timezone}")
        )->Summary(20);
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->replaceField(
                'Timezone',
                DropdownField::create('Timezone')
                    ->setSource($this->getTimezoneList())
            );
        });
        return parent::getCMSFields();
    }

    /**
     * originally from https://davidhancock.co/2013/05/generating-a-list-of-timezones-with-php/
     * @return array
     */
    protected function getTimezoneList()
    {
        $timezoneIdentifiers = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $utcTime = new DateTime('now', new DateTimeZone('UTC'));

        $tempTimezones = [];
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new DateTimeZone($timezoneIdentifier);

            $tempTimezones[] = array(
                'offset' => (int)$currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier,
            );
        }

        // Sort the array by offset,identifier ascending
        usort($tempTimezones, function ($a, $b) {
            if ($a['offset'] == $b['offset']) {
                return strcmp($a['identifier'], $b['identifier']);
            }
            return $a['offset'] - $b['offset'];
        });

        $timezoneList = [];
        foreach ($tempTimezones as $tz) {
            $sign = ($tz['offset'] > 0) ? '+' : '-';
            $offset = gmdate('H:i', abs($tz['offset']));
            $timezoneList["UTC{$sign}{$offset}"] = "(UTC {$sign}{$offset}) {$tz['identifier']}";
        }

        return $timezoneList;
    }

    /**
     * @return array
     */
    protected function provideBlockSchema()
    {
        $blockSchema = parent::provideBlockSchema();
        $blockSchema['content'] = $this->getSummary();
        return $blockSchema;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Countdown');
    }

    /**
     * @return $this
     */
    public function setClientConfig()
    {
        $clientArray = [
            'End' => trim("{$this->End}  {$this->Timezone}"),
            'Elapse' => $this->Elapse,
        ];

        $this->client_config = ArrayData::create($this->encodeArrayValues($clientArray));

        return $this;
    }

    /**
     * @return ArrayData
     */
    public function getClientConfig()
    {
        if (!$this->client_config) {
            $this->setClientConfig();
        }

        return $this->client_config;
    }

    /**
     * @param $array
     * @return mixed
     */
    protected function encodeArrayValues($array)
    {
        foreach ($array as $key => $val) {
            $array[$key] = trim(json_encode($val));
        }

        return $array;
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        // skip if not written
        if (!$this->isInDB()) {
            return $result;
        }

        // skip if only sort changed
        $changed = $this->getChangedFields(true);
        if (count($changed) == 1 && array_key_exists('Sort', $changed)) {
            return $result;
        }

        if (!$this->End) {
            $result->addError('An end date and time is required before saving the Countdown Element record');
        }

        return $result;
    }
}
