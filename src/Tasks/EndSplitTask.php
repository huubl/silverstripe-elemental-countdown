<?php

namespace Dynamic\Elements\CountDown\Tests;

use IntlDateFormatter;
use Dynamic\Elements\CountDown\Elements\ElementCountDown;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\Connect\Query;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataObjectSchema;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\Queries\SQLSelect;

/**
 * Class EndSplitTask
 * @package Dynamic\Elements\CountDown\Tests
 */
class EndSplitTask extends BuildTask
{
    /**
     * @var string
     */
    protected $title = 'Countdown Element End Update';

    /**
     * @var string
     */
    protected $description = "Splits the countdown's End field into EndDate and EndTime";

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @param $request
     */
    public function run($request)
    {
        if (!$this->endFieldExists()) {
            return;
        }

        $count = 0;

        /** @var ElementCountDown|\SilverStripe\Versioned\Versioned $element */
        foreach ($this->iterateElements() as $element) {
            if ($element->EndDate && $element->EndTime) {
                continue;
            }

            $published = $element->isPublished();
            $end = $this->getEndField($element->ID);

            // because $end->Date() doesn't return in ISO format, nor does it allow a custom format to be passed
            $formatter = $end->getCustomFormatter(
                null,
                $end->getISOFormat(),
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::NONE
            );
            $element->EndDate = $formatter->format($end->getTimestamp());
            $element->EndTime = $end->Time();

            $element->write();
            if ($published) {
                $element->publishRecursive();
            }
            $count++;
        }
        echo "Updated {$count} countdown elements.";
    }

    /**
     * @return \Generator
     */
    public function iterateElements()
    {
        foreach (ElementCountDown::get() as $element) {
            yield $element;
        }
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return ElementCountDown::getSchema()->tableName(ElementCountDown::class);
    }

    /**
     * @return bool
     */
    public function endFieldExists()
    {
        return array_key_exists('End', DB::field_list($this->getTable()));
    }

    /**
     * @param int $id
     *
     * @return DBDatetime
     */
    public function getEndField($id)
    {
        $query = new SQLSelect();
        $query->setFrom($this->getTable());
        $query->addWhere('"ID"='. $id);
        $query->setLimit(1);
        $results = $query->execute();

        $record = $results->record();
        return DBField::create_field('DBDatetime', $record['End']);
    }
}
