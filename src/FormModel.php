<?php

declare(strict_types=1);

namespace jin2chen\FormModel;

use ReflectionClass;
use Yiisoft\Validator\Exception\MissingAttributeException;
use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\ResultSet;
use Yiisoft\Validator\RulesProviderInterface;

use function reset;

abstract class FormModel implements FormModelInterface, RulesProviderInterface, PostValidationHookInterface
{
    private array $attributes;
    private array $attributesErrors = [];
    private bool $validated = false;

    public function __construct()
    {
        $this->attributes = $this->collectAttributes();
    }

    /**
     * Returns the list of attribute types indexed by attribute names.
     *
     * By default, this method returns all public non-static properties of the class.
     *
     * @return array list of attribute types indexed by attribute names.
     */
    private function collectAttributes(): array
    {
        $class = new ReflectionClass($this);
        $attributes = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isStatic() || !$property->isPublic()) {
                continue;
            }

            $attributes[$property->getName()] = true;
        }

        return $attributes;
    }

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    public function error(string $attribute): array
    {
        return $this->attributesErrors[$attribute] ?? [];
    }

    public function errors(): array
    {
        return $this->attributesErrors;
    }

    public function firstError(string $attribute): string
    {
        if (empty($this->attributesErrors[$attribute])) {
            return '';
        }

        return reset($this->attributesErrors[$attribute]);
    }

    public function firstErrors(): array
    {
        if (empty($this->attributesErrors)) {
            return [];
        }

        $errors = [];

        foreach ($this->attributesErrors as $name => $es) {
            if (!empty($es)) {
                $errors[$name] = reset($es);
            }
        }

        return $errors;
    }

    private function clearErrors(?string $attribute = null): void
    {
        if ($attribute === null) {
            $this->attributesErrors = [];
        } else {
            unset($this->attributesErrors[$attribute]);
        }
    }

    /**
     * @param string[][] $items
     */
    private function addErrors(array $items): void
    {
        foreach ($items as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->attributesErrors[$attribute][] = $error;
            }
        }
    }

    public function hasErrors(?string $attribute = null): bool
    {
        return $attribute === null ? !empty($this->attributesErrors) : isset($this->attributesErrors[$attribute]);
    }

    public function addError(string $attribute, string $error): void
    {
        $this->attributesErrors[$attribute][] = $error;
    }

    public function load(array $data): void
    {
        foreach ($data as $name => $value) {
            $this->setAttributeValue($name, $value);
        }
    }

    public function getAttributeValue(string $attribute)
    {
        if (!isset($this->attributes[$attribute])) {
            throw new MissingAttributeException(sprintf('Property %s is undefined.', $attribute));
        }

        return $this->{$attribute};
    }

    public function setAttributeValue(string $attribute, $value): void
    {
        if (isset($this->attributes[$attribute])) {
            $this->{$attribute} = $value;
        }
    }

    public function processValidationResult(ResultSet $resultSet): void
    {
        $this->clearErrors();
        foreach ($resultSet as $attribute => $result) {
            if ($result->isValid() === false) {
                $this->addErrors([$attribute => $result->getErrors()]);
            }
        }
        $this->validated = true;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }
}
