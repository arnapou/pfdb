<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Condition;

use Arnapou\PFDB\Exception\Exception;

class ConditionBuilder
{
    /**
     *
     * @var AndCondition
     */
    protected $condition;

    /**
     * Instanciate a ConditionBuilder
     *
     * @param string $operator either 'AND' or 'OR'
     */
    public function __construct($operator)
    {
        if ('AND' == $operator) {
            $this->condition = new AndCondition();
        } elseif ('OR' == $operator) {
            $this->condition = new OrCondition();
        } else {
            Exception::throwInvalidRootOperatorException();
        }
    }

    /**
     * Create a ConditionBuilder which make AND operations between children
     *
     * @return ConditionBuilder
     */
    public static function AND()
    {
        return new self('AND');
    }

    /**
     * Create a ConditionBuilder which make OR operations between children
     *
     * @return ConditionBuilder
     */
    public static function OR()
    {
        return new self('OR');
    }

    /**
     * Get the final Condition object
     *
     * @return ConditionInterface
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Add a child condition
     *
     * @param ConditionInterface $condition
     * @return ConditionBuilder
     */
    public function add($condition)
    {
        if ($condition instanceof ConditionBuilder) {
            $this->condition->add($condition->getCondition());
        } elseif ($condition instanceof ConditionInterface) {
            $this->condition->add($condition);
        } else {
            Exception::throwBadArgumentTypeException('ConditionBuilder or ConditionInterface');
        }
        return $this;
    }

    /**
     * Add a greaterThan condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function greaterThan($field, $value, $caseSensitive = true)
    {
        $this->condition->add(new Operator\GreaterThanOperator($field, $value, $caseSensitive));
        return $this;
    }

    /**
     * Add a greaterThanOrEqual condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function greaterThanOrEqual($field, $value, $caseSensitive = true)
    {
        $this->condition->add(new Operator\GreaterThanOrEqualOperator($field, $value, $caseSensitive));
        return $this;
    }

    /**
     * Add a lowerThan condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function lowerThan($field, $value, $caseSensitive = true)
    {
        $this->condition->add(new Operator\LowerThanOperator($field, $value, $caseSensitive));
        return $this;
    }

    /**
     * Add a lowerThanOrEqual condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function lowerThanOrEqual($field, $value, $caseSensitive = true)
    {
        $this->condition->add(new Operator\LowerThanOrEqualOperator($field, $value, $caseSensitive));
        return $this;
    }

    /**
     * Add a equalTo condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function equalTo($field, $value, $caseSensitive = true)
    {
        $this->condition->add(new Operator\EqualOperator($field, $value, $caseSensitive));
        return $this;
    }

    /**
     * Add a notEqualTo condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function notEqualTo($field, $value, $caseSensitive = true)
    {
        $this->condition->add(new NotCondition(new Operator\EqualOperator($field, $value, $caseSensitive)));
        return $this;
    }

    /**
     * Add a in condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function in($field, $values, $caseSensitive = true)
    {
        $this->condition->add(new Operator\InOperator($field, $values, $caseSensitive));
        return $this;
    }

    /**
     * Add a notIn condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function notIn($field, $values, $caseSensitive = true)
    {
        $this->condition->add(new NotCondition(new Operator\InOperator($field, $values, $caseSensitive)));
        return $this;
    }

    /**
     * Add a matchRegExp condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function matchRegExp($field, $pattern, $caseSensitive = true)
    {
        $this->condition->add(new Operator\RegExpOperator($field, $pattern, $caseSensitive));
        return $this;
    }

    /**
     * Add a notMatchRegExp condition
     *
     * @param string $field         use NULL value if you want to condition on keys (not field rows)
     * @param mixed  $value
     * @param bool   $caseSensitive (default: true)
     * @return ConditionBuilder
     */
    public function notMatchRegExp($field, $pattern, $caseSensitive = true)
    {
        $this->condition->add(new NotCondition(new Operator\RegExpOperator($field, $pattern, $caseSensitive)));
        return $this;
    }
}
