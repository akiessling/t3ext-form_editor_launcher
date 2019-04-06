<?php


namespace AndreasKiessling\FormEditorLauncher\Hooks;


use AndreasKiessling\FormEditorLauncher\Service\EditLinkService;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class TtContentDrawItemHook implements PageLayoutViewDrawItemHookInterface
{
    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row)
    {
        if ($row['CType'] === 'form_formframework') {
            try {
                $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                $linkService = $objectManager->get(EditLinkService::class);

                $flexFormService = $objectManager->get(\TYPO3\CMS\Core\Service\FlexFormService::class);
                $flexData = $flexFormService->convertFlexFormContentToArray($row['pi_flexform']);
                $path = ArrayUtility::getValueByPath($flexData, 'settings/persistenceIdentifier');

                $itemContent .= $linkService->renderLink($path);
            } catch (\Throwable $e) {
                // fail silently
                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($e->getMessage());
            }
        }

    }

}