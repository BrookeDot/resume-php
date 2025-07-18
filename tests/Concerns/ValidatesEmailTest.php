<?php

declare(strict_types=1);

namespace Tests\Concerns;

use InvalidArgumentException;
use JustSteveKing\Resume\Concerns\ValidatesEmail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\PackageTestCase;

final class ValidatesEmailTest extends PackageTestCase
{
    private TestValidatesEmailClass $validator;

    protected function setUp(): void
    {
        $this->validator = new TestValidatesEmailClass();
    }

    public static function validEmailProvider(): array
    {
        return [
            'simple email' => ['test@example.com'],
            'email with subdomain' => ['user@mail.example.com'],
            'email with plus' => ['user+tag@example.com'],
            'email with dots' => ['first.last@example.com'],
            'email with numbers' => ['user123@example.com'],
            'email with hyphens' => ['user-name@example-domain.com'],
            'international domain' => ['user@example.co.uk'],
            'long email' => ['verylongusernamethatisvalid@verylongdomainname.com'],
        ];
    }

    public static function invalidEmailProvider(): array
    {
        return [
            'empty string' => ['', 'Email cannot be empty'],
            'whitespace only' => ['   ', 'Email cannot be empty'],
            'no at symbol' => ['invalidemail', 'Invalid email format: invalidemail'],
            'multiple at symbols' => ['invalid@@email.com', 'Invalid email format: invalid@@email.com'],
            'no domain' => ['invalid@', 'Invalid email domain: '],
            'no local part' => ['@example.com', 'Invalid email format: @example.com'],
            'invalid characters' => ['invalid email@example.com', 'Invalid email format: invalid email@example.com'],
            'starts with dot' => ['.invalid@example.com', 'Invalid email format: .invalid@example.com'],
            'ends with dot' => ['invalid.@example.com', 'Invalid email format: invalid.@example.com'],
            'consecutive dots' => ['invalid..email@example.com', 'Invalid email format: invalid..email@example.com'],
            'no domain extension' => ['invalid@example', 'Invalid email domain: example'],
            'domain too long' => ['user@' . str_repeat('a', 250) . '.com', 'Email address is too long (max 254 characters)'],
            'email too long' => [str_repeat('a', 250) . '@example.com', 'Email address is too long (max 254 characters)'],
            'invalid domain characters' => ['user@domain!.com', 'Invalid email domain: domain!.com'],
            'domain starts with dot' => ['user@.example.com', 'Invalid email format: user@.example.com'],
            'domain ends with dot' => ['user@example.com.', 'Invalid email format: user@example.com.'],
        ];
    }

    public function test_accepts_null_email(): void
    {
        $this->validator->testAssertEmail(null);

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    #[DataProvider('validEmailProvider')]
    public function test_accepts_valid_emails(string $email): void
    {
        $this->validator->testAssertEmail($email);

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    #[DataProvider('invalidEmailProvider')]
    public function test_rejects_invalid_emails(string $email, null|string $expectedMessage = null): void
    {
        $this->expectException(InvalidArgumentException::class);

        if ($expectedMessage) {
            $this->expectExceptionMessage($expectedMessage);
        }

        $this->validator->testAssertEmail($email);
    }

    public function test_validates_domain_length(): void
    {
        $longDomain = str_repeat('a', 254) . '.com';
        $email = "user@{$longDomain}";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Email address is too long (max 254 characters)");

        $this->validator->testAssertEmail($email);
    }

    public function test_validates_email_total_length(): void
    {
        $longLocalPart = str_repeat('a', 250);
        $email = "{$longLocalPart}@example.com";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email address is too long (max 254 characters)');

        $this->validator->testAssertEmail($email);
    }

    public function test_rejects_domain_without_dot(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email domain: localhost');

        $this->validator->testAssertEmail('user@localhost');
    }

    public function test_accepts_valid_domain_with_hyphens(): void
    {
        $this->validator->testAssertEmail('user@sub-domain.example.com');

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    public function test_accepts_valid_domain_with_numbers(): void
    {
        $this->validator->testAssertEmail('user@123domain.com');

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }
}

/**
 * Test class to expose the protected assertEmail method for testing
 */
final class TestValidatesEmailClass
{
    use ValidatesEmail;

    public function testAssertEmail(?string $email): void
    {
        $this->assertEmail($email);
    }
}
