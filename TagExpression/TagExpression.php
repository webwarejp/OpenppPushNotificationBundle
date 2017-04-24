<?php

namespace Openpp\PushNotificationBundle\TagExpression;

use Openpp\PushNotificationBundle\Exception\InvalidTagExpressionException;

class TagExpression
{
    const MAX_TAG_LENGTH = 120;
    const MAX_TAGS_WITH_ONLY_OR_OPERATORS = 20;
    const MAX_TAGS_WITH_VALIOUS_OPERATORS = 6;

    protected $tagExpression;

    /**
     * Constructor
     *
     * @param string $tagExpression
     */
    public function __construct($tagExpression)
    {
        $this->tagExpression = $tagExpression;
    }

    /**
     * Validate tag expressions.
     *
     * Tag expressions are limited to 20 tags if they contain only ORs; otherwise they are limited to 6 tags.
     *
     * @param boolean $exceptionOnInvalid
     *
     * @throws InvalidTagExpressionException
     * @return boolean
     */
    public function validate($exceptionOnInvalid = true)
    {
        try {
            $this->parse();
        } catch (Exception $e) {
            if ($exceptionOnInvalid) {
                throw new InvalidTagExpressionException($e->getMessage());
            }
            return false;
        }

        if (!$exceptionOnInvalid) {
            return true;
        }
    }

    /**
     * Parse a tag expression.
     *
     * @return string
     */
    protected function parse()
    {
        $lexer = new Lexer($this->tagExpression);
        $parser = new Parser();
        while ($lexer->yylex()) {
            $parser->doParse($lexer->token, $lexer->value);
        }
        $parser->doParse(0, 0);

        return $parser->getResult();
    }

    /**
     * Convert a tag expression to the native SQL's WHERE clouse.
     *
     * @return string
     */
    public function toNativeSQLWhereClause()
    {
        try {
            return $this->parse();
        } catch (Exception $e) {
            throw new InvalidTagExpressionException($e->getMessage());
        }
    }

    /**
     * Validate a single tag.
     *
     * A tag can be any string, up to 120 characters, containing alphanumeric and
     * the following non-alphanumeric characters: ‘_’, ‘@’, ‘#’, ‘.’, ‘:’, ‘-’.
     *
     * @param string $tag
     * @throws InvalidTagExpressionException
     */
    public static function validateSingleTag($tag)
    {
        if (self::MAX_TAG_LENGTH < strlen($tag)) {
            throw new InvalidTagExpressionException(sprintf('A tag can be up to %d characters: %s', self::MAX_TAG_LENGTH, $tag));
        }

        if (!preg_match('/^[a-zA-Z0-9_@#\.:\-]+$/', $tag)) {
            throw new InvalidTagExpressionException("A tag can be containing alphanumeric and the following non-alphanumeric characters: ‘_’, ‘@’, ‘#’, ‘.’, ‘:’, ‘-’: " . $tag);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->tagExpression;
    }
}
