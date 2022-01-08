<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

/**
 * Calculate the number of posts per user.
 *
 * @package Statistics\Calculator
 */
class AveragePostNumberPerUser extends AbstractCalculator
{

    protected const UNITS = 'posts';

    /**
     * @var int
     */
    private $postCount = 0;

    /**
     * @var array
     */
    private $userArray = [];

    /**
     * @param SocialPostTo $postTo
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        // Increment post count.
        $this->postCount++;

        // Get post author ID as unique user identifier.
        $userId = $postTo->getAuthorId();

        // Determine if we have `userId` in `userArray`.
        if (!in_array($userId, $this->userArray)) {
            $this->userArray[] = $userId;
        }
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        // Store user count for reuse.
        $userCount = count($this->userArray);

        // Calculate average posts per user.
        $value = ($userCount > 0)
            ? $this->postCount / $userCount
            : 0;

        // Return rounded value to two decimal places.
        return (new StatisticsTo())->setValue(round($value, 2));
    }
}
