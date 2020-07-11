<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 2:46.
 */

namespace App\Service\PhpCsFixer;

class RuleSetDefinition
{
    /**
     * @var array
     */
    public static $rules = [
        'align_multiline_comment' => [
            'Multi-line comments must have an asterisk and must be aligned with the first one',
            'Проверяет, что не съехали звездочки в многосточных комментариях',
        ],
        'array_indentation' => [
            'Each element of an array must be indented exactly once',
            'Выравниваем отступы в массивах',
        ],
        'array_syntax'                                => [
            'PHP arrays should be declared using short syntax',
            'Используем только короткий синтаксис объявления массивов `[]`',
        ],
        'blank_line_after_namespace'                  => [
            'There MUST be one blank line after the namespace declaration',
            'Проверяет, что после объявления неймспейса ровно одна пустая строка',
        ],
        'binary_operator_spaces'                      => [
            'Binary operators should be surrounded by space as configured',
            'Выравнивание операторов',
        ],
        'braces'                                      => [
            'Braces: allow single line closure, don`t next position after functions and oop constructs',
            'Открывающая фигурная скобка быть помещена в «следующую» или «ту же» строку после классных конструкций (неанонимные классы, интерфейсы, признаки, методы и не-лямбда-функции)',
        ],
        'cast_spaces'                                 => [
            'A single space should be between cast and variable',
            'Правило проверяет, что между оператором приведения типа и переменной, ровно 1 пробел',
        ],
        'concat_space'                                => [
            'Concatenation should be surrounded by single spaces',
            'Правило проверяет, что оператор конкатенации обрамлен в двух сторон по одному пробелу',
        ],
        'encoding'                                    => [
            'PHP code MUST use only UTF-8 without BOM (remove BOM)',
            'Удаляет (BOM) из файлов',
        ],
        'full_opening_tag'                            => [
            'PHP code must use the long <?php tags or short-echo <?= tags and not other tag variations',
            'Заменяет <? на <?php',
        ],
        'function_declaration'                        => [
            'Spaces should be properly placed in a function declaration',
            'Проверяет корректноть расстановки пробелов при объявлении функций',
        ],
        'function_typehint_space'                     => [
            'Add missing space between function\'s argument and its typehint',
            'Добавляет пропущенный пробел между аргументом функции и его typehint',
        ],
        'general_phpdoc_annotation_remove'            => [
            'Configured annotations should be omitted from PHPDoc',
            'Удаляет перечисленные ниже тэги из аннотаций',
        ],
        'indentation_type'                            => [
            'Code must use configured indentation type (4 spaces)',
            'В качестве отступов используем только 4 пробела',
        ],
        'list_syntax'                                 => [
            'List (array destructuring) assignment should be declared using short syntax',
            'Проверяет, что оператор list Использует короткий синтаксис',
        ],
        'lowercase_cast'                              => [
            'Cast should be written in lower case',
            'Проверяет, что приведение типов написано в нижнем регистре',
        ],
        'lowercase_constants'                         => [
            'The PHP constants true, false, and null MUST be in lower case',
            'Проверяет, что true, false, and null написаны в нижнем регистре',
        ],
        'lowercase_keywords'                          => [
            'PHP keywords MUST be in lower case',
            'Зарезервированные слова должны быть написаны в нижнем регистре',
        ],
        'method_argument_space'                       => [
            'Ensure function arguments are placed correctly',
            'Проверяет корректность написания аргументов функции, при объявлении и вызове',
        ],
        'method_chaining_indentation'                 => [
            'Method chaining MUST be properly indented',
            'Выравнимаем отступы, при использовании цепочек вызовов',
        ],
        'multiline_whitespace_before_semicolons'      => [
            'Forbid multi-line whitespace before the closing semicolon',
            'Проверяет, что `;` находится на той же строке, что и оператор',
        ],
        'new_with_braces'                             => [
            'All instances created with new keyword must be followed by braces',
            'Приверяет, что при создании объекта с использованием оператора `new` используются скобочки `()`',
        ],
        'no_closing_tag'                              => [
            'The closing `?>` tag MUST be omitted from files containing only PHP',
            'Php файлы которые содержат только код, не должны использовать закрывающий php тэг',
        ],
        'no_empty_statement'                          => [
            'Remove useless semicolon statements',
            'Избавляемся от ненужных `;`',
        ],
        'no_extra_blank_lines'                        => [
            'Remove useless empty lines',
            'Избавляемся от ненужных пустых линий',
        ],
        'no_homoglyph_names'                          => [
            'Replace accidental usage of homoglyphs (russian or other non ascii characters) in names',
            'Убирает случайное использование русских букв в именах',
        ],
        'no_leading_import_slash'                     => [
            'Remove leading slashes in use clauses',
            'Удаляем лидируюший слеш при импорте',
        ],
        'no_multiline_whitespace_around_double_arrow' => [
            'Operator => should not be surrounded by multi-line whitespaces',
            'Проверяет, что нет переносов строк вокруг оператора `=>`',
        ],
        'no_null_property_initialization'             => [
            'Properties MUST not be explicitly initialized with null',
            'Проверяет, что свойства классов и объектов не инициализируются значением `null`',
        ],
        'no_spaces_after_function_name'               => [
            'There MUST NOT be a space between the method or function name and the opening parenthesis',
            'Проверяет, что между названием метода и открывающейся скобкой нет пробелов',
        ],
        'no_spaces_inside_parenthesis'                => [
            'There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis',
            'Проверяет, что при вызове функций нет пробела после открывающейся скобки, и перед закрывающейся',
        ],
        'no_superfluous_elseif'                       => [
            'Replaces superfluous elseif with if.',
            'Заменяет elseif на if, если это не меняет поведение кода',
        ],
        'no_trailing_whitespace'                      => [
            'Remove trailing whitespace at the end of non-blank lines',
            'Удаляем лишние пробелы в конце не пустых строк',
        ],
        'no_unused_imports'                           => [
            'Unused use statements must be removed',
            'Удаляем неиспользуемые импорты',
        ],
        'no_useless_else'                             => [
            'There should not be useless else cases',
            'Удаляем else, если это не меняет поведение кода',
        ],
        'no_whitespace_in_blank_line'                 => [
            'Remove trailing whitespace at the end of blank lines',
            'Проверяет, что на пустых строках нет пробелов',
        ],
        'non_printable_character'                     => [
            'Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols',
            'Удаляет невидимые символы',
        ],
        'ordered_imports'                             => [
            'Ordering use statements',
            'Выравниваем импорты по алфавиту',
        ],
        'phpdoc_add_missing_param_annotation'         => [
            'PHPDoc should contain @param for all params',
            'Добавляем тэг @param для параметров, если его нет',
        ],
        'phpdoc_align'                                => [
            'All items of the given PHPDoc tags must be aligned vertically',
            'Выравниваем тэги и их значения в doc блоках',
        ],
        'phpdoc_single_line_var_spacing'              => [
            'Single line @var PHPDoc should have proper spacing',
            'Проверяет корректность расстановки пробелов в односточных doc блоках для переменных (@var)',
        ],
        'phpdoc_trim'                                 => [
            'PHPDoc should start and end with content, excluding the very first and last line of the docblocks',
            'Удаляет пустые строки в начале и конце php-doc блока',
        ],
        'phpdoc_types_order'                          => [
            'null in @param PHPDoc must be last',
            'Проверяет, что есть в аннотации @param есть null, то он будет последним',
        ],
        'return_type_declaration'                     => [
            'There should be no space before colon, and one space after it in return type declarations',
            'Проверяет расстановку пробелов, при использовании return type declaration',
        ],
        'single_blank_line_at_eof'                    => [
            'A PHP file without end tag must always end with a single empty line feed',
            'Проверяет, что в конце файла ровно одна пустая строка',
        ],
        'single_line_after_imports'                   => [
            'Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block',
            'Проверяет, что после импортов ровно одна пустая строка',
        ],
        'single_line_comment_style'                   => [
            'Single-line comments and multi-line comments with only one line of actual content should use the // syntax',
            'Проверяет, что однострочныее комментарии используют синтаксис комментирования `//`',
        ],
        'switch_case_space'                           => [
            'Removes extra spaces between colon and case value',
            'Проверяет расстановку пробелов, при использовании операторов case и default',
        ],
        'ternary_operator_spaces'                     => [
            'Spaces around ternary operator should be placed correctly',
            'Проверяет расстановку пробелов, при использовании тернарного оператора',
        ],
        'ternary_to_null_coalescing'                  => [
            'Use null coalescing operator ?? where possible',
            'Меняет тернарный оператор на Null Coalesce Operator, где это возможно',
        ],
        'trailing_comma_in_multiline_array'           => [
            'PHP multi-line arrays should have a trailing comma',
            'Убедимся, что последний элемент многострочеого массива имеет запятую в конце',
        ],
        'trim_array_spaces'                           => [
            'Arrays should be formatted like function/method arguments, without leading or trailing single line space',
            'Проверяет, что при объявлении массива нет пробела после открывающейся скобки, и перед закрывающейся',
        ],
        'whitespace_after_comma_in_array'             => [
            'In array declaration, there MUST be a whitespace after each comma',
            'Проверяет, что при объявлении массива есть пробел после каждой запятой',
        ],
        'blank_line_after_opening_tag'                => [
            'Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line',
            'Проверяет наличие пустой строки после <?php',
        ],
    ];

    public static function getRuleDescription(string $ruleName, bool $inRussian = true): string
    {
        if (!isset(self::$rules[$ruleName]) || empty(self::$rules[$ruleName])) {
            return $ruleName;
        }
        if ($inRussian && isset(self::$rules[$ruleName][1])) {
            return self::$rules[$ruleName][1];
        }

        return self::$rules[$ruleName][0];
    }
}
