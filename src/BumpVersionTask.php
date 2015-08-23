<?php

namespace Jasny\Robo;

use \Robo\Result;
use \Robo\Task\BaseTask;

/**
 * Bump a version in a JSON file
 *
 * ```php
 * $this->taskBumpVersion('composer.json')
 *  ->inc('patch')
 *  ->run();
 *
 * $this->taskBumpVersion('composer.json')
 *  ->to('1.2.6')
 *  ->run();
 * ```
 *
 * @method inc(string) increment 'major', 'minor' or 'patch'
 * @method to(string) version to set
 */
class BumpVersionTask extends \Robo\Task\BaseTask
{
    use \Robo\Common\DynamicParams;

    protected $filename;
    protected $inc;
    protected $to;
    protected $useGit;

    protected $tags;

    /**
     * Class constructor
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Run task
     */
    function run()
    {
        if (!file_exists($this->filename)) {
            return Result::error($this, "File {$this->filename} does not exist");
        }
        
        $text = file_get_contents($this->filename);
        $info = json_decode($text);

        if (!file_exists($info->version)) {
            return Result::error($this, "{$this->filename} does not contain a version property");
        }

        if ($this->to) {
            if (!preg_match('/^v?(\d+\.\d+\.\d+(?:-.+)?)$/', $this->to, $matches)) {
                return Result::error($this, "Invalid version $version");
            }
            $version = $matches[1];
        } else {
            $version = $this->bumpSemVer($info->version);
            
            if ($this->useGit && $this->versionExists($version)) {
                if ($inc !== 'patch') return Result::error($this, "Version $version already exists as Git tag");
                
                do {
                    $version = $this->bumpSemVer($version);
                } while($this->versionExists($version));
            }
        }
        
        $newText = preg_replace('/"version"\s*:\s*"' . preg_quote($version, '/') . '"/', $text);
        
        $res = file_put_contents($this->filename, $newText);
        if ($res === false) {
            return Result::error($this, "Error writing to file {$this->filename}.");
        }
        
        $this->printTaskSuccess("<info>{$this->filename}</info> updated. Bumped to $version");
        return Result::success($this, '', ['replaced' => $count]);
    }
    
    /**
     * Set to
     *
     * @param string $version
     */
    public function to($version)
    {
        if (in_array($version, ['major', 'minor', 'patch'])) {
            $this->inc = $version;
            return;
        }
        
        $this->to = $version;
    }
    
    /**
     * Increment a part of a semver version
     *
     * @param string $version
     * @return string
     */
    protected function bumpSemVer($version)
    {
        if (!preg_match('/^v?(\d+\.\d+\.\d+)(?:-.+)?$/', $version, $matches)) return null;
        
        $inc = $this->inc ?: 'patch';
        $parts = explode('.', $matches[1]);
        
        switch ($inc) {
            case 'major':
                $parts[0]++;
                $parts[1] = 0; 
                $parts[2] = 0;
                break;
            case 'minor':
                $parts[1]++;
                $parts[2] = 0;
                break;
            case 'patch':
                $parts[2]++;
                break;
        }
        
        return join('.', $parts);
    }
    
    /**
     * Check if version exists as git tag
     *
     * @param string $version
     * @return boolean
     */
    protected function versionExists($version)
    {
        if (!isset($this->tags)) {
            exec('git tag', $tags, $ret);
            if (!$ret) return false;
            
            $this->tags = $tags;
        }
        
        foreach ($this->tags as $tag) {
            if (ltrim($tag, 'v') === $version) return true;
        }
        
        return false;
    }
}

