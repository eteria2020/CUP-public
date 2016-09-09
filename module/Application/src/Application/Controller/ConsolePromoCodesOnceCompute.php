<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use SharengoCore\Service\SimpleLoggerService as Logger;

class ConsolePromoCodesOnceCompute extends AbstractActionController {

    /**
     * @var $EntityManager
     */
    private $entityManager;

    /**
     * @var $Logger
     */
    private $logger;

    /**
     * @param EntityManager $entityManager
     * @param Logger $logger
     */
    public function __construct(
    EntityManager $entityManager, Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function InsertNewPromocodeAction() {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $promocodesInfoId = intval($request->getParam('promocodesInfoId'));
        $qty = intval($request->getParam('qty'));
        $this->logger->log("\nInsertNewPromocodeAction promocodesInfoId=" . $promocodesInfoId . " qty=" . $qty . "\n");
        for ($i = 0; $i < $qty; $i++) {
            $this->logger->log($i . " " . $this->GetPromocode(). "\n");
        }
    }

    private function GetPromocode() {
        return $this->RandomString(4) . "-" .
            $this->RandomString(4) . "-" .
            $this->RandomString(4) . "-" .
            $this->RandomString(4);
    }

    private function RandomString($length = 10) {
        //$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
