<?php

namespace Fasterfy;


class Filter
{
    protected static $timeFilters = array();
    protected static $categoryExecutionFilters = array();
    protected static $nameExecutionFilters = array();
    protected static $categoryExecutionCounter = array();
    protected static $nameExecutionCounter = array();
    protected static $categoryBlacklist = array();
    protected static $nonValidNames = array();

    public static function blacklist($category)
    {
        self::$categoryBlacklist[] = $category;
    }
    public static function slowerThan($category, $minimalTime)
    {
        self::$timeFilters[$category] = $minimalTime;
    }

    public static function categoryExecutedMoreThan($category,$minimalExecutions)
    {
        self::$categoryExecutionFilters[$category] = $minimalExecutions;
    }

    public static function nameExecutedMoreThan($category,$minimalExecutions)
    {
        self::$nameExecutionFilters[$category] = $minimalExecutions;
    }

    public static function count($category,$name)
    {
        if (!isset(self::$categoryExecutionCounter[$category])) {
            self::$categoryExecutionCounter[$category] = 0;
        }
        self::$categoryExecutionCounter[$category]++;

        if (!isset(self::$nameExecutionCounter[$name])) {
            self::$nameExecutionCounter[$name] = 0;
        }
        self::$nameExecutionCounter[$name]++;
    }

    public static function isValid(Event $event){

        $category = $event->getCategory();
        $name = $event->getName();

        $validTime = self::isTimeValid($event);
        $validCategoryCounter = self::isCategoryCounterValid($event);
        $validNameCounter = self::isNameCounterValid($event);

        if (in_array($category,self::$categoryBlacklist)) {
            return false;
        }



        $regras = array();
        if ($validTime !== null) {
            $regras[] = $validTime;
        }

        if ($validCategoryCounter !== null) {
            $regras[] = $validCategoryCounter;
        }

        if ($validNameCounter !== null) {
            $regras[] = $validNameCounter;
        }

        if (!$regras || in_array(true,$regras)) {
            return true;
        }

        return false;
    }

    protected static function isTimeValid(Event $event)
    {
        $executionTime = $event->getAccumulatedExecutionTime();
        $validTime = true;
        if (isset(self::$timeFilters[$event->getCategory()])) {
            if ($executionTime < self::$timeFilters[$event->getCategory()]) {
                $validTime = false;
            } else {
                $event->setFlagExecutionTime(true);
            }
        } else {
            return null;
        }
        return $validTime;
    }

    protected static function isCategoryCounterValid(Event $event)
    {
        $valid = true;
        $category = $event->getCategory();
        $counter = 0;
        if (isset(self::$categoryExecutionCounter[$category])) {
            $counter = self::$categoryExecutionCounter[$category];
        }

        if (isset(self::$categoryExecutionFilters[$category])) {
            $limit = self::$categoryExecutionFilters[$category];

            if ($counter < $limit) {
                $valid = false;
            }
        } else {
            return null;
        }
        return $valid;
    }

    protected static function isNameCounterValid(Event $event)
    {
        $valid = true;
        $category = $event->getCategory();
        $name = $event->getName();
        $counter = $event->getNameRepeats();

        if (isset(self::$nameExecutionFilters[$category])) {

            $limit = self::$nameExecutionFilters[$category];

            if ($counter < $limit || in_array($name,self::$nonValidNames)) {
                $valid = false;
            } else {
                $event->setFlagNameRepeats(true);
                self::$nonValidNames[] = $name;
            }
        } else {
            return null;
        }
        return $valid;
    }

    public static function getCategoryCounter()
    {
        return self::$categoryExecutionCounter;
    }
}
