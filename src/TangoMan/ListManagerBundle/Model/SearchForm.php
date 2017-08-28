<?php

namespace TangoMan\ListManagerBundle\Model;

use TangoMan\ListManagerBundle\Model\SearchInput;

/**
 * Class SearchForm
 *
 * @package TangoMan\ListManagerBundle\Model
 */
class SearchForm
{
    /**
     * @var array
     */
    private $inputs = [];

    /**
     * @var integer
     */
    private $count;

    /**
     * @param array $inputs
     *
     * @return $this
     */
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;

        return $this;
    }

    /**
     * @return array $inputs
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\SearchInput $input
     *
     * @return bool
     */
    public function hasInput(SearchInput $input)
    {
        if (in_array($input, $this->inputs)) {
            return true;
        }

        return false;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\SearchInput $input
     *
     * @return $this
     */
    public function addInput(SearchInput $input)
    {
        if (!$this->hasInput($input)) {
            $this->inputs[] = $input;
        }

        return $this;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\SearchInput $input
     *
     * @return $this
     */
    public function removeInput(SearchInput $input)
    {
        $inputs = $this->inputs;

        if ($this->hasInput($input)) {
            $remove[] = $input;
            $this->inputs = array_diff($inputs, $remove);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return SearchForm
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }
}
