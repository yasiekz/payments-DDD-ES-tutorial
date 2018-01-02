<?php

namespace App\Domain;

abstract class AggregateRoot
{
    use EventSourceTrait;
}