<?php

namespace yii\gii\generators\model;

use yii\db\ColumnSchema;
use yii\helpers\Inflector;

class EnumGenerator
{
    private static $symbolsAbbrevation = [
        '!' => 'exclamation',
        '@' => 'at',
        '#' => 'number',
        '$' => 'dollar',
        '%' => 'percent',
        '^' => 'caret',
        '&' => 'and',
        '*' => 'asterisk',
        '(' => 'open_parenthesis',
        ')' => 'close_parenthesis',
        '=' => 'equals',
        '+' => 'plus',
        '{' => 'open_curly_brace',
        '}' => 'close_curly_brace',
        '[' => 'open_square_bracket',
        ']' => 'close_square_bracket',
        '|' => 'pipe',
        '\\' => 'backslash',
        '/' => 'forward_slash',
        ':' => 'colon',
        ';' => 'semicolon',
        '"' => 'double_quote',
        '\'' => 'single_quote',
        '<' => 'less_than',
        '>' => 'greater_than',
        ',' => 'comma',
        '.' => 'dot',
        '?' => 'question_mark',
        '~' => 'tilde',
        '`' => 'backtick'
    ];

    /**
     * @var string[]
     */
    private static $_symbolsAbbrevationList;

    /** @var ColumnSchema */
    private $column;


    /**
     * @var null|array
     */
    private $_constantList;

    private $_generator;

    /**
     * @param ColumnSchema[] $columns
     * @return EnumGenerator[]
     */
    public static function loadEnumColumns($generator, $columns)
    {
        $enumColumns = [];
        foreach ($columns as $column) {
            if (empty($column->enumValues)) {
                continue;
            }
            if (stripos($column->dbType, 'ENUM') !== 0) {
                continue;
            }
            $enumColumns[$column->name] = new self($generator, $column);
        }
        return $enumColumns;
    }

    public static function symbolsAbbrevationList()
    {
        if (self::$_symbolsAbbrevationList) {
            return self::$_symbolsAbbrevationList;
        }
        return self::$_symbolsAbbrevationList = array_map(static function ($item) {
            return ' ' . $item . ' ';
        }, self::$symbolsAbbrevation);
    }

    public function __construct($generator, $column)
    {
        $this->_generator = $generator;
        $this->column = $column;
    }

    public function enumConstantList()
    {
        if ($this->_constantList !== null) {
            return $this->_constantList;
        }
        $list = [];
        $constantEnumValues = [];
        foreach ($this->column->enumValues as $value) {
            $constantName = self::createConstantName($this->column->name, $value);
            if (in_array($constantName, $list, true)) {
                $this->_generator->addError('tableName', "Enum column '{$this->column->name}' has generated duplicate constant name '{$constantName}' for enum value '{$value}'.");
            }
            $list[$constantName] = [
                'constantName' => $constantName,
                'value' => $value,
            ];
            $constantEnumValues[$constantName][] = $value;
        }
        foreach ($constantEnumValues as $enumConstantName => $enumValues) {
            if (count($enumValues) === 1) {
                continue;
            }
            $values = implode("', '", $enumValues);
            $this
                ->_generator
                ->addError(
                    'tableName',
                    "Enum column '{$this->getColumnsName()}' has generated duplicate constant names '{$enumConstantName}' for enum values '{$values}'."
                );
        }
        return $this->_constantList = $list;
    }

    private static function createValueForName($value)
    {
        /**
         * Replaces all non-alphabetical symbols with their respective names.
         * Exceptions:
         * - "_" and " " - considered as separators
         * - "-" - treated as a special case and processed in the next statement
         */
        $value = strtr($value, self::symbolsAbbrevationList());

        /**
         * Replaces "-" with "minus":
         *  - if "-" is at the beginning of the string
         *  - if "space" precedes "-"
         *  - if "-" is at the end of the string
         * In all other cases, it is treated as a separator.
         */
        return preg_replace('#^-| -|-$#', ' minus ', $value);
    }

    /**
     * @return string
     */
    public function createRule()
    {
        return "['" . $this->column->name . "', 'in', 'range' => array_keys(self::" . $this->createOptsFunctionName() . '())]';
    }

    /**
     * @return string
     */
    public function createOptsFunctionName()
    {
        return 'opts' . $this->createColumnCamelName();
    }

    /**
     * @return string
     */
    public function createDisplayFunctionName()
    {
        return 'display' . $this->createColumnCamelName();
    }

    /**
     * @return string
     */
    public function createIsFunctionName($value)
    {
        return 'is'
            . $this->createColumnCamelName()
            . self::createValueForFunction($value);
    }

    /**
     * @return string
     */
    public function createSetFunctionName($value)
    {
        return 'set'
            . $this->createColumnCamelName()
            . 'To'
            . self::createValueForFunction($value);
    }

    /**
     * @param string $value
     * @return string
     */
    private static function createValueForFunction($value)
    {
        return Inflector::id2camel(Inflector::slug(self::createValueForName($value)));
    }

    private static function createConstantName($columnName, $value)
    {
        return strtoupper(Inflector::slug($columnName . ' ' . self::createValueForName($value), '_'));
    }

    /**
     * @return string
     */
    private function createColumnCamelName()
    {
        return Inflector::id2camel(Inflector::id2camel($this->column->name, '_'));
    }

    /**
     * @return string
     */
    public function getColumnsName()
    {
        return $this->column->name;
    }
}
