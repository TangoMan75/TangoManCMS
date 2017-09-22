<?php

namespace TangoMan\FrontBundle\Model;

/**
 * Class Modal
 *
 * @package TangoMan\FrontBundle\Model
 */
class Modal implements \JsonSerializable
{
    /**
     * Modal id
     *
     * @var string
     */
    private $id;

    /**
     * Modal class
     * e.g.: 'modal fade'
     *
     * @var string
     */
    private $class;

    /**
     * Title to be displayed
     *
     * @var string
     */
    private $title;

    /**
     * Text to be displayed
     *
     * @var string
     */
    private $text;

    /**
     * Modal header
     *
     * @var string
     */
    private $header;

    /**
     * Modal body
     *
     * @var string
     */
    private $body;

    /**
     * Button collection
     *
     * @var array
     */
    private $buttons = [];

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Modal
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return Modal
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Modal
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return Modal
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     *
     * @return Modal
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return Modal
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param array $buttons
     *
     * @return $this
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @return array $buttons
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param String $button
     *
     * @return bool
     */
    public function hasButton($button)
    {
        if (in_array($button, $this->buttons)) {
            return true;
        }

        return false;
    }

    /**
     * @param String $button
     *
     * @return $this
     */
    public function addButton($button)
    {
        if (!$this->hasButton($button)) {
            $this->buttons[] = $button;
        }

        return $this;
    }

    /**
     * @param String $button
     *
     * @return $this
     */
    public function removeButton($button)
    {
        $buttons = $this->buttons;

        if ($this->hasButton($button)) {
            $remove[] = $button;
            $this->buttons = array_diff($buttons, $remove);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];
        if ($this->id) {
            $json['id'] = $this->id;
        }

        if ($this->class) {
            $json['class'] = $this->class;
        }

        if ($this->title) {
            $json['title'] = $this->title;
        }

        if ($this->text) {
            $json['text'] = $this->text;
        }

        if ($this->header) {
            $json['header'] = $this->header;
        }

        if ($this->body) {
            $json['body'] = $this->body;
        }

        $buttons = [];
        foreach ($this->buttons as $button) {
            $buttons[] = $button->jsonSerialize();
        }
        $json['buttons'] = $buttons;

        return $json;
    }
}
