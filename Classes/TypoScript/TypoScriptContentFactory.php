<?php

namespace Itribe\TyposcriptCode\TypoScript;

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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\TypoScript\AST\Node\RootNode;
use TYPO3\CMS\Core\TypoScript\IncludeTree\IncludeNode\RootInclude;
use TYPO3\CMS\Core\TypoScript\IncludeTree\IncludeNode\SegmentInclude;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateRepository;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateTreeBuilder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Traverser\ConditionVerdictAwareIncludeTreeTraverser;
use TYPO3\CMS\Core\TypoScript\IncludeTree\TreeFromLineStreamBuilder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Visitor\IncludeTreeAstBuilderVisitor;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Visitor\IncludeTreeConditionMatcherVisitor;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Visitor\IncludeTreeSetupConditionConstantSubstitutionVisitor;
use TYPO3\CMS\Core\TypoScript\Tokenizer\LossyTokenizer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class TypoScriptContentFactory
{
    /**
     * __construct
     */
    public function __construct(
        private readonly TreeFromLineStreamBuilder $treeFromTokenStreamBuilder,
        private readonly SysTemplateRepository $sysTemplateRepository,
        private readonly SysTemplateTreeBuilder $treeBuilder,
    ) {}
    
    public function createSettingsAndSetupConditions(ServerRequestInterface $request, ContentObjectRenderer $contentObject): RootNode
    {
        $data = $contentObject->data ?? [];
        $typoScript = $request->getAttribute('frontend.typoscript');
        $conditionMatcherVariables = $this->prepareConditionMatcherVariables($request);
        $rootNode = new RootInclude();
        $tokenizer = new LossyTokenizer();
        $includeNode = new SegmentInclude();
        $includeTreeTraverserConditionVerdictAware = new ConditionVerdictAwareIncludeTreeTraverser();
        $name = '[typoscript_code:' . $data['uid'] . '] ' . $data['header'];
        $includeNode->setName($name);
        $includeNode->setPid((int)$data['pid']);
        $includeNode->setLineStream($tokenizer->tokenize($data['bodytext'] ?? ''));
        $this->treeFromTokenStreamBuilder->buildTree($includeNode, 'other', $tokenizer);
        $rootNode->addChild($includeNode);

        $includeTreeTraverserConditionVerdictAwareVisitors = [];
        $setupConditionConstantSubstitutionVisitor = new IncludeTreeSetupConditionConstantSubstitutionVisitor();
        $setupConditionConstantSubstitutionVisitor->setFlattenedConstants($typoScript->getFlatSettings());
        $includeTreeTraverserConditionVerdictAwareVisitors[] = $setupConditionConstantSubstitutionVisitor;
        $setupMatcherVisitor = GeneralUtility::makeInstance(IncludeTreeConditionMatcherVisitor::class);
        $setupMatcherVisitor->initializeExpressionMatcherWithVariables($conditionMatcherVariables);
        $includeTreeTraverserConditionVerdictAwareVisitors[] = $setupMatcherVisitor;
        $setupAstBuilderVisitor = GeneralUtility::makeInstance(IncludeTreeAstBuilderVisitor::class);
        $setupAstBuilderVisitor->setFlatConstants($typoScript->getFlatSettings());
        $includeTreeTraverserConditionVerdictAwareVisitors[] = $setupAstBuilderVisitor;
        $includeTreeTraverserConditionVerdictAware->traverse($rootNode, $includeTreeTraverserConditionVerdictAwareVisitors);
        if (!empty($setupMatcherVisitor->getConditionListWithVerdicts())) {
            $contentObject->convertToUserIntObject();
        }
        $setupIncludeTree = $this->getSetupIncludeTree((int)$data['pid'], $request, $tokenizer);
        $setupIncludeTree->addChild($rootNode);
        $includeTreeTraverserConditionVerdictAware->traverse($setupIncludeTree, $includeTreeTraverserConditionVerdictAwareVisitors);
        return $setupAstBuilderVisitor->getAst();
    }

    /**
     * Returns the setup include tree for the given page.
     */
    private function getSetupIncludeTree(int $pageId, ServerRequestInterface $request, LossyTokenizer $tokenizer): RootInclude
    {
        $rootLine = GeneralUtility::makeInstance(RootlineUtility::class, $pageId)->get();
        $sysTemplateRows = $this->sysTemplateRepository->getSysTemplateRowsByRootline($rootLine, $request);
        /** @var SiteInterface|null $site */
        $site = $request->getAttribute('site');
        return $this->treeBuilder->getTreeBySysTemplateRowsAndSite('setup', $sysTemplateRows, $tokenizer, $site);
    }

    /**
     * Data available in TypoScript "condition" matching.
     */
    private function prepareConditionMatcherVariables(ServerRequestInterface $request): array
    {
        $tsfe = $request->getAttribute('frontend.controller');
        $pageInformation = $request->getAttribute('frontend.page.information');
        $topDownRootLine = $pageInformation?->getRootLine() ?? $tsfe?->rootLine ?? [];
        $localRootline = $pageInformation?->getLocalRootLine() ?? $tsfe?->tmpl->rootLine ?? [];
        ksort($topDownRootLine);
        return [
            'request' => $request,
            'pageId' => $pageInformation?->getId() ?? $tsfe?->id ?? 0,
            'page' => $pageInformation?->getPageRecord() ?? $tsfe?->page ?? [],
            'fullRootLine' => $topDownRootLine,
            'localRootLine' => $localRootline,
            'site' => $request->getAttribute('site'),
            'siteLanguage' => $request->getAttribute('language'),
            'tsfe' => $request->getAttribute('frontend.controller'),
        ];
    }
}