<?php

namespace Dynamic\Elements\CountDown\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\ArrayData;

/**
 * Class ElementCountDown
 * @package Dynamic\Elements\Elements
 *
 * @property string $End
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
        return DBField::create_field(
            'HTMLText',
            'Count down to ' . $end->Date() . ' ' . $end->Time()
        )->Summary(20);
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
            'End' => $this->End,
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
            $array[$key] = json_encode($val);
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
