<?php

namespace App;

class ExampleCommand
{
    /**
     * @var ExampleService
     */
    private $service;

    public function __construct(ExampleService $service)
    {
        $this->service = $service;
    }

    public function execute($arg)
    {
        return $this->service->doSomething($arg);
    }
}