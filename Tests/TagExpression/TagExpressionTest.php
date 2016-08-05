<?php

namespace Openpp\PushNotificationBundle\Tests\TagExpression;

use Openpp\PushNotificationBundle\TagExpression\TagExpression;

class TagExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @dataProvider validTagExpressionProvider
     */
    public function testParseValidTagExpression($data)
    {
        $expression = new TagExpression($data[key($data)]);
        $this->assertEquals($this->getExpectedForValidTagExpression(key($data)), $expression->toNativeSQLWhereClause());
    }

    public function validTagExpressionProvider()
    {
        return array(
            array(array(1 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890@@@@@@@@@@:::::::::::___________..........#########-------')),
            array(array(2 => 'tag_1 || tag_2 || tag_3 || tag_4 || tag_5 || tag_6 || tag_7 || tag_8 || tag_9 || tag_10 || tag_11 || tag_12 || tag_13 || tag_14 || tag_15 || tag_16 || tag_17 || tag_18 || tag_19 || tag_20')),
            array(array(3 => 'tag_1 && tag_2 && tag_3 && tag_4 && tag_5 && tag_6')),
            array(array(4 => '(tag_1 && tag_2) || (tag_3 && tag_4)')),
            array(array(5 => 'tag_1 && (tag_2 || tag_3) && tag_4')),
            array(array(6 => 'tag_1 && tag_2 || tag_3 && tag_4')),
            array(array(7 => '!tag_1')),
            array(array(8 => 'tag_1 || !tag_2 || tag_3 || tag_4')),
            array(array(9 => 'tag_1 && tag_2 && !tag_3')),
            array(array(10 => 'follows_RedSox || follows_Cardinals || (follow_Marines && location_Boston)')),
        );
    }

    private function getExpectedForValidTagExpression($key)
    {
        $expects = array(
            1 => "%s && ARRAY['ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890@@@@@@@@@@:::::::::::___________..........#########-------'::varchar]",
            2 => "%s && ARRAY['tag_1'::varchar,'tag_2'::varchar,'tag_3'::varchar,'tag_4'::varchar,'tag_5'::varchar,'tag_6'::varchar,'tag_7'::varchar,'tag_8'::varchar,'tag_9'::varchar,'tag_10'::varchar,'tag_11'::varchar,'tag_12'::varchar,'tag_13'::varchar,'tag_14'::varchar,'tag_15'::varchar,'tag_16'::varchar,'tag_17'::varchar,'tag_18'::varchar,'tag_19'::varchar,'tag_20'::varchar]",
            3 => "%s @> ARRAY['tag_1'::varchar,'tag_2'::varchar,'tag_3'::varchar,'tag_4'::varchar,'tag_5'::varchar,'tag_6'::varchar]",
            4 => "(%s @> ARRAY['tag_1'::varchar,'tag_2'::varchar]) OR (%s @> ARRAY['tag_3'::varchar,'tag_4'::varchar])",
            5 => "%s @> ARRAY['tag_1'::varchar] AND %s && ARRAY['tag_2'::varchar,'tag_3'::varchar] AND %s && ARRAY['tag_4'::varchar]",
            6 => "%s @> ARRAY['tag_1'::varchar,'tag_2'::varchar] OR %s @> ARRAY['tag_3'::varchar,'tag_4'::varchar]",
            7 => "(%s && ARRAY['tag_1'::varchar]) = false",
            8 => "%s && ARRAY['tag_1'::varchar] OR (%s && ARRAY['tag_2'::varchar]) = false OR %s && ARRAY['tag_3'::varchar,'tag_4'::varchar]",
            9 => "%s @> ARRAY['tag_1'::varchar,'tag_2'::varchar] AND (%s && ARRAY['tag_3'::varchar]) = false",
            10 => "%s && ARRAY['follows_RedSox'::varchar,'follows_Cardinals'::varchar] OR %s @> ARRAY['follow_Marines'::varchar,'location_Boston'::varchar]",
        );

        return $expects[$key];
    }

    /**
     *
     * @dataProvider invalidTagExpressionProvider
     * @expectedException Openpp\PushNotificationBundle\Exception\InvalidTagExpressionException
     */
    public function testParseInvalidTagExpression($tagExpression)
    {
        $expression = new TagExpression($tagExpression);
        $expression->validate();
    }

    public function invalidTagExpressionProvider()
    {
        return array(
            // invalid length
            array('0ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890@@@@@@@@@@:::::::::::___________..........#########-------'),
            // invalid character '?'
            array('android?4'),
            // invalid character '~'
            array('tag~2'),
            // invalid character '%'
            array('tag%6'),
            // invalid character '&'
            array('tag&5'),
            // invalid character not ascii
            array('タグ'),
            // invalid syntax
            array('tag!!!'),
            // too many tags with only '||'
            array('tag_1 || tag_2 || tag_3 || tag_4 || tag_5 || tag_6 || tag_7 || tag_8 || tag_9 || tag_10 || tag_11 || tag_12 || tag_13 || tag_14 || tag_15 || tag_16 || tag_17 || tag_18 || tag_19 || tag_20 || tag_21'),
            // too many tags with not only '||'
            array('tag_1 || tag_2 && tag_3 || tag_4 || tag_5 || tag_6 || tag_7'),
            // too many tags with not only '||'
            array('tag_1 || tag_2 || tag_3 || tag_4 || tag_5 || tag_6 || !tag_7'),
            // tags with no operators
            array('tag_1 tag_2'),
            // tags with no operators
            array('tag_1 !tag_2'),
            // tags with no operator
            array('tag_1 (tag_2 && tag_3)'),
            // invalid
            array('(tag_1 || (tag_2 && tag_3)'),
            // invalid
            array('tag_1 || (tag_2 && tag_3))'),
            // invalid
            array('tag_1 || && tag_2 && tag_3'),
            // invalid
            array('||'),
        );
    }
}