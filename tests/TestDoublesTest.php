<?php

class TestDoublesTest extends \PHPUnit\Framework\TestCase
{

    public function testMock()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $mock->expects($this->once())
            ->method('doSomething')
            ->with('bar')
            ->willReturn('foo');

        $exampleCommand = new \App\ExampleCommand($mock);

        $this->assertSame('foo', $exampleCommand->execute('bar'));
    }

    public function testReturnTypes()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $this->assertNull($mock->doSomething('bar'));
    }

    public function testConsecutiveReturns()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $mock->method('doSomething')
            ->will($this->onConsecutiveCalls(1, 2));

        foreach ([1, 2] as $value) {

            $this->assertSame($value, $mock->doSomething('bar'));
        }
    }

    public function testExceptionsThrown()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $mock->method('doSomething')
            ->willThrowException(new RuntimeException());

        $this->expectException(RuntimeException::class);

        $mock->doSomething('bar');
    }

    public function testCallbackReturns()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $mock->method('doSomething')
            ->willReturnCallback(function($arg) {

                if ($arg % 2 == 0) {
                    return $arg;
                }

                throw new InvalidArgumentException();
            });

        $this->assertSame(10, $mock->doSomething(10));

        $this->expectException(InvalidArgumentException::class);
        $mock->doSomething(9);
    }

    public function testWithEqualTo()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $mock->expects($this->once())
            ->method('doSomething')
            ->with($this->equalTo('bar'));

        $mock->doSomething('bar');
    }

    public function testMultipleArgs()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $mock->expects($this->once())
            ->method('doSomething')
            ->with(
                $this->stringContains('foo'),
                $this->greaterThanOrEqual(100),
                $this->anything()
            );

        $mock->doSomething('foobar', 101, null);
    }

    public function testConsecutiveArguments()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $mock->expects($this->exactly(2))
            ->method('doSomething')
            ->withConsecutive(
                [$this->stringContains('foo'), $this->greaterThanOrEqual(100)],
                [$this->isNull(), $this->greaterThanOrEqual(10)]
            );

        $mock->doSomething('foobar', 100);
        $mock->doSomething(null, 15);
    }

    public function testCallbackArguments()
    {
        $mock = $this->createMock(\App\ExampleService::class);

        $mock->expects($this->once())
            ->method('doSomething')
            ->with($this->callback(function($object) {
                $this->assertInstanceOf(\App\ExampleDependency::class, $object);
                return $object->exampleMethod() === 'Example string';
            }));

        $mock->doSomething(new \App\ExampleDependency());
    }

    public function testIdenticalTo()
    {
        $dependency = new \App\ExampleDependency();

        $mock = $this->createMock(\App\ExampleService::class);

        $mock->expects($this->once())
            ->method('doSomething')
            ->with($this->identicalTo($dependency));

        $mock->doSomething($dependency);
    }

    public function testMockBuilder()
    {
        $mock = $this->getMockBuilder(\App\ExampleService::class)
            ->setConstructorArgs([100, 200])
            ->getMock();

        $mock->method('doSomething')->willReturn('foo');

        $this->assertSame('foo', $mock->doSomething('bar'));
    }

    public function testOnlyMethods()
    {
        $mock = $this->getMockBuilder(\App\ExampleService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['doSomething'])
            ->getMock();

        $mock->method('doSomething')->willReturn('foo');

        $this->assertSame('foo', $mock->nonMockedMethod('bar'));
    }

    public function testAddMethods()
    {
        $mock = $this->getMockBuilder(\App\ExampleService::class)
            ->disableOriginalConstructor()
            ->addMethods(['nonExistentMethod'])
            ->getMock();

        $mock->expects($this->once())
            ->method('nonExistentMethod')
            ->with($this->isInstanceOf(\App\ExampleDependency::class))
            ->willReturn('foo');

        $this->assertSame('foo', $mock->nonExistentMethod(new \App\ExampleDependency()));

    }







}