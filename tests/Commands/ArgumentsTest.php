<?php

namespace alxgeras\php2\UnitTests\Commands;

use alxgeras\php2\Blog\Commands\Arguments;
use alxgeras\php2\Exceptions\ArgumentsException;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{
    public function testItCheckEmptyValueInArgv(): void
    {
        $arguments = new Arguments(['username' => '']);

        $count = count($arguments->getArguments());

        $this->assertEquals(0, $count);
    }

    private function argumentsProvider(): iterable
    {
        return [
            [123, '123'],
            [' some_value', 'some_value'],
            ['some_value ', 'some_value'],
            [' some_value ', 'some_value']
        ];
    }

    /**
     * @param $input
     * @param $expected
     * @return void
     * @throws ArgumentsException
     * @dataProvider argumentsProvider
     */
    public function testItReturnsValueAsString($input, $expected): void
    {
        $arguments = new Arguments(['some_key' => $input]);

        $value = $arguments->get('some_key');

        $this->assertSame($expected, $value);
    }

    /**
     * @throws ArgumentsException
     */
    public function testItParseFromArgv(): void
    {
        $masArgv = ['cli.php', 'username=ivan_4', 'first_name=Ivan', 'last_name=Nikitin'];

        $argument = Arguments::fromArgv($masArgv);

        $this->assertEquals('ivan_4', $argument->get('username'));
        $this->assertEquals('Ivan', $argument->get('first_name'));
        $this->assertEquals('Nikitin', $argument->get('last_name'));
    }

    /**
     * @throws ArgumentsException
     */
    public function testItReturnsArgumentsValueByName(): void
    {
        $arguments = new Arguments(['some_key' => 'some_value']);

        $value = $arguments->get('some_key');

        $this->assertEquals('some_value', $value);
    }

    public function testItThrowAnExceptionArgumentsNotFound(): void
    {
        $arguments = new Arguments(['some_key' => 'some_value']);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('Такого аргумента нет: not_some_key');

        $arguments->get('not_some_key');
    }
}