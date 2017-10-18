<?php
namespace Fasterfy;


class Event
{

    protected $category;
    protected $name;
    protected $startedAt;
    protected $endedAt;
    protected $executionTime;
    protected $childs = array();
    protected $tags;
    protected $properties;
    protected $nameRepeats=false;
    protected $flagExecutionTime=false;
    protected $flagNameRepeats=false;

    function __construct($category,$name="")
    {
        $this->setCategory($category);
        $this->setName($name);
        Filter::count($category,$name);
        $this->start();
    }

    public function addChild(Event $child)
    {
        $this->childs[] = $child;
        return $this;
    }

    protected function start()
    {
        $this->setStartedAt(microtime(true));
        return $this;
    }

    public function stop($deep=false)
    {
        if (!$this->getEndedAt()) {
            $this->setEndedAt(microtime(true));
            $this->setExecutionTime($this->getEndedAt() - $this->getStartedAt());
        }

        if ($deep) {
            foreach ($this->getChilds() as $child) {
                $child->stop($deep);
            }
        }
        return $this;
    }

    /**
     * Get the value of Category
     *
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the value of Category
     *
     * @param mixed category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of Started At
     *
     * @return mixed
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set the value of Started At
     *
     * @param mixed startedAt
     *
     * @return self
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get the value of Ended At
     *
     * @return mixed
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * Set the value of Ended At
     *
     * @param mixed endedAt
     *
     * @return self
     */
    public function setEndedAt($endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * Get the value of Execution Time
     *
     * @return mixed
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * Set the value of Execution Time
     *
     * @param mixed executionTime
     *
     * @return self
     */
    public function setExecutionTime($executionTime)
    {
        $this->executionTime = $executionTime;

        return $this;
    }

    /**
     * Get the value of Childs
     *
     * @return mixed
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set the value of Childs
     *
     * @param mixed childs
     *
     * @return self
     */
    public function setChilds(array $childs)
    {
        foreach ($childs as $child) {
            if (!$child instanceof Event) {
                throw new Exception("Trying to add a child that isn't an Event to a Fasterfy event", 1);
            }
        }
        $this->childs = $childs;

        return $this;
    }

    /**
     * Get the value of Tags
     *
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the value of Tags
     *
     * @param mixed tags
     *
     * @return self
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get the value of Properties
     *
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set the value of Properties
     *
     * @param mixed properties
     *
     * @return self
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }


    public function toJson($prettyPrint=false,$filter=false)
    {

        $array = $this->toArray($filter);
        if ($array) {
            if ($prettyPrint) {
                return json_encode($array,JSON_PRETTY_PRINT);
            }
            return json_encode($array);
        } else {
            return null;
        }

    }

    public function toArray($filter=false)
    {
        $started = new DateTime($this->getStartedAt());

        $array = array(
            "name" => $this->getName(),
            "category" => $this->getCategory(),
            "startedAt" => $started->format("Y-m-d H:i:s.u")(),
            "endedAt" => $this->getEndedAt(),
            "executionTime" => $this->getExecutionTime(),
            "tags" => $this->getTags(),
            "properties" => $this->getproperties(),
            "childs" => array(),
        );
        if ($this->getChilds()) {
            foreach ($this->getChilds() as $child) {
                $result = $child->toArray($filter);
                if ($result) {
                    $array['childs'][] = $result;
                }

            }
        }

        if (Filter::isValid($this) or !$filter or $array['childs']) {
            $array["flagExecutionTime"] = $this->getFlagExecutionTime();
            $array["flagNameRepeats"] = $this->getFlagNameRepeats();
            $array["nameRepeats"] = $this->getNameRepeats();
            return $array;
        }

        return false;



    }

    public function isStopped(){
        return ($this->executionTime) ? true : false;
    }

    public function __set($key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    public function __get($key)
    {
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        } else {
            throw new Exception("Trying to access an non defined property of FasterFy Event", 1);
        }
    }


    /**
     * Get the value of Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param mixed name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * Get the value of  Name Repeats
     *
     * @return mixed
     */
    public function getNameRepeats()
    {
        return $this->nameRepeats;
    }

    /**
     * Set the value of  Name Repeats
     *
     * @param mixed nameRepeats
     *
     * @return self
     */
    public function setNameRepeats($nameRepeats)
    {
        $this->nameRepeats = $nameRepeats;

        return $this;
    }


    /**
     * Get the value of Flag Execution Time
     *
     * @return mixed
     */
    public function getFlagExecutionTime()
    {
        return $this->flagExecutionTime;
    }

    /**
     * Set the value of Flag Execution Time
     *
     * @param mixed flagExecutionTime
     *
     * @return self
     */
    public function setFlagExecutionTime($flagExecutionTime)
    {
        $this->flagExecutionTime = $flagExecutionTime;

        return $this;
    }


    /**
     * Get the value of Flag Repeats
     *
     * @return mixed
     */
    public function getFlagNameRepeats()
    {
        return $this->flagNameRepeats;
    }

    /**
     * Set the value of Flag Repeats
     *
     * @param mixed flagNameRepeats
     *
     * @return self
     */
    public function setFlagNameRepeats($flagNameRepeats)
    {
        $this->flagNameRepeats = $flagNameRepeats;

        return $this;
    }

}
