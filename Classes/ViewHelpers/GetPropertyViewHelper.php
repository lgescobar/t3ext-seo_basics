<?php
namespace B13\SeoBasics\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 KO-Web | Kai Ole Hartwig <mail@ko-web.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class GetPropertyViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Output should not be escaped to be able to get arrays and objects as output.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize ViewHelper arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'mixed', 'The object to be selected or de-referenced with the path', true);
        $this->registerArgument('path', 'string', 'The path', false, '');
    }

    /**
     * Get the value of the selected subject trying to de-reference it using the given path. If the subject is a string,
     * first get the value of the referenced variable before the de-referencing operation.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     *
     * @return mixed Value of the selected property of the given object
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $subject = $arguments['subject'];

        if (is_string($subject) && strlen($subject) > 0) {
            $value = $renderingContext->getVariableProvider()->get($subject);

            if (strlen($arguments['path']) > 0) {
                $value = ObjectAccess::getPropertyPath($value, $arguments['path']);
            }

            return $value;
        } else {
            return ObjectAccess::getPropertyPath($subject, $arguments['path']);
        }
    }
}
