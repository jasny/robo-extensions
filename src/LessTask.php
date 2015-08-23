<?php

namespace Jasny\Robo;

/**
 * Robo task for compiling Less
 */
class LessTask extends \Robo\Task\Assets\Less
{
    /**
     * Combile less file
     * 
     * @param string $file
     * @return string
     */
    public function less($file)
    {
        $options = $this->compilerOptions;

        $uriRoot = dirname($file) . '/';
        
        if (isset($options['base'])) {
            if (strpos($file, $options['base']) !== 0) {
                return $this->printTaskError("$file isn't in {$options['base']}");
            }
            
            $uriRoot = '/' . substr($uriRoot, strlen($options['base']));
        }
        
        try {
            $parser = new \Less_Parser($options);
            $parser->parseFile($file, $uriRoot);
            return $parser->getCss();
        } catch (\Exception $e) {
            return $this->printTaskError($e->getMessage());
        }
    }
}

