%declare_class { class Parser }

%include {
/* ?><?php {//*/
}

%include_class {
    /**
     * @var string
     */
    protected $result;

    /**
     * Returns the result.
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Returns or_set expression.
     * @return string
     */
    static function exprOrSet($orSet)
    {
        $tmp = '';
        foreach (explode('|', $orSet) as $token) {
            $tmp .= $tmp ? ",'" . $token . "'::varchar" : "'" . $token . "'::varchar";
        }

        return "%s && ARRAY[" . $tmp . "]";
    }

    /**
     * Returns and_set expression.
     * @return string
     */
    static function exprAndSet($andSet)
    {
        $tmp = '';
        foreach (explode('&', $andSet) as $token) {
            $tmp .= $tmp ? ",'" . $token . "'::varchar" : "'" . $token . "'::varchar";
        }

        return "%s @> ARRAY[" . $tmp . "]";
    }

    /**
     * Returns not_clause expression.
     * @return string
     */
    static function exprNotClause($notClause)
    {
        return "(%s && ARRAY['" . $notClause . "'::varchar]) = false";
    }

    /**
     * Returns one_tag expression.
     * @return string
     */
    static function exprOneTag($oneTag)
    {
        return "%s && ARRAY['" . $oneTag . "'::varchar]";
    }
}

%syntax_error {
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Syntax Error: Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
}

%left T_TAG.
%left T_OR.
%left T_AND.
%left T_NOT.
%left T_LPAREN T_RPAREN.

program ::= expr(A). {
    $this->result = A;
}

program ::= one_tag(A). {
    $this->result = A;
}

expr(A) ::= expr(B) T_AND expr(C). {
    A = B . " AND " . C ;
}

expr(A) ::= expr(B) T_OR expr(C). {
    A = B . " OR " . C ;
}

expr(A) ::= expr(B) T_AND one_tag(C). {
    A = B . " AND " . C ;
}

expr(A) ::= expr(B) T_OR one_tag(C). {
    A = B . " OR " . C ;
}

expr(A) ::= T_TAG(B) T_OR T_LPAREN expr(C) T_RPAREN. {
    A = self::exprOrSet(B) . " OR " .C;
}

expr(A) ::= or_set(B) T_OR T_LPAREN expr(C) T_RPAREN. {
    A = self::exprOrSet(B) . " OR " .C;
}

expr(A) ::= or_set(B). {
     A = self::exprOrSet(B);
}

or_set(A) ::= or_set(B) T_OR T_TAG(C). {
    A = B . '|' . C;
}

or_set(A) ::= T_TAG(B) T_OR T_TAG(C). {
    A = B . '|' . C;
}

expr(A) ::= T_TAG(B) T_AND T_LPAREN expr(C) T_RPAREN. {
    A = self::exprAndSet(B) . " AND " .C;
}

expr(A) ::= and_set(B) T_AND T_LPAREN expr(C) T_RPAREN. {
    A = self::exprAndSet(B) . " AND " . C;
}

expr(A) ::= and_set(B). {
    A = self::exprAndSet(B);
}

and_set(A) ::= and_set(B) T_AND T_TAG(C). {
    A = B . '&' . C;
}

and_set(A) ::= T_TAG(B) T_AND T_TAG(C). {
    A = B . '&' . C;
}

expr(A) ::= T_TAG(B) T_OR not_clause(C). {
    A = self::exprOneTag(B) . " OR " . C;
}

expr(A) ::= T_TAG(B) T_AND not_clause(C). {
    A = self::exprOneTag(B) . " AND " . C;
}

expr(A) ::= or_set(B) T_OR not_clause(C). {
    A = self::exprOrSet(B) . " OR " . C;
}

expr(A) ::= and_set(B) T_AND not_clause(C). {
    A = self::exprAndSet(B) . " AND " . C;
}

expr(A) ::= not_clause(B). {
    A = B;
}

not_clause(A) ::= T_NOT T_TAG(B). {
    A = self::exprNotClause(B);
}

expr(A) ::= T_LPAREN expr(B) T_RPAREN. {
    A = '(' . B . ')';
}

one_tag(A) ::= T_TAG(B). {
    A = self::exprOneTag(B);
}


