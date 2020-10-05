<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit;

use Jeremeamia\Slack\BlockKit\Inputs\HasConfirm;
use Jeremeamia\Slack\BlockKit\Partials\HasOptionGroups;
use Jeremeamia\Slack\BlockKit\Partials\HasOptions;
use Jeremeamia\Slack\BlockKit\Partials\Text;
use JsonSerializable;

abstract class Element implements JsonSerializable
{
    /** @var Element|null */
    protected $parent;

    /** @var array */
    protected $extra;

    /**
     * @return static
     */
    public static function new()
    {
        return new static();
    }

    /**
     * @return Element|null
     */
    public function getParent(): ?Element
    {
        return $this->parent;
    }

    /**
     * @param Element $parent
     * @return static
     */
    public function setParent(Element $parent): Element
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return Type::mapClass(static::class);
    }

    /**
     * Allows setting arbitrary extra fields on an element.
     *
     * Ideally, this is only used to allow setting new Slack features that are not yet implemented in this library.
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function setExtra(string $key, $value): Element
    {
        if (!is_scalar($value) && !($value instanceof Element)) {
            throw new Exception('Invalid extra field set in %d.', [static::class]);
        }

        $this->extra[$key] = $value;

        return $this;
    }

    /**
     * @throws Exception if the block kit item is invalid (e.g., missing data).
     */
    abstract public function validate(): void;

    /**
     * @return array
     */
    public function toArray(): array
    {
        $this->validate();
        $type = $this->getType();

        $data = !in_array($type, Type::HIDDEN_TYPES, true) ? compact('type') : [];

        foreach ($this->extra ?? [] as $key => $value) {
            $data[$key] = $value instanceof Element ? $value->toArray() : $value;
        }

        return $data;
    }

    public function parse(array $content): Element
    {

        if (in_array(HasOptionGroups::class, class_uses($this))) {
            $this->parseOptionGroups($content);
            $this->parseInitialOptions($content);
        } elseif (in_array(HasOptions::class, class_uses($this))) {
            $this->parseOptions($content);
            $this->parseInitialOptions($content);
        }

        if (in_array(HasConfirm::class, class_uses($this))) {
            $this->parseConfirm($content);
        }

        foreach ($content as $key => $value) {

            if ($this->checkTextElement($key, $value)) {
                continue;
            }

            $method = $this->snakeToCamel($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
                continue;
            }

            $this->setExtra($key, $value);

        }

        return $this;

    }

    /**
     * @param string $key
     * @param string|array $value
     * @return bool
     */
    private function checkTextElement(string $key, $value): bool
    {

        $method = 'set' . ucfirst($key);

        if (in_array($value['type'] ?? '', Text::TYPES) && method_exists($this, $method)) {

            $this->$method(Text::create($value)->parse($value));
            return true;

        }

        return false;

    }

    protected function snakeToCamel ($str, $ucfirst = false) {

        $camel = str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
        if ($ucfirst) {
            return $camel;
        }

        return lcfirst($camel);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
