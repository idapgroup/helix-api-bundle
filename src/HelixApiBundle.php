<?php

namespace IdapGroup\HelixApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HelixApiBundle extends Bundle
{
    /**
     * @return string
     */
    public function  getPath(): string
    {
        return \dirname(__DIR__);
    }
}