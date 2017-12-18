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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class IncrementViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize ViewHelper arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'value',
            'int',
            'The incoming value to increment, or NULL if VH children should be used',
            true
        );
        $this->registerArgument('by', 'int', 'The increment', false, 1);
    }

    /**
     * Render incremented value using the given increment (default increment is 1).
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     *
     * @return string The incremented value
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $value = $arguments['value'];

        if (!is_numeric($value)) {
            $value = 0;
        }

        $increment = $arguments['by'];

        if (!is_numeric($increment)) {
            $increment = 0;
        }

        return $value + $increment;
    }
}
