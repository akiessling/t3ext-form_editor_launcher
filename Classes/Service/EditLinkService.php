<?php declare(strict_types = 1);

namespace AndreasKiessling\FormEditorLauncher\Service;

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

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class EditLinkService implements \TYPO3\CMS\Core\SingletonInterface
{
    const TEMPLATE_PATH = 'EXT:form_editor_launcher/Resources/Private/Templates/EditorWizard.html';
    /**
     * @var \TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManagerInterface
     */
    protected $formPersistenceManager;

    /**
     * @var array
     */
    protected $storageFolders;

    public function __construct(\TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManagerInterface $formPersistenceManager)
    {
        $this->formPersistenceManager = $formPersistenceManager;
        $this->storageFolders = $this->formPersistenceManager->getAccessibleFormStorageFolders();
    }

    /**
     * @param string $formPath Path to the configured form yaml
     * @return string
     */
    public function getOnClickCode($formPath)
    {
        $typo3UriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $method = 'buildUriFromModule';

        if (\version_compare(\TYPO3_branch, '9.3', '>=')) {
            $method = 'buildUriFromRoute';
        }

        $editUri = $typo3UriBuilder->$method(
            'web_FormFormbuilder',
            [
                'tx_form_web_formformbuilder' => [
                    'formPersistenceIdentifier' => $formPath,
                    'action' => 'index',
                    'controller' => 'FormEditor',
                ],
            ]
        );

        return 'top.jump(' . GeneralUtility::quoteJSvalue(
            $editUri
        ) . ', \'web_FormFormbuilder\', \'web\'); return false;';
    }

    public function isEditable($formPath)
    {
        if (StringUtility::beginsWith($formPath, 'EXT:')) {
            return false;
        }

        $resourceFactory = ResourceFactory::getInstance();
        $file = $resourceFactory->retrieveFileOrFolderObject($formPath);

        if (!$file->checkActionPermission('write')) {
            return false;
        }

        if (!isset($GLOBALS['TBE_MODULES']['_configuration']['web_FormFormbuilder'])) {
            return false;
        }

        if (!$this->isInWritableMount($file)) {
            return false;
        }

        return $this->hasAccessToFormBuilder();
    }

    /**
     * Validates, if a file is in a configured form storage folder
     *
     * @param \TYPO3\CMS\Core\Resource\File $file
     * @return bool
     */
    public function isInWritableMount(File $file)
    {
        return \in_array($file->getParentFolder(), $this->storageFolders);
    }

    /**
     * @return bool
     */
    public function hasAccessToFormBuilder()
    {
        try {
            $GLOBALS['BE_USER']->modAccess($GLOBALS['TBE_MODULES']['_configuration']['web_FormFormbuilder']);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * Main entrypoint to render the edit link
     *
     * @param string $formPath
     * @return string
     */
    public function renderLink(string $formPath)
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            self::TEMPLATE_PATH
        );
        $view->assign('formPath', $formPath);

        $editable = false;

        if ($this->isEditable($formPath)) {
            $editable = true;
            $view->assign('onClick', $this->getOnClickCode($formPath));
        }

        $view->assign('isEditable', $editable);

        return $view->render();
    }
}
