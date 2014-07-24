<?php
/*
 * This file is part of StaticReview
 *
 * Copyright (c) 2014 Samuel Parkinson <@samparkinson_>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/sjparkinson/static-review/blob/master/LICENSE.md
 */

namespace StaticReview\VersionControl;

use StaticReview\VersionControl\VersionControlInterface;
use StaticReview\File\File;
use StaticReview\Collection\FileCollection;

use Symfony\Component\Process\Process;

class GitVersionControl implements VersionControlInterface
{
    /**
     * Gets a list of the files currently staged under git.
     *
     * Returns either an empty array or a tab seperated list of staged files and
     * their git status.
     *
     * @link http://git-scm.com/docs/git-status
     *
     * @return FileCollection
     */
    public function getStagedFiles()
    {
        $files = new FileCollection();

        $process = new Process('git rev-parse --show-toplevel');
        $process->run();

        $base = trim($process->getOutput());

        $process = new Process('git diff --cached --name-status --diff-filter=ACMR');
        $process->run();

        $output = array_filter(explode(PHP_EOL, $process->getOutput()));

        foreach($output as $file) {
            list($status, $path) = explode("\t", $file);
            $files->append(new File($status, $path, $base));
        }

        return $files;
    }
}