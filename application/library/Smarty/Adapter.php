<?php
/*确保Smarty.class.php在Smarty/libs/下*/
if (function_exists('saeAutoLoader')) {// 自动识别SAE环境
    Yaf_Loader::import("Smarty/sysplugins/smarty_internal_data.php");   /*基类目录为library*/
    Yaf_Loader::import("Smarty/sysplugins/smarty_internal_resource_file.php");   /*基类目录为library*/
    Yaf_Loader::import("Smarty/sysplugins/smarty_internal_template.php");   /*基类目录为library*/
    Yaf_Loader::import("Smarty/Smarty.class.php");   /*基类目录为library*/
} else {
    Yaf_Loader::import("Smarty/Local/libs/Smarty.class.php");   /*基类目录为library*/
}

class Smarty_Adapter implements Yaf_View_Interface
{
    /**
     * Smarty object
     * @var Smarty
     */
    public $_smarty;

    /**
     * Constructor
     *
     * @param string $tmplPath
     * @param array $extraParams
     * @throws Exception
     */
    public function __construct($tmplPath = null, $extraParams = array())
    {
        $this->_smarty = new Smarty;

        if (null !== $tmplPath) {
            $this->setScriptPath($tmplPath);
        }
        if (!is_dir($extraParams['compile_dir'])) {
            mkdir($extraParams['compile_dir']);
        }
        foreach ($extraParams as $key => $value) {
            $this->_smarty->$key = $value;
        }
    }

    /**
     * Return the template engine object
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }

    public function __get($key)
    {
        return $this->_smarty->getTemplateVars($key);
    }

    /**
     * Set the path to the templates
     *
     * @param string $path The directory to set as the path.
     * @throws Exception
     */
    public function setScriptPath($path)
    {
        if (is_readable($path)) {
            $this->_smarty->template_dir = $path;
            return;
        }

        throw new Exception('Invalid path provided');
    }

    /**
     * Retrieve the current template directory
     *
     * @return string
     */
    public function getScriptPath()
    {
        return $this->_smarty->template_dir;
    }

    /**
     * Alias for setScriptPath
     *
     * @param string $path
     * @throws Exception
     */
    public function setBasePath($path)
    {
        $this->setScriptPath($path);
    }

    /**
     * Alias for setScriptPath
     *
     * @param string $path
     * @throws Exception
     * @internal param string $prefix Unused
     */
    public function addBasePath($path)
    {
        $this->setScriptPath($path);
    }

    /**
     * Assign a variable to the template
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_smarty->assign($key, $val);
    }

    /**
     * Allows testing with empty() and isset() to work
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->_smarty->getTemplateVars($key));
    }

    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->_smarty->clearAssign($key);
    }

    /**
     * Assign variables to the template
     *
     * Allows setting a specific key to the specified value, OR passing
     * an array of key => value pairs to set en masse.
     *
     * @see __set()
     * @param string|array $spec The assignment strategy to use (key or
     * array of key => value pairs)
     * @param mixed $value (Optional) If assigning a named variable,
     * use this as the value.
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }

        $this->_smarty->assign($spec, $value);
    }

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via
     * {@link assign()} or property overloading
     * ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_smarty->clearAllAssign();
    }

    /**
     * Render a template and return the result.
     *
     * @link http://www.php.net/manual/en/yaf-view-interface.render.php
     *
     * @param string $tpl
     * @param array $tpl_vars
     * @return string
     */
    public function render($tpl, $tpl_vars = NULL)
    {
        return $this->_smarty->fetch($tpl);
    }

    /**
     * display a template
     *
     * @link http://www.php.net/manual/en/yaf-view-interface.display.php
     *
     * @param string $tpl
     * @param array $tpl_vars
     * @return string
     */
    public function display($tpl, $tpl_vars = NULL)
    {
        echo $this->_smarty->fetch($tpl);
    }
}