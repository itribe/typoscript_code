<?php

namespace Itribe\TyposcriptCode\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Itribe\TyposcriptCode\TypoScript\TypoScriptContentFactory;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * TyposcriptCode Content Controller
 *
 * @author     Anton Danilov, interactive tribe GmbH
 * @package    TyposcriptCode
 * @subpackage Controller
 */
class ContentController extends ActionController
{
    /**
     * __construct
     */
    public function __construct(protected readonly TypoScriptContentFactory $typoScriptContentFactory)
    {}

    /**
     * Render content
     */
    public function indexAction(): ResponseInterface
    {
        $contentObject = $this->request->getAttribute('currentContentObject');
        $setupAst = $this->typoScriptContentFactory->createSettingsAndSetupConditions($this->request, $contentObject);
        return $this->htmlResponse($contentObject->cObjGet($setupAst->toArray(), 'typoscript_code_proc.'));
    }
}