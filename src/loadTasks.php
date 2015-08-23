<?php

namespace Jasny\Robo;

/**
 * Load all custom(ized) tasks
 */
trait loadTasks
{
    /**
     * @param $input
     * @return LessTask
     */
    protected function taskLess($input)
    {
        return new LessTask($input);
    }
    
    /**
     * @param $filename
     * @return BumpVersionTask
     */
    protected function taskBumpVersion($filename)
    {
        return new BumpVersionTask($filename);
    }
}

