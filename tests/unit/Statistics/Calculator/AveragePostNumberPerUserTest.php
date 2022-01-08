<?php

declare(strict_types = 1);

namespace Tests\unit\Statistics\Calculator;

use DateTime;
use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Builder\ParamsBuilder;
use Statistics\Calculator\AveragePostNumberPerUser;

/**
 * Class AveragePostNumberPerUserTest
 *
 * @package Tests\unit\Statistics\Calculator
 */
class AveragePostNumberPerUserTest extends TestCase
{
    /**
     * Test Statistics\Calculator\AveragePostNumberPerUser::doCalculate()
     */
    public function testDoCalculate()
    {
        // Set report date.
        $parameters = ParamsBuilder::reportStatsParams(new DateTime('2018-08-01'));

        // Create instance of 'AveragePostNumberPerUser' calculator.
        $calculator = (new AveragePostNumberPerUser())->setParameters($parameters[3]);

        // Read JSON data file.
        $responseJson = file_get_contents('./tests/data/social-posts-response.json');
        // Convert JSON data into array.
        $responseData = json_decode($responseJson, true);

        // Extract posts from array.
        $posts = $responseData['data']['posts'];

        // Iterate posts.
        foreach ($posts as $postData) {
            $post = (new SocialPostTo())
                ->setId($postData['id'])
                ->setAuthorName($postData['from_name'])
                ->setAuthorId($postData['from_id'])
                ->setText($postData['message'])
                ->setType($postData['type'])
                ->setDate(new DateTime($postData['created_time']));

            $calculator->accumulateData($post);
        }

        // We should have one post per user.
        $this->assertEquals(1, $calculator->calculate()->getValue());

        // Confirm we have four user posts.
        $this->assertEquals(4, count($posts));
    }
}
