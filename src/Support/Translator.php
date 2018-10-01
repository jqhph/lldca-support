<?php

namespace Swoft\Support;

use Swoft\App;
use Swoft\Bean\Annotation\Value;
use Swoft\Helper\ArrayHelper;

class Translator
{
    /**
     * @var $this
     */
    protected static $instance;

    /**
     * Source languages
     *
     * @var string
     * @Value(name="${config.translator.languageDir}", env="${TRANSLATOR_LANG_DIR}")
     */
    public $languageDir = '@resources/languages/';

    /**
     * Translation messages
     *
     * @var array
     */
    private $messages = [];

    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @Value(name="${config.translator.defaultCategory}", env="${TRANSLATOR_DEFAULT_CATEGORY}")
     * @var string
     */
    private $defaultCategory = 'default';

    /**
     * @Value(name="${config.translator.defaultLanguage}", env="${TRANSLATOR_DEFAULT_LANG}")
     * @var string
     */
    private $defaultLanguage = 'zh-CN';

    public function __construct()
    {
        $this->languageDir = env('TRANSLATOR_LANG_DIR', config('translator.languageDir', $this->languageDir));
        $this->defaultCategory = env('TRANSLATOR_DEFAULT_CATEGORY', config('translator.defaultCategory', $this->defaultCategory));
        $this->defaultLanguage = env('TRANSLATOR_DEFAULT_LANG', config('translator.defaultLanguage', $this->defaultLanguage));

        $this->init();
    }

    /**
     * @return $this
     */
    public static function make()
    {
        return self::$instance ?: (self::$instance = new static());
    }

    /**
     * @return void
     * @throws \RuntimeException
     */
    public function init()
    {
        $sourcePath = App::getAlias($this->languageDir);
        if (! $sourcePath || ! file_exists($sourcePath)) {
            return;
        }
        if (! is_readable($sourcePath)) {
            throw new \RuntimeException(sprintf('%s dir is not readable', $sourcePath));
        }
        $this->loadLanguages($sourcePath);
    }

    /**
     * 获取当前语言包
     *
     * @return string
     */
    public function current()
    {
        return $this->defaultLanguage;
    }

    /**
     * 动态设置语言包
     *
     * @param string $lang
     * @return $this
     */
    public function setLang(string $lang)
    {
        $this->defaultLanguage = $lang;

        return $this;
    }

    /**
     * 获取当前语言包路径
     *
     * @return string
     */
    public function currentPath()
    {
        return rtrim(App::getAlias($this->languageDir), '/').'/'.$this->defaultLanguage;
    }

    /**
     * @param string $sourcePath
     * @return void
     */
    protected function loadLanguages(string $sourcePath)
    {
        if ($this->loaded === false) {
            $iterator = new \RecursiveDirectoryIterator($sourcePath);
            $files = new \RecursiveIteratorIterator($iterator);
            foreach ($files as $file) {
                // Only load php file
                // TODO add .mo .po support
                if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                    continue;
                }
                $messages = str_replace([$sourcePath, '.php'], '', $file);
                list($language, $category) = explode('/', $messages);
                $this->messages[$language][$category] = require $file;
            }
            $this->loaded = true;
        }
    }

    /**
     * 翻译
     * 如果指定的翻译文件中没有该翻译,则会读取默认的翻译文件翻译
     *
     * @param string $content 要翻译的字段或语句
     * @param string $type "category.key" or "locale.category.key" （文件名/文件名.子类别）
     * @param array  $params
     * @param string $locale
     * @return mixed
     */
    public function translate($content, string $type = '', array $params = [], string $locale = null): string
    {
        $realKey = $this->getRealCategory($type, $locale);

        $message = ArrayHelper::get($this->messages, $realKey);
        if (!is_array($message)) {
            return $params ? $this->formatMessage($content, $params) : $content;
        }
        if (!isset($message[$content]) || $message[$content] === '') {
            if ($this->hasDefaultFile($realKey, $locale)) {
                return $content;
            }
            return $this->translateWithDefaultFile($content, $type, $params, $locale);
        }
        if (is_array($message[$content])) {
            return $message[$content];
        }

        return $params ? $this->formatMessage($message[$content], $params) : $message[$content];
    }

    /**
     * 使用默认翻译文件翻译
     *
     * @param string $content 要翻译的字段或语句
     * @param string $type "category.key" or "locale.category.key" （文件名/文件名.子类别）
     * @param array  $params
     * @param string $locale
     * @return mixed
     */
    protected function translateWithDefaultFile(&$content, string &$type = '', array &$params = [], string $locale = null): string
    {
        $type = explode('.', $type);
        $type[0] = $this->defaultCategory;

        return $this->translate($content, implode('.', $type), $params, $locale);
    }

    /**
     * 检查是否是使用了默认翻译文件
     *
     * @param string $realKey
     * @param string $lang
     * @return bool
     */
    protected function hasDefaultFile(string $realKey, $lang)
    {
        $lang = $lang ?: $this->defaultLanguage;

        if (strpos(str_replace($lang.'.', '', $realKey), $this->defaultCategory) === 0) {
            return true;
        }
        return false;
    }

    /**
     * Make the place-holder replacements on a line.
     *
     * @param  string  $line
     * @param  array   $replace
     * @return string
     */
    protected function makeReplacements($line, array &$replace)
    {
        if (empty($replace)) {
            return $line;
        }

        $replace = $this->sortReplacements($replace);

        foreach ($replace as $key => &$value) {
            $line = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );
        }

        return $line;
    }

    /**
     * Sort the replacements array.
     *
     * @param  array  $replace
     * @return array
     */
    protected function sortReplacements(array &$replace)
    {
        return (new Collection($replace))->sortBy(function ($value, $key) {
            return mb_strlen($key) * -1;
        })->all();
    }

    /**
     * @param string      $type
     * @param string|null $locale
     *
     * @return string
     */
    private function getRealCategory(string $type, string $locale = null): string
    {
        if ($locale === null) {
            $locale = $this->defaultLanguage;
        }
        if (!$type) {
            $type = $this->defaultCategory;
        }

        return implode('.', [$locale, str__slug($type)]);
    }

    /**
     * Format message
     *
     * @param string $message
     * @param array  $params
     * @return string
     */
    private function formatMessage(string $message, array &$params): string
    {
        $new = array_values($params);
        array_unshift($new, $message);
        return $this->makeReplacements(sprintf(...$new), $params);
    }
}
