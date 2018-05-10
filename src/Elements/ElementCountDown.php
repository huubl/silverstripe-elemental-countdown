<?php

namespace Dynamic\Elements\CountDown\Elements;

use DNADesign\Elemental\Models\BaseElement;
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
    private static $icon = 'sectionnav-icon';

    /**
     * @var string
     */
    private static $singular_name = 'Count Down Element';

    /**
     * @var string
     */
    private static $plural_name = 'Count Down Elements';

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
    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Count Down');
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        if (!$this->End) {
            $result->addError('An end date is required');
        }

        return $result;
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
}
