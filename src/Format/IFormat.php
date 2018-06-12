<?php

namespace Tochka\JsonRpcSmdConverter\Format;


interface IFormat
{
    public function make(): array;
}