<?php

class PlanetError
{
    public int $level;

    /** @var array<int, string> */
    public $levels = array(
        1 => 'notice',
        2 => 'warning',
        3 => 'error',
    );
    public string $message;

    /**
     * PlanetError constructor.
     */
    public function __construct(int $level, string $message)
    {
        $this->level = (int) $level;
        $this->message = $message;
    }

    public function toString(string $format = '%1$s: %2$s') : string
    {
        return sprintf($format, $this->levels[$this->level], $this->message);
    }
}
