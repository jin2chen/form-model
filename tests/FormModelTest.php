<?php

namespace jin2chen\FormModel\Tests;

use jin2chen\FormModel\FormModel;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Exception\MissingAttributeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Validator;

final class FormModelTest extends TestCase
{
    private function data(): array
    {
        return [
            'name' => 'Mac Pro',
            'price' => "10000",
        ];
    }

    public function testHasAttribute()
    {
        $form = new ProductForm();
        $this->assertTrue($form->hasAttribute('name'));
        $this->assertTrue($form->hasAttribute('price'));
        $this->assertTrue($form->hasAttribute('description'));
        $this->assertFalse($form->hasAttribute('type'));
    }

    public function testLoad()
    {
        $data = $this->data();

        $form = new ProductForm();
        $form->load($data);

        $this->assertEquals($data['name'], $form->name);
        $this->assertEquals($data['price'], $form->price);
        $this->assertEquals('', $form->description);
    }

    public function testSetAttributeValue()
    {
        $data = $this->data();

        $form = new ProductForm();
        $form->setAttributeValue('name', $data['name']);
        $this->assertEquals($data['name'], $form->name);
    }


    public function testGetAttributeValue()
    {
        $form = new ProductForm();
        $this->assertEquals('', $form->getAttributeValue('name'));

        $this->expectException(MissingAttributeException::class);
        $form->getAttributeValue('undefined');
    }

    public function testValidate()
    {
        $form = new ProductForm();
        $validator = new Validator();
        $results = $validator->validate($form);

        $this->assertFalse($results->isValid());
        $this->assertTrue($form->isValidated());

        $errors = $form->errors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertCount(1, $form->error('name'));

        $this->assertEquals('Value cannot be blank.', $form->firstError('name'));
        $firstErrors = $form->firstErrors();
        $this->assertCount(2, $firstErrors);
        $this->assertEquals('', $form->firstError('price'));

        $this->assertTrue($form->hasErrors());
        $this->assertTrue($form->hasErrors('name'));
        $this->assertFalse($form->hasErrors('price'));

        $form->addError('description', 'Add errors');
        $this->assertCount(1, $form->error('description'));
    }
}

/**
 * @internal
 */
class ProductForm extends FormModel
{
    public string $name = '';
    public string $price = '0.00000';
    public string $description = '';
    public string $categoryId = '0';
    protected string $type;

    public function getRules(): array
    {
        return [
            'name' => $this->nameValidator(),
            'price' => $this->priceValidator(),
            'categoryId' => $this->categoryValidator(),
        ];
    }

    /**
     * @return Rule[]
     */
    private function nameValidator(): array
    {
        return [
            Rule\Required::rule(),
            Rule\HasLength::rule()->max(120),
        ];
    }

    private function priceValidator(): array
    {
        return [
            Rule\Required::rule(),
            Rule\Number::rule(),
        ];
    }

    private function categoryValidator(): array
    {
        return [
            static function ($value): Result {
                $result = new Result();
                if (!in_array($value, [1, 2])) {
                    $result->addError('This value is invalid.');
                }

                return $result;
            },
        ];
    }
}
