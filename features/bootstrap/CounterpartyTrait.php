<?php
/**
 *
 */

use Accountancy\Features\CounterpartyManagement\EditCounterparty;
use Accountancy\Features\CounterpartyManagement\DeleteCounterparty;
use Accountancy\Features\CounterpartyManagement\CreateCounterparty;
use Accountancy\Entity\Counterparty;
use Accountancy\Entity\Currency;
use Accountancy\Entity\User;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

trait CounterpartyTrait
{
    /**
     * @Given /^I have Counterparties:$/
     */
    public function iHaveCounterparties(TableNode $counterpartiesTable)
    {
        foreach ($counterpartiesTable->getHash() as $row) {
            $counterparty = new Counterparty();

            if (isset($row['id'])) {
                $counterparty->setId($row['id']);
            }

            if (isset($row['name'])) {
                $counterparty->setName($row['name']);
            }

            $this->user->getCounterparties()->addCounterparty($counterparty);
        }
    }

    /**
     * @When /^I create Counterparty with Name "([^"]*)"$/
     */
    public function iCreateCounterpartyWithName($name)
    {
        $feature = new CreateCounterparty();
        $feature->setUser($this->user)
            ->setCounterpartyName($name);

        try {
            $feature->run();
        } catch(\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then /^my Counterparties should be:$/
     */
    public function myCounterpartiesShouldBe(TableNode $counterpartiesTable)
    {
        $counterpartiesByName = array();
        foreach($this->user->getCounterparties()->getCounterparties() as $counterparty) {
            $counterpartiesByName[$counterparty->getName()] = $counterparty;
        }

        foreach ($counterpartiesTable->getHash() as $row) {
            assertArrayHasKey("name", $row, "'name' field must be present in 'My Counterparties should be' table");
            assertArrayHasKey($row['name'], $counterpartiesByName, sprintf("Counterparty with name '%s' doesn't exist", $row['name']));
            $counterparty = $counterpartiesByName[$row['name']];


            if (isset($row['id'])) {
                assertEquals($row['id'], $counterparty->getId(), sprintf("Id does not match for counterparty '%s'", $row['name']));
            }

            if (isset($row['name'])) {
                assertEquals($row['name'], $counterparty->getName(), sprintf("Name does not match for counterparty '%s'", $row['name']));
            }
        }

        $expected = count($counterpartiesTable->getHash());
        $actual = count($counterpartiesByName);
        assertEquals($expected, $actual, sprintf("Expected %s counterparties, got %s", $expected, $actual));
    }

    /**
     * @When /^I delete Counterparty "([^"]*)"$/
     */
    public function iDeleteCounterparty($counterpartyId)
    {
        $feature = new DeleteCounterparty();
        $feature->setUser($this->user)
                ->setCounterpartyId($counterpartyId);

        try {
            $feature->run();
        } catch(\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When /^I edit Counterparty "([^"]*)", set name "([^"]*)"$/
     */
    public function iEditCounterpartySetName($counterpartyId, $name)
    {
        $feature = new EditCounterparty();
        $feature->setUser($this->user)
            ->setCounterpartyId($counterpartyId)
            ->setNewCounterpartyName($name);

        try {
            $feature->run();
        } catch(\Exception $e) {
            $this->lastException = $e;
        }
    }
}