<?php

namespace Application\Functional;

use Application\Bootstrap;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ValidateCustomerRegistrationTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $applicationConfig = $serviceManager->get('ApplicationConfig');

        $this->setApplicationConfig($applicationConfig);
        parent::setUp();
    }

    public function testRegisterValidUserWithOnlyEquomobiliData()
    {
        //$databaseMock = \Mockery::mock('Doctrine\ORM\EntityManager');
        $this->getApplicationServiceLocator();

        /*$data = [];

        $this->dispatch('/signup', 'POST', $data);*/
    }
}
