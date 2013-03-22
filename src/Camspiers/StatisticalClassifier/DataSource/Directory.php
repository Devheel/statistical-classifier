<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\DataSource;

/**
 * @author Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class Directory extends DataArray
{
    private $directory;
    private $include;

    public function __construct($directory, array $include = null)
    {
        if (!file_exists($directory)) {
            mkdir($directory);
        }
        $this->directory = realpath($directory);
        $this->include = $include;
        parent::__construct($this->read());
    }

    public function read()
    {
        $data = array();
        if (file_exists($this->directory)) {
            if (is_array($this->include) && count($this->include) !== 0) {
                $files = array();
                foreach ($this->include as $include) {
                    $files = array_merge($files, glob("$this->directory/$include/*", GLOB_NOSORT));
                }
            } else {
                $files = glob($this->directory . '/*/*', GLOB_NOSORT);
            }
            foreach ($files as $filename) {
                if (is_file($filename)) {
                    $dirname = basename(dirname($filename));
                    if (!isset($data[$dirname])) {
                        $data[$dirname] = array();
                    }
                    $data[$dirname][] = file_get_contents($filename);
                }
            }
        }

        return $data;
    }

    public function write()
    {
        foreach ($this->data as $category => $documents) {
            if (!file_exists($this->directory . '/' . $category)) {
                mkdir($this->directory . '/' . $category);
            }
            foreach ($documents as $document) {
                file_put_contents(
                    $this->directory . '/' . $category . '/' . md5($document),
                    $document
                );
            }
        }
    }
}