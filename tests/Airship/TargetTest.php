<?php

use Airship\Target;
use Airship\TestCase;

class TargetTest extends TestCase
{
    public function testToArray()
    {
        $group = new Target(2, 'Group', 'GroupName', ['att2' => 'val2']);

        $target = new Target(1, 'Type', 'TargetName', ['att' => 'val'], $group);

        $this->assertEquals(
            [
                Target::KEY_TYPE => 'Type',
                Target::KEY_ID => 1,
                Target::KEY_DISPLAY_NAME => 'TargetName',
                Target::KEY_ATTRIBUTES => [
                    'att' => 'val',
                ],
                Target::KEY_GROUP => [
                    Target::KEY_TYPE => 'Group',
                    Target::KEY_ID => 2,
                    Target::KEY_DISPLAY_NAME => 'GroupName',
                    Target::KEY_ATTRIBUTES => [
                        'att2' => 'val2',
                    ],
                    Target::KEY_IS_GROUP => true
                ]
            ],
            $target->toArray()
        );
    }
}
