<?php

use OLBot\Category\AbstractCategory;
use OLBot\Category\Translation;
use OLBot\Model\SubjectCandidate;
use OLBot\Service\StorageService;

include_once 'IntegrationSettingsMock.php';

class TranslationIntegrationTest extends \PHPUnit\Framework\TestCase
{
    function testWithNonsenseSecondSubjectCandidate()
    {
        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', __DIR__ . '/../..');
        }

        $storageService = new StorageService(new IntegrationSettingsMock());
        $storageService->subjectCandidates[] = new SubjectCandidate(SubjectCandidate::DELIMITER, ':', 'Bonjour');
        $storageService->subjectCandidates[] = new SubjectCandidate(SubjectCandidate::QUOTATION, '"', 'foo bar');
        AbstractCategory::setStorageService($storageService);

        $settingsArray = require PROJECT_ROOT . '/app/config/olbot_test.php';
        /** @var \OLBotSettings\Model\Settings $testSettings */
        $testSettings = \OLBotSettings\ObjectSerializer::deserialize(json_decode(json_encode($settingsArray)), 'OLBotSettings\Model\Settings');

        $translation = new Translation(
            1,
            0,
            $testSettings->getParser()->getCategories()[6],
            1
        );

        $translation->generateResponse();

        $this->assertEquals(
            "`fr-en`\nHello",
            $storageService->response->text[0]
        );
    }

}