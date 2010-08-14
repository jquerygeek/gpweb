<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Reflection;

/**
 * @uses       ReflectionFunction
 * @uses       \Zend\Reflection\ReflectionDocblockTag
 * @uses       \Zend\Reflection\Exception
 * @uses       \Zend\Reflection\ReflectionParameter
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReflectionFunction extends \ReflectionFunction
{
    /**
     * Get function docblock
     *
     * @param  string $reflectionClass Name of reflection class to use
     * @return Zend_Reflection_Docblock
     */
    public function getDocblock($reflectionClass = '\Zend\Reflection\ReflectionDocblock')
    {
        if ('' == ($comment = $this->getDocComment())) {
            throw new Exception($this->getName() . ' does not have a docblock');
        }
        $instance = new $reflectionClass($comment);
        if (!$instance instanceof ReflectionDocblock) {
            throw new Exception('Invalid reflection class provided; must extend Zend\Reflection\ReflectionDocblock');
        }
        return $instance;
    }

    /**
     * Get start line (position) of function
     *
     * @param  bool $includeDocComment
     * @return int
     */
    public function getStartLine($includeDocComment = false)
    {
        if ($includeDocComment) {
            if ($this->getDocComment() != '') {
                return $this->getDocblock()->getStartLine();
            }
        }

        return parent::getStartLine();
    }

    /**
     * Get contents of function
     *
     * @param  bool $includeDocblock
     * @return string
     */
    public function getContents($includeDocblock = true)
    {
        return implode("\n",
            array_splice(
                file($this->getFileName()),
                $this->getStartLine($includeDocblock),
                ($this->getEndLine() - $this->getStartLine()),
                true
                )
            );
    }

    /**
     * Get function parameters
     *
     * @param  string $reflectionClass Name of reflection class to use
     * @return array Array of \Zend\Reflection\ReflectionParameter
     */
    public function getParameters($reflectionClass = '\Zend\Reflection\ReflectionParameter')
    {
        $phpReflections  = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new $reflectionClass($this->getName(), $phpReflection->getName());
            if (!$instance instanceof ReflectionParameter) {
                throw new Exception('Invalid reflection class provided; must extend Zend_Reflection_Parameter');
            }
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

    /**
     * Get return type tag
     *
     * @return \Zend\Reflection\Docblock\Tag\Return
     */
    public function getReturn()
    {
        $docblock = $this->getDocblock();
        if (!$docblock->hasTag('return')) {
            throw new Exception('Function does not specify an @return annotation tag; cannot determine return type');
        }
        $tag    = $docblock->getTag('return');
        $return = ReflectionDocblockTag::factory('@return ' . $tag->getDescription());
        return $return;
    }
}
