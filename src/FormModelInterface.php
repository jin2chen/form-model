<?php

declare(strict_types=1);

namespace jin2chen\FormModel;

use Yiisoft\Validator\DataSetInterface;

interface FormModelInterface extends DataSetInterface
{
    /**
     * Add error for the specified attribute.
     *
     * @param string $attribute attribute name.
     * @param string $error attribute error message.
     */
    public function addError(string $attribute, string $error): void;

    /**
     * Returns the errors for all attributes.
     *
     * @return array errors for all attributes or the specified attribute. null is returned if no error.
     *
     * Note that when returning errors for all attributes, the result is a two-dimensional array, like the following:
     *
     * ```php
     * [
     *     'username' => [
     *         'Username is required.',
     *         'Username must contain only word characters.',
     *     ],
     *     'email' => [
     *         'Email address is invalid.',
     *     ]
     * ]
     * ```
     *
     * {@see firstErrors()}
     * {@see firstError()}
     */
    public function errors(): array;

    /**
     * Returns the errors for single attribute.
     *
     * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
     *
     * @return array
     */
    public function error(string $attribute): array;

    /**
     * Returns a value indicating whether there is any validation error.
     *
     * @param string|null $attribute attribute name. Use null to check all attributes.
     *
     * @return bool whether there is any error.
     */
    public function hasErrors(?string $attribute = null): bool;

    /**
     * Returns the first error of every attribute in the model.
     *
     * @return array the first errors. The array keys are the attribute names, and the array values are the
     * corresponding error messages. An empty array will be returned if there is no error.
     *
     * {@see errors()}
     * {@see firstError()}
     */
    public function firstErrors(): array;

    /**
     * Returns the first error of the specified attribute.
     *
     * @param string $attribute attribute name.
     *
     * @return string the error message. Empty string is returned if there is no error.
     *
     * {@see errors()}
     * {@see firstErrors()}
     */
    public function firstError(string $attribute): string;

    /**
     * Populates the model with input data.
     *
     * which, with `load()` can be written as:
     *
     * ```php
     * $body = $request->getParsedBody();
     * $method = $request->getMethod();
     *
     * if ($method === Method::POST && $loginForm->load($body)) {
     *     // handle success
     * }
     * ```
     *
     * @param array $data the data array to load, typically server request attributes.
     */
    public function load(array $data): void;

    /**
     * Set specified attribute
     *
     * @param string $attribute of the attribute to set
     * @param mixed $value value
     */
    public function setAttributeValue(string $attribute, $value): void;

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by {@see \Yiisoft\Validator\Validator} to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * Each rule is an array with the following structure:
     *
     * ```php
     * public function rules(): array
     * {
     *     return [
     *         'login' => $this->loginRules()
     *     ];
     * }
     *
     * private function loginRules(): array
     * {
     *   return [
     *       \Yiisoft\Validator\Rule\Required::rule(),
     *       \Yiisoft\Validator\Rule\HasLength::rule()
     *       ->min(4)
     *       ->max(40)
     *       ->tooShortMessage('Is too short.')
     *       ->tooLongMessage('Is too long.'),
     *       \Yiisoft\Validator\Rule\Email::rule()
     *   ];
     * }
     * ```
     *
     * @return array Validation rules.
     */
    public function getRules(): array;
}
