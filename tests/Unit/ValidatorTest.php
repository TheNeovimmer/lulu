<?php
namespace Tests\Unit;

use App\Core\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {
    public function testRequiredPasses(): void {
        $v = new Validator(['name' => 'Alice']);
        $v->required('name');
        $this->assertTrue($v->passes());
    }

    public function testRequiredFailsOnEmpty(): void {
        $v = new Validator(['name' => '']);
        $v->required('name');
        $this->assertFalse($v->passes());
        $this->assertStringContainsString('requis', $v->firstError());
    }

    public function testRequiredFailsOnMissing(): void {
        $v = new Validator([]);
        $v->required('name');
        $this->assertFalse($v->passes());
    }

    public function testEmailPasses(): void {
        $v = new Validator(['email' => 'test@example.com']);
        $v->email('email');
        $this->assertTrue($v->passes());
    }

    public function testEmailFailsOnInvalid(): void {
        $v = new Validator(['email' => 'not-an-email']);
        $v->email('email');
        $this->assertFalse($v->passes());
        $this->assertStringContainsString('email valide', $v->firstError());
    }

    public function testEmailPassesOnEmpty(): void {
        $v = new Validator(['email' => '']);
        $v->email('email');
        $this->assertTrue($v->passes());
    }

    public function testMinLengthPasses(): void {
        $v = new Validator(['pass' => '123456']);
        $v->minLength('pass', 6);
        $this->assertTrue($v->passes());
    }

    public function testMinLengthFails(): void {
        $v = new Validator(['pass' => '123']);
        $v->minLength('pass', 6);
        $this->assertFalse($v->passes());
    }

    public function testMaxLengthPasses(): void {
        $v = new Validator(['bio' => 'short']);
        $v->maxLength('bio', 100);
        $this->assertTrue($v->passes());
    }

    public function testMaxLengthFails(): void {
        $v = new Validator(['bio' => str_repeat('a', 101)]);
        $v->maxLength('bio', 100);
        $this->assertFalse($v->passes());
    }

    public function testNumericPasses(): void {
        $v = new Validator(['age' => '25']);
        $v->numeric('age');
        $this->assertTrue($v->passes());
    }

    public function testNumericFails(): void {
        $v = new Validator(['age' => 'abc']);
        $v->numeric('age');
        $this->assertFalse($v->passes());
    }

    public function testNumericPassesOnEmpty(): void {
        $v = new Validator(['age' => '']);
        $v->numeric('age');
        $this->assertTrue($v->passes());
    }

    public function testInArrayPasses(): void {
        $v = new Validator(['role' => 'admin']);
        $v->inArray('role', ['admin', 'user']);
        $this->assertTrue($v->passes());
    }

    public function testInArrayFails(): void {
        $v = new Validator(['role' => 'superadmin']);
        $v->inArray('role', ['admin', 'user']);
        $this->assertFalse($v->passes());
    }

    public function testInArrayPassesOnEmpty(): void {
        $v = new Validator(['role' => '']);
        $v->inArray('role', ['admin', 'user']);
        $this->assertTrue($v->passes());
    }

    public function testMatchesPasses(): void {
        $v = new Validator(['password' => 'secret', 'password_confirm' => 'secret']);
        $v->matches('password', 'password_confirm');
        $this->assertTrue($v->passes());
    }

    public function testMatchesFails(): void {
        $v = new Validator(['password' => 'secret', 'password_confirm' => 'different']);
        $v->matches('password', 'password_confirm');
        $this->assertFalse($v->passes());
    }

    public function testChainedValidationAllPass(): void {
        $v = new Validator([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'age' => '30',
        ]);
        $result = $v
            ->required('name', 'Nom')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->required('age', 'Âge')
            ->numeric('age', 'Âge');
        $this->assertTrue($result->passes());
        $this->assertEmpty($result->errors());
    }

    public function testChainedValidationSomeFail(): void {
        $v = new Validator([
            'name' => '',
            'email' => 'bademail',
        ]);
        $result = $v
            ->required('name', 'Nom')
            ->required('email', 'Email')
            ->email('email', 'Email');
        $this->assertFalse($result->passes());
        $this->assertCount(2, $result->errors());
    }

    public function testErrorsReturnsAllErrors(): void {
        $v = new Validator(['a' => '', 'b' => '']);
        $v->required('a')->required('b');
        $errors = $v->errors();
        $this->assertCount(2, $errors);
        $this->assertArrayHasKey('a', $errors);
        $this->assertArrayHasKey('b', $errors);
    }

    public function testFirstErrorReturnsFirst(): void {
        $v = new Validator(['a' => '', 'b' => '']);
        $v->required('a')->required('b');
        $this->assertSame($v->errors()['a'], $v->firstError());
    }

    public function testFirstErrorReturnsEmptyOnPass(): void {
        $v = new Validator(['name' => 'Alice']);
        $v->required('name');
        $this->assertSame('', $v->firstError());
    }

    public function testCustomLabelsInErrors(): void {
        $v = new Validator(['nom' => '']);
        $v->required('nom', 'Nom complet');
        $this->assertStringContainsString('Nom complet', $v->firstError());
    }
}
